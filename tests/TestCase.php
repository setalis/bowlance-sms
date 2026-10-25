<?php

namespace Tests;

use App\Enums\OrderMode;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public bool $openCatalogByDefault = true;

    public bool $inferOrderModeFromDeliveryType = true;

    public bool $keepOrderModeWindow = true;

    /**
     * @param  array<string, mixed>  $parameters
     * @param  array<string, mixed>  $cookies
     * @param  array<string, mixed>  $files
     * @param  array<string, mixed>  $server
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $path = parse_url((string) $uri, PHP_URL_PATH) ?? (string) $uri;
        $path = '/'.ltrim($path, '/');
        if ($path === '/') {
            $path = '/';
        }

        $followCatalogRedirect = false;

        if ($this->openCatalogByDefault && strtoupper($method) === 'GET' && $path === '/') {
            if (! session()->has(OrderMode::SESSION_KEY)) {
                $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value]);
            }

            $followCatalogRedirect = true;
        }

        if ($this->openCatalogByDefault && strtoupper($method) === 'GET' && $path === '/menu' && ! session()->has(OrderMode::SESSION_KEY)) {
            $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value]);
        }

        if ($this->inferOrderModeFromDeliveryType && strtoupper($method) === 'POST' && $path === '/orders' && ! session()->has(OrderMode::SESSION_KEY)) {
            $payload = $parameters;

            if ($payload === [] && is_string($content) && $content !== '') {
                $decoded = json_decode($content, true);
                $payload = is_array($decoded) ? $decoded : [];
            }

            $deliveryType = $payload['delivery_type'] ?? 'delivery';
            $mode = in_array($deliveryType, ['pickup', 'dine_in'], true)
                ? OrderMode::Onsite
                : OrderMode::Delivery;

            $this->withSession([OrderMode::SESSION_KEY => $mode->value]);
        }

        if ($this->keepOrderModeWindow && session()->has(OrderMode::SESSION_KEY) && ! array_key_exists(OrderMode::WINDOW_COOKIE, $cookies)) {
            $cookies[OrderMode::WINDOW_COOKIE] = encrypt(
                CookieValuePrefix::create(OrderMode::WINDOW_COOKIE, app('encrypter')->getKey()).'1',
                false,
            );
        }

        if ($followCatalogRedirect) {
            $this->followRedirects = true;
        }

        try {
            return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
        } finally {
            $this->followRedirects = false;
        }
    }
}
