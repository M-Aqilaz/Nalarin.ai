<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentFulfillment
{
    public function complete(Payment $payment, array $gatewayPayload = []): Payment
    {
        return DB::transaction(function () use ($payment, $gatewayPayload): Payment {
            $lockedPayment = Payment::query()
                ->with('user')
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->isCompleted()) {
                return $lockedPayment;
            }

            $user = $lockedPayment->user;
            $planStartsAt = $user->plan === 'premium' && $user->plan_expires_at?->isFuture()
                ? $user->plan_expires_at->copy()
                : now();
            $planEndsAt = $planStartsAt->copy()->addDays($lockedPayment->duration_days);
            $paidAt = $this->completedAt($gatewayPayload) ?? now();

            $lockedPayment->update([
                'status' => Payment::STATUS_COMPLETED,
                'payment_method' => data_get($gatewayPayload, 'detail.transaction.payment_method')
                    ?? data_get($gatewayPayload, 'webhook.payment_method'),
                'paid_at' => $paidAt,
                'plan_starts_at' => $planStartsAt,
                'plan_ends_at' => $planEndsAt,
                'gateway_payload' => $gatewayPayload,
            ]);

            $user->update([
                'plan' => $lockedPayment->plan,
                'plan_expires_at' => $planEndsAt,
                'room_limit' => $lockedPayment->room_limit,
                'match_credits' => $lockedPayment->match_credits,
            ]);

            return $lockedPayment->refresh();
        });
    }

    private function completedAt(array $gatewayPayload): ?Carbon
    {
        $completedAt = data_get($gatewayPayload, 'detail.transaction.completed_at')
            ?? data_get($gatewayPayload, 'webhook.completed_at');

        if (! $completedAt) {
            return null;
        }

        try {
            return Carbon::parse($completedAt);
        } catch (\Throwable) {
            return null;
        }
    }
}
