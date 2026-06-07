<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\PakasirPaymentVerifier;
use App\Services\Payments\PaymentFulfillment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PakasirWebhookController extends Controller
{
    public function store(
        Request $request,
        PakasirPaymentVerifier $verifier,
        PaymentFulfillment $fulfillment
    ): JsonResponse {
        $payload = $request->validate([
            'amount' => ['required', 'integer'],
            'order_id' => ['required', 'string'],
            'project' => ['required', 'string'],
            'status' => ['required', 'string'],
            'payment_method' => ['nullable', 'string'],
            'completed_at' => ['nullable', 'string'],
        ]);

        $payment = Payment::query()
            ->where('order_id', $payload['order_id'])
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment tidak ditemukan.'], 404);
        }

        try {
            $verifier->validateWebhookPayload($payment, $payload);

            if ($payload['status'] !== Payment::STATUS_COMPLETED) {
                return response()->json(['message' => 'Webhook diterima, pembayaran belum completed.']);
            }

            $gatewayPayload = $verifier->verifiedCompletedPayload($payment, $payload);

            if (! $gatewayPayload) {
                return response()->json(['message' => 'Webhook diterima, transaksi Pakasir belum completed.']);
            }

            $fulfillment->complete($payment, $gatewayPayload);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (\Throwable) {
            return response()->json(['message' => 'Gagal memverifikasi transaksi Pakasir.'], 502);
        }

        return response()->json(['message' => 'Pembayaran berhasil diproses.']);
    }
}
