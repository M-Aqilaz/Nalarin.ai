<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PakasirClient
{
    public function isConfigured(): bool
    {
        return filled($this->slug()) && filled($this->apiKey());
    }

    public function paymentUrl(Payment $payment): string
    {
        if (! filled($this->slug())) {
            throw new RuntimeException('Pakasir slug belum dikonfigurasi.');
        }

        $query = [
            'order_id' => $payment->order_id,
            'redirect' => route('billing.return', ['payment' => $payment->order_id], absolute: true),
        ];

        if ((bool) config('services.pakasir.qris_only', false)) {
            $query['qris_only'] = 1;
        }

        return $this->baseUrl()
            .'/pay/'.rawurlencode($this->slug()).'/'.$payment->amount
            .'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    public function transactionDetail(Payment $payment): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Konfigurasi Pakasir belum lengkap.');
        }

        return Http::acceptJson()
            ->timeout((int) config('services.pakasir.timeout', 15))
            ->get($this->baseUrl().'/api/transactiondetail', [
                'project' => $this->slug(),
                'amount' => $payment->amount,
                'order_id' => $payment->order_id,
                'api_key' => $this->apiKey(),
            ])
            ->throw()
            ->json();
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.pakasir.base_url', 'https://app.pakasir.com'), '/');
    }

    private function slug(): ?string
    {
        return config('services.pakasir.slug');
    }

    private function apiKey(): ?string
    {
        return config('services.pakasir.api_key');
    }
}
