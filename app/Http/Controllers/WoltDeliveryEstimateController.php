<?php

namespace App\Http\Controllers;

use App\Services\WoltDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WoltDeliveryEstimateController extends Controller
{
    public function __construct(
        protected WoltDriveService $woltDrive
    ) {}

    /**
     * Проверка доступности доставки Wolt по адресу и ориентировочная цена/время (venueful).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $address = $request->input('address', '');
        $address = is_string($address) ? trim($address) : '';

        if ($address === '' && $request->filled('delivery_city') && $request->filled('delivery_street')) {
            $address = trim(implode(', ', array_filter([
                $request->delivery_city,
                trim($request->delivery_street.($request->filled('delivery_house') ? ' '.$request->delivery_house : '')),
            ])));
        }

        if ($address === '') {
            return response()->json([
                'success' => false,
                'available' => false,
                'message' => 'Укажите город, улицу и дом',
            ], 422);
        }

        if (! $this->woltDrive->isEnabled() || config('wolt.drive.mode') !== 'venueful') {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'Расчёт доставки временно недоступен',
            ]);
        }

        $venueId = config('wolt.drive.venue_id');
        if (blank($venueId)) {
            return response()->json([
                'success' => true,
                'available' => false,
            ]);
        }

        $minPrep = (int) config('wolt.drive.min_preparation_time_minutes', 15);
        $promise = $this->woltDrive->getShipmentPromise($venueId, $address, $minPrep);

        if ($promise === null) {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'Не удалось проверить адрес. Оформите заказ — возможность доставки подтвердится при оформлении.',
                'message_short' => 'Проверка недоступна',
            ]);
        }

        $isBinding = (bool) Arr::get($promise, 'is_binding');
        $price = Arr::get($promise, 'price', []);
        $amountCents = (int) Arr::get($price, 'amount', 0);
        $currency = Arr::get($price, 'currency', 'GEL');
        $etaMinutes = Arr::get($promise, 'dropoff.eta_minutes') ?? Arr::get($promise, 'time_estimate_minutes');

        return response()->json([
            'success' => true,
            'available' => $isBinding,
            'fee' => [
                'amount' => $amountCents / 100,
                'amount_minor' => $amountCents,
                'currency' => $currency,
            ],
            'eta_minutes' => $etaMinutes ? (int) $etaMinutes : null,
            'message' => $isBinding ? null : 'Уточните адрес для точного расчёта',
        ]);
    }
}
