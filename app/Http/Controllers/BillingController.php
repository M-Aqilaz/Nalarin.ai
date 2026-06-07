<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\PakasirClient;
use App\Services\Payments\PakasirPaymentVerifier;
use App\Services\Payments\PaymentFulfillment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $payments = $request->user()
            ->payments()
            ->latest()
            ->paginate(10);

        return view('pages.user.billing.index', compact('payments'));
    }

    public function checkout(Request $request, string $plan, PakasirClient $pakasir): RedirectResponse
    {
        $planConfig = $this->planConfig($plan);

        if (! $planConfig) {
            abort(404);
        }

        if (! $pakasir->isConfigured()) {
            return back()->withErrors([
                'billing' => 'Konfigurasi Pakasir belum lengkap. Isi PAKASIR_SLUG dan PAKASIR_API_KEY di environment.',
            ]);
        }

        $payment = $request->user()->payments()->create([
            'gateway' => 'pakasir',
            'order_id' => $this->uniqueOrderId(),
            'plan_key' => $plan,
            'plan_name' => $planConfig['name'],
            'plan' => $planConfig['plan'],
            'amount' => $planConfig['amount'],
            'currency' => 'IDR',
            'duration_days' => $planConfig['duration_days'],
            'room_limit' => $planConfig['room_limit'],
            'match_credits' => $planConfig['match_credits'],
            'status' => Payment::STATUS_PENDING,
        ]);

        return redirect()->away($pakasir->paymentUrl($payment));
    }

    public function return(
        Request $request,
        Payment $payment,
        PakasirPaymentVerifier $verifier,
        PaymentFulfillment $fulfillment
    ): RedirectResponse {
        abort_unless($payment->user_id === $request->user()->id, 403);

        if (! $payment->isCompleted()) {
            try {
                $gatewayPayload = $verifier->verifiedCompletedPayload($payment);

                if ($gatewayPayload) {
                    $payment = $fulfillment->complete($payment, $gatewayPayload);
                }
            } catch (\Throwable) {
                return redirect()
                    ->route('billing.index')
                    ->with('billing_status', 'Pembayaran belum bisa diverifikasi otomatis. Status akan diperbarui lewat webhook Pakasir.');
            }
        }

        if ($payment->isCompleted()) {
            return redirect()
                ->route('profile.edit')
                ->with('billing_status', 'Pembayaran berhasil. Paket premium sudah aktif.');
        }

        return redirect()
            ->route('billing.index')
            ->with('billing_status', 'Pembayaran masih pending. Jika sudah membayar, tunggu webhook Pakasir memperbarui status.');
    }

    private function planConfig(string $plan): ?array
    {
        $config = config("services.pakasir.plans.{$plan}");

        return is_array($config) ? $config : null;
    }

    private function uniqueOrderId(): string
    {
        do {
            $orderId = Payment::makeOrderId();
        } while (Payment::query()->where('order_id', $orderId)->exists());

        return $orderId;
    }
}
