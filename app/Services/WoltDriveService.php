<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WoltDriveService
{
    public function isEnabled(): bool
    {
        if (! (bool) config('wolt.drive.enabled') || blank(config('wolt.drive.token'))) {
            return false;
        }

        $mode = config('wolt.drive.mode', 'venueful');
        if ($mode === 'venueful') {
            return filled(config('wolt.drive.venue_id'));
        }

        return filled(config('wolt.drive.merchant_id'));
    }

    public function createDeliveryForOrder(Order $order): ?array
    {
        if (! $this->isEnabled() || $order->delivery_type->value !== 'delivery') {
            return null;
        }

        $hasAddress = filled($order->delivery_address)
            || (filled($order->delivery_city) && filled($order->delivery_street));
        if (! $hasAddress || blank($order->customer_phone)) {
            return null;
        }

        $mode = config('wolt.drive.mode', 'venueful');
        $data = $mode === 'venueful'
            ? $this->createVenuefulDelivery($order)
            : $this->createVenuelessDelivery($order);

        if ($data !== null) {
            $this->updateOrderFromWoltResponse($order, $data);
        }

        return $data;
    }

    protected function createVenuefulDelivery(Order $order): ?array
    {
        $venueId = config('wolt.drive.venue_id');
        $requestContext = $this->getShipmentPromiseRequestContext($order);
        $promise = $this->requestShipmentPromiseWithPayload($order, $venueId, $requestContext['payload']);
        if ($promise === null || ! Arr::get($promise, 'is_binding')) {
            Log::warning('Wolt Drive shipment promise missing or non-binding', [
                'order_id' => $order->id,
                'promise' => $promise,
                'is_binding' => $promise !== null ? Arr::get($promise, 'is_binding') : null,
                'request_street' => $requestContext['payload']['street'] ?? null,
                'request_city' => $requestContext['payload']['city'] ?? null,
                'address_source' => $requestContext['address_source'],
            ]);

            return null;
        }

        $promiseId = Arr::get($promise, 'id');
        $payload = $this->buildVenuefulDeliveryPayload($order, $promise);
        $path = "/v1/venues/{$venueId}/deliveries";
        $this->logWoltRequest('POST', $path, $payload);
        $response = $this->request()->post($path, $payload);
        $this->logWoltResponse($path, $response->status(), $response->body(), $response->successful());

        if (! $response->successful()) {
            Log::warning('Wolt Drive create venueful delivery failed', [
                'order_id' => $order->id,
                'shipment_promise_id' => $promiseId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * Parse delivery address into street + city for Wolt API (binding promise requires street + city).
     * Preferred format: "city, street". With config wolt.drive.known_cities, also accepts "street, city"
     * when the second part matches a known city.
     * See https://developer.wolt.com/docs/wolt-drive/endpoints
     *
     * @return array{street: string, city: string}
     */
    protected function parseAddressForWolt(string $address): array
    {
        $address = trim($address);
        $defaultCity = (string) config('wolt.drive.default_delivery_city', 'Batumi');
        $knownCities = config('wolt.drive.known_cities', []);

        if (str_contains($address, ',')) {
            $parts = array_map('trim', explode(',', $address, 2));
            $first = $parts[0] ?? '';
            $second = $parts[1] ?? '';

            if ($first !== '' && $second !== '') {
                $firstIsCity = in_array($first, $knownCities, true);
                $secondIsCity = in_array($second, $knownCities, true);
                if ($secondIsCity && ! $firstIsCity) {
                    return [
                        'street' => $first,
                        'city' => $second,
                    ];
                }
                if ($firstIsCity && ! $secondIsCity) {
                    return [
                        'street' => $second,
                        'city' => $first,
                    ];
                }
            }

            return [
                'street' => $second !== '' ? $second : $address,
                'city' => $first !== '' ? $first : $defaultCity,
            ];
        }

        return [
            'street' => $address,
            'city' => $defaultCity,
        ];
    }

    /**
     * Build flat request body for POST /v1/venues/{venueId}/shipment-promises.
     * API expects top-level street, city, min_preparation_time_minutes (no dropoff wrapper).
     */
    protected function buildShipmentPromisePayload(string $deliveryAddress, int $minPrepMinutes, ?array $parcels = null): array
    {
        $parsed = $this->parseAddressForWolt($deliveryAddress);
        $payload = [
            'street' => $parsed['street'],
            'city' => $parsed['city'],
            'min_preparation_time_minutes' => $minPrepMinutes,
        ];
        if ($parcels !== null && $parcels !== []) {
            $payload['parcels'] = $parcels;
        }

        return $payload;
    }

    /**
     * Build shipment promise request payload and address source for an order (for logging).
     *
     * @return array{payload: array<string, mixed>, address_source: 'explicit'|'fallback'}
     */
    protected function getShipmentPromiseRequestContext(Order $order): array
    {
        $minPrep = (int) config('wolt.drive.min_preparation_time_minutes', 15);
        $parcels = $this->buildParcelsForEstimate($order);

        if (filled($order->delivery_city) && filled($order->delivery_street)) {
            $street = trim($order->delivery_street.($order->delivery_house ? ' '.$order->delivery_house : ''));
            $payload = [
                'street' => $street,
                'city' => $order->delivery_city,
                'min_preparation_time_minutes' => $minPrep,
            ];
            if ($parcels !== []) {
                $payload['parcels'] = $parcels;
            }

            return ['payload' => $payload, 'address_source' => 'explicit'];
        }

        $deliveryAddress = $order->delivery_address ?? '';
        Log::warning('Wolt Drive using fallback address parsing (delivery_address only)', [
            'order_id' => $order->id,
            'delivery_address' => $deliveryAddress,
        ]);
        $payload = $this->buildShipmentPromisePayload(
            $deliveryAddress,
            $minPrep,
            $parcels !== [] ? $parcels : null
        );

        return ['payload' => $payload, 'address_source' => 'fallback'];
    }

    protected function requestShipmentPromiseWithPayload(Order $order, string $venueId, array $payload): ?array
    {
        $path = "/v1/venues/{$venueId}/shipment-promises";
        $this->logWoltRequest('POST', $path, $payload);
        $response = $this->request()->post($path, $payload);
        $this->logWoltResponse($path, $response->status(), $response->body(), $response->successful());

        if (! $response->successful()) {
            Log::warning('Wolt Drive shipment promise failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    protected function buildVenuefulDeliveryPayload(Order $order, array $promise): array
    {
        $dropoff = Arr::get($promise, 'dropoff', []);
        $dropoffLocation = Arr::get($dropoff, 'location', []);
        $currency = config('wolt.drive.currency', 'GEL');
        $priceAmount = (int) round((float) ($order->delivery_fee ?: 0) * 100);
        if ($priceAmount <= 0 && Arr::has($promise, 'price.amount')) {
            $priceAmount = (int) Arr::get($promise, 'price.amount');
            $currency = Arr::get($promise, 'price.currency', $currency);
        }

        $payload = [
            'shipment_promise_id' => Arr::get($promise, 'id'),
            'merchant_order_reference_id' => $order->order_number,
            'order_number' => $this->shortOrderNumber($order->order_number),
            'recipient' => [
                'name' => $order->customer_name,
                'phone_number' => $order->receiver_phone ?: $order->customer_phone,
                'email' => $order->customer_email ?? '',
            ],
            'dropoff' => [
                'location' => $dropoffLocation,
                'comment' => $this->formatDropoffComment($order),
            ],
            'pickup' => [
                'options' => [
                    'min_preparation_time_minutes' => (int) config('wolt.drive.min_preparation_time_minutes', 15),
                ],
            ],
            'parcels' => $this->buildVenuefulParcels($order),
            'customer_support' => $this->customerSupportPayload(),
        ];

        if ($priceAmount > 0) {
            $payload['price'] = [
                'amount' => $priceAmount,
                'currency' => $currency,
            ];
        }

        $payload['tips'] = [
            [
                'type' => 'pre_delivery_courier_tip',
                'price' => [
                    'amount' => 0,
                    'currency' => $currency,
                ],
            ],
        ];

        return $payload;
    }

    protected function createVenuelessDelivery(Order $order): ?array
    {
        $merchantId = config('wolt.drive.merchant_id');
        $payload = $this->buildVenuelessDeliveryPayload($order);
        $path = "/merchants/{$merchantId}/delivery-order";
        $this->logWoltRequest('POST', $path, $payload);
        $response = $this->request()->post($path, $payload);
        $this->logWoltResponse($path, $response->status(), $response->body(), $response->successful());

        if (! $response->successful()) {
            Log::warning('Wolt Drive create venueless delivery failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    protected function buildVenuelessDeliveryPayload(Order $order): array
    {
        $currency = config('wolt.drive.currency', 'GEL');
        $priceAmount = (int) round((float) ($order->delivery_fee ?: 0) * 100);
        if ($priceAmount <= 0) {
            $priceAmount = 100; // Wolt may require minimum; use 1 unit if no fee
        }

        return [
            'merchant_order_reference_id' => $order->order_number,
            'order_number' => $this->shortOrderNumber($order->order_number),
            'pickup' => [
                'location' => [
                    'formatted_address' => config('wolt.drive.pickup.address'),
                    'coordinates' => [
                        'lat' => (float) config('wolt.drive.pickup.lat'),
                        'lon' => (float) config('wolt.drive.pickup.lng'),
                    ],
                ],
                'contact_details' => [
                    'name' => config('wolt.drive.pickup.name'),
                    'phone_number' => config('wolt.drive.pickup.phone'),
                    'send_tracking_link_sms' => false,
                ],
                'display_name' => config('wolt.drive.pickup.name'),
                'comment' => '',
            ],
            'dropoff' => [
                'location' => [
                    'formatted_address' => $order->delivery_address,
                ],
                'contact_details' => [
                    'name' => $order->customer_name,
                    'phone_number' => $order->receiver_phone ?: $order->customer_phone,
                    'send_tracking_link_sms' => true,
                ],
                'comment' => $this->formatDropoffComment($order),
            ],
            'min_preparation_time_minutes' => (int) config('wolt.drive.min_preparation_time_minutes', 15),
            'contents' => $this->buildVenuelessContents($order),
            'price' => [
                'amount' => $priceAmount,
                'currency' => $currency,
            ],
            'customer_support' => $this->customerSupportPayload(),
            'is_no_contact' => (bool) $order->leave_at_door,
            'tips' => [
                [
                    'type' => 'pre_delivery_courier_tip',
                    'price' => [
                        'amount' => 0,
                        'currency' => $currency,
                    ],
                ],
            ],
        ];
    }

    protected function buildParcelsForEstimate(Order $order): array
    {
        $currency = config('wolt.drive.currency', 'GEL');
        $count = max(1, $order->total_items);

        return [
            [
                'count' => $count,
                'price' => [
                    'amount' => (int) round((float) $order->subtotal * 100),
                    'currency' => $currency,
                ],
                'dimensions' => [
                    'weight_gram' => 500,
                    'width_cm' => 20,
                    'height_cm' => 20,
                    'depth_cm' => 20,
                ],
            ],
        ];
    }

    protected function buildVenuefulParcels(Order $order): array
    {
        $currency = config('wolt.drive.currency', 'GEL');
        $items = $order->items;
        if ($items->isEmpty()) {
            return [
                [
                    'count' => 1,
                    'description' => 'Order '.$order->order_number,
                    'identifier' => $order->order_number,
                    'price' => [
                        'amount' => (int) round((float) $order->subtotal * 100),
                        'currency' => $currency,
                    ],
                    'dimensions' => [
                        'weight_gram' => 500,
                        'width_cm' => 20,
                        'height_cm' => 20,
                        'depth_cm' => 20,
                    ],
                ],
            ];
        }

        return $items->map(fn ($item) => [
            'count' => $item->quantity,
            'description' => $item->name,
            'identifier' => $order->order_number.'-'.$item->id,
            'price' => [
                'amount' => (int) round((float) $item->subtotal * 100),
                'currency' => $currency,
            ],
            'dimensions' => [
                'weight_gram' => 300,
                'width_cm' => 15,
                'height_cm' => 15,
                'depth_cm' => 15,
            ],
        ])->values()->all();
    }

    protected function buildVenuelessContents(Order $order): array
    {
        $currency = config('wolt.drive.currency', 'GEL');
        $items = $order->items;
        if ($items->isEmpty()) {
            return [
                [
                    'count' => 1,
                    'description' => 'Order '.$order->order_number,
                    'identifier' => $order->order_number,
                    'price' => [
                        'amount' => (int) round((float) $order->subtotal * 100),
                        'currency' => $currency,
                    ],
                    'dimensions' => [
                        'weight_gram' => 500,
                        'width_cm' => 20,
                        'height_cm' => 20,
                        'depth_cm' => 20,
                    ],
                ],
            ];
        }

        return $items->map(fn ($item) => [
            'count' => $item->quantity,
            'description' => $item->name,
            'identifier' => $order->order_number.'-'.$item->id,
            'price' => [
                'amount' => (int) round((float) $item->subtotal * 100),
                'currency' => $currency,
            ],
            'dimensions' => [
                'weight_gram' => 300,
                'width_cm' => 15,
                'height_cm' => 15,
                'depth_cm' => 15,
            ],
        ])->values()->all();
    }

    protected function formatDropoffComment(Order $order): string
    {
        $parts = array_filter([
            $order->entrance ? 'Подъезд: '.$order->entrance : null,
            $order->floor ? 'Этаж: '.$order->floor : null,
            $order->apartment ? 'Кв.: '.$order->apartment : null,
            $order->intercom ? 'Домофон: '.$order->intercom : null,
            $order->courier_comment,
        ]);

        return implode('. ', $parts);
    }

    protected function customerSupportPayload(): array
    {
        $support = config('wolt.drive.customer_support', []);
        $payload = [];
        if (! empty($support['email'])) {
            $payload['email'] = $support['email'];
        }
        if (! empty($support['phone_number'])) {
            $payload['phone_number'] = $support['phone_number'];
        }
        if (! empty($support['url'])) {
            $payload['url'] = $support['url'];
        }

        return $payload ?: ['email' => config('mail.from.address', '')];
    }

    protected function shortOrderNumber(string $orderNumber): string
    {
        $short = preg_replace('/[^A-Za-z0-9]/', '', $orderNumber);

        return strlen($short) > 5 ? substr($short, -5) : $short;
    }

    protected function updateOrderFromWoltResponse(Order $order, array $data): void
    {
        $order->update([
            'wolt_delivery_id' => Arr::get($data, 'id') ?? Arr::get($data, 'wolt_order_reference_id'),
            'wolt_status' => Arr::get($data, 'status'),
            'wolt_tracking_url' => Arr::get($data, 'tracking.url') ?? Arr::get($data, 'tracking_url'),
            'wolt_last_payload' => $data,
        ]);
    }

    public function refreshDeliveryStatus(Order $order): ?array
    {
        if (! $this->isEnabled() || blank($order->wolt_delivery_id)) {
            return null;
        }

        $path = config('wolt.drive.mode') === 'venueful' && filled(config('wolt.drive.venue_id'))
            ? '/v1/venues/'.config('wolt.drive.venue_id').'/deliveries/'.$order->wolt_delivery_id
            : '/v1/deliveries/'.$order->wolt_delivery_id;

        $this->logWoltRequest('GET', $path, null);
        $response = $this->request()->get($path);
        $this->logWoltResponse($path, $response->status(), $response->body(), $response->successful());

        if (! $response->successful()) {
            Log::warning('Wolt Drive fetch delivery failed', [
                'order_id' => $order->id,
                'wolt_delivery_id' => $order->wolt_delivery_id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $data = $response->json();
        $order->update([
            'wolt_status' => Arr::get($data, 'status'),
            'wolt_tracking_url' => Arr::get($data, 'tracking.url') ?? $order->wolt_tracking_url,
            'wolt_last_payload' => $data,
        ]);

        return $data;
    }

    /**
     * Check delivery availability and get price/ETA for an address (venueful).
     * Useful before checkout to show delivery fee and time.
     */
    public function getShipmentPromise(string $venueId, string $deliveryAddress, int $minPrepMinutes = 15, ?Order $order = null): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $parcels = null;
        if ($order !== null) {
            $parcels = $this->buildParcelsForEstimate($order);
            if ($parcels === []) {
                $parcels = null;
            }
        }
        $payload = $this->buildShipmentPromisePayload($deliveryAddress, $minPrepMinutes, $parcels);
        $path = "/v1/venues/{$venueId}/shipment-promises";
        $this->logWoltRequest('POST', $path, $payload);
        $response = $this->request()->post($path, $payload);
        $this->logWoltResponse($path, $response->status(), $response->body(), $response->successful());

        if (! $response->successful()) {
            Log::warning('Wolt Drive shipment promise failed', [
                'venue_id' => $venueId,
                'address_length' => strlen($deliveryAddress),
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('wolt.drive.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->withToken((string) config('wolt.drive.token'));
    }

    protected function logWoltRequest(string $method, string $path, ?array $payload = null): void
    {
        Log::info('[WoltDrive] Request', [
            'method' => $method,
            'path' => $path,
            'payload' => $payload,
        ]);
    }

    protected function logWoltResponse(string $path, int $status, string $body, bool $ok): void
    {
        $level = $ok ? 'info' : 'warning';
        $decoded = json_decode($body, true);
        Log::log($level, '[WoltDrive] Response', [
            'path' => $path,
            'status' => $status,
            'ok' => $ok,
            'body_raw' => strlen($body) > 2000 ? substr($body, 0, 2000).'...' : $body,
            'body' => $decoded !== null ? $decoded : $body,
        ]);
    }
}
