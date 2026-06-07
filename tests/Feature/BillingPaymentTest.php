<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\PakasirClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BillingPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_pakasir_checkout(): void
    {
        config([
            'services.pakasir.base_url' => 'https://app.pakasir.com',
            'services.pakasir.slug' => 'nalarin',
            'services.pakasir.api_key' => 'secret',
            'services.pakasir.qris_only' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('billing.checkout', 'pro_monthly'));

        $response->assertRedirect();

        $location = $response->headers->get('Location');
        $this->assertStringContainsString('https://app.pakasir.com/pay/nalarin/49000?', $location);
        $this->assertStringContainsString('order_id=', $location);
        $this->assertStringContainsString('qris_only=1', $location);

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'gateway' => 'pakasir',
            'plan_key' => 'pro_monthly',
            'amount' => 49000,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function test_pakasir_webhook_completes_payment_and_activates_premium(): void
    {
        config([
            'services.pakasir.slug' => 'nalarin',
            'services.pakasir.api_key' => 'secret',
        ]);

        $user = User::factory()->create([
            'plan' => 'free',
            'room_limit' => 2,
            'match_credits' => 3,
        ]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'pakasir',
            'order_id' => 'NLR202606050001',
            'plan_key' => 'pro_monthly',
            'plan_name' => 'Pro',
            'plan' => 'premium',
            'amount' => 49000,
            'currency' => 'IDR',
            'duration_days' => 30,
            'room_limit' => 10,
            'match_credits' => 99,
            'status' => Payment::STATUS_PENDING,
        ]);

        $pakasir = Mockery::mock(PakasirClient::class);
        $pakasir->shouldReceive('transactionDetail')
            ->once()
            ->with(Mockery::on(fn (Payment $argument) => $argument->id === $payment->id))
            ->andReturn([
                'transaction' => [
                    'amount' => 49000,
                    'order_id' => 'NLR202606050001',
                    'project' => 'nalarin',
                    'status' => 'completed',
                    'payment_method' => 'qris',
                    'completed_at' => '2026-06-05T10:15:00+07:00',
                ],
            ]);
        $this->app->instance(PakasirClient::class, $pakasir);

        $response = $this->postJson(route('webhooks.pakasir'), [
            'amount' => 49000,
            'order_id' => 'NLR202606050001',
            'project' => 'nalarin',
            'status' => 'completed',
            'payment_method' => 'qris',
            'completed_at' => '2026-06-05T10:15:00+07:00',
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Pembayaran berhasil diproses.']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_COMPLETED,
            'payment_method' => 'qris',
        ]);

        $user->refresh();

        $this->assertTrue($user->isPremium());
        $this->assertSame('premium', $user->plan);
        $this->assertSame(10, $user->room_limit);
        $this->assertSame(99, $user->match_credits);
        $this->assertNotNull($user->plan_expires_at);
    }

    public function test_pakasir_webhook_rejects_wrong_amount(): void
    {
        config([
            'services.pakasir.slug' => 'nalarin',
            'services.pakasir.api_key' => 'secret',
        ]);

        $payment = Payment::create([
            'user_id' => User::factory()->create()->id,
            'gateway' => 'pakasir',
            'order_id' => 'NLR202606050002',
            'plan_key' => 'pro_monthly',
            'plan_name' => 'Pro',
            'plan' => 'premium',
            'amount' => 49000,
            'currency' => 'IDR',
            'duration_days' => 30,
            'room_limit' => 10,
            'match_credits' => 99,
            'status' => Payment::STATUS_PENDING,
        ]);

        $response = $this->postJson(route('webhooks.pakasir'), [
            'amount' => 50000,
            'order_id' => $payment->order_id,
            'project' => 'nalarin',
            'status' => 'completed',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_PENDING,
        ]);
    }
}
