<?php

namespace App\Services\Payments;

use App\Models\Payment;
use RuntimeException;

class PakasirPaymentVerifier
{
    public function __construct(private PakasirClient $client)
    {
    }

    public function validateWebhookPayload(Payment $payment, array $payload): void
    {
        if ((string) ($payload['order_id'] ?? '') !== $payment->order_id) {
            throw new RuntimeException('Order ID pembayaran tidak sesuai.');
        }

        if ((int) ($payload['amount'] ?? 0) !== $payment->amount) {
            throw new RuntimeException('Nominal pembayaran tidak sesuai.');
        }

        $configuredProject = config('services.pakasir.slug');
        $payloadProject = (string) ($payload['project'] ?? '');

        if (filled($configuredProject) && $payloadProject !== '' && $payloadProject !== $configuredProject) {
            throw new RuntimeException('Project Pakasir tidak sesuai.');
        }
    }

    public function verifiedCompletedPayload(Payment $payment, array $webhookPayload = []): ?array
    {
        $detail = $this->client->transactionDetail($payment);
        $transaction = (array) data_get($detail, 'transaction', []);

        $this->validateGatewayTransaction($payment, $transaction);

        if (($transaction['status'] ?? null) !== Payment::STATUS_COMPLETED) {
            return null;
        }

        return [
            'webhook' => $webhookPayload,
            'detail' => $detail,
        ];
    }

    private function validateGatewayTransaction(Payment $payment, array $transaction): void
    {
        if ((string) ($transaction['order_id'] ?? '') !== $payment->order_id) {
            throw new RuntimeException('Order ID hasil verifikasi tidak sesuai.');
        }

        if ((int) ($transaction['amount'] ?? 0) !== $payment->amount) {
            throw new RuntimeException('Nominal hasil verifikasi tidak sesuai.');
        }

        $configuredProject = config('services.pakasir.slug');
        $transactionProject = (string) ($transaction['project'] ?? '');

        if (filled($configuredProject) && $transactionProject !== '' && $transactionProject !== $configuredProject) {
            throw new RuntimeException('Project hasil verifikasi tidak sesuai.');
        }
    }
}
