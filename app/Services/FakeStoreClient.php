<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;


class FakeStoreClient
{
    protected string $baseUrl;
    protected int $timeout;
    protected int $retries;
    protected int $retrySleepMs;


    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.fakestore.base_url', env('FAKESTORE_BASE_URL', 'https://fakestoreapi.com')), '/');
        $this->timeout = (int) env('FAKESTORE_TIMEOUT', 10);
        $this->retries = (int) env('FAKESTORE_RETRIES', 3);
        $this->retrySleepMs = (int) env('FAKESTORE_RETRY_SLEEP_MS', 400);
    }


    protected function client()
    {
        return Http::timeout($this->timeout)
            ->retry($this->retries, $this->retrySleepMs);
    }


    public function getProducts(): array
    {
        try {
            $resp = $this->client()->get($this->baseUrl . '/products');
            if ($resp->failed()) {
                Log::warning('fakestore_products_failed', ['status' => $resp->status()]);
                throw new \RuntimeException('Failed to fetch products');
            }
            return $resp->json();
        } catch (Throwable $e) {
            Log::error('fakestore_products_error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    public function getCategories(): array
    {
        try {
            $resp = $this->client()->get($this->baseUrl . '/products/categories');
            if ($resp->failed()) {
                Log::warning('fakestore_categories_failed', ['status' => $resp->status()]);
                throw new \RuntimeException('Failed to fetch categories');
            }
            return $resp->json();
        } catch (Throwable $e) {
            Log::error('fakestore_categories_error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
