<?php

namespace Tests\Feature;

use App\Models\AiRequest;
use App\Models\FeatureEvent;
use App\Models\Material;
use App\Models\QuizAttempt;
use App\Models\QuizSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_tracking_creates_event_and_updates_aggregate(): void
    {
        $this->postJson(route('feature.track'), [
            'feature_name' => 'Smart Flashcard',
        ])->assertOk()
            ->assertJson([
                'success' => true,
                'click_count' => 1,
            ]);

        $this->assertDatabaseHas('feature_events', [
            'feature_key' => 'smart_flashcard',
            'feature_name' => 'Smart Flashcard',
            'action' => 'click',
        ]);

        $this->assertDatabaseHas('feature_usages', [
            'feature_name' => 'Smart Flashcard',
            'click_count' => 1,
        ]);
    }

    public function test_admin_analytics_pages_read_real_events_requests_and_attempts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $material = Material::factory()->for($user)->create(['status' => 'processed']);
        $quiz = QuizSet::create([
            'material_id' => $material->id,
            'title' => 'Latihan Kuis: Biologi',
            'description' => 'Kuis test',
            'question_count' => 2,
        ]);

        FeatureEvent::create([
            'user_id' => $user->id,
            'feature_key' => 'smart_flashcard',
            'feature_name' => 'Smart Flashcard',
            'action' => 'generated',
            'occurred_at' => now(),
        ]);

        AiRequest::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'feature' => 'summary',
            'provider' => 'openrouter',
            'model' => 'test-model',
            'status' => AiRequest::STATUS_SUCCESS,
            'started_at' => now()->subSeconds(2),
            'completed_at' => now(),
            'latency_ms' => 1500,
        ]);
        AiRequest::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'feature' => 'chat',
            'provider' => 'openrouter',
            'model' => 'test-model',
            'status' => AiRequest::STATUS_FAILED,
            'started_at' => now()->subSecond(),
            'completed_at' => now(),
            'latency_ms' => 900,
            'error_message' => 'Provider timeout',
        ]);

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_set_id' => $quiz->id,
            'material_id' => $material->id,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'score' => 1,
            'total_questions' => 2,
            'percentage' => 50,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['range' => '7d']))
            ->assertOk()
            ->assertSee('Smart Flashcard');

        $this->actingAs($admin)
            ->get(route('admin.monitoring-ai', ['range' => '7d']))
            ->assertOk()
            ->assertSee('1.5s')
            ->assertSee('Error AI');

        $this->actingAs($admin)
            ->get(route('admin.statistik-pembelajaran', ['range' => '7d']))
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('Smart Flashcard');
    }

    public function test_quiz_attempt_is_persisted_when_user_answers_questions(): void
    {
        $user = User::factory()->create();
        $material = Material::factory()->for($user)->create(['status' => 'processed']);
        $quiz = QuizSet::create([
            'material_id' => $material->id,
            'title' => 'Latihan Kuis: Fotosintesis',
            'description' => 'Kuis test',
            'question_count' => 2,
        ]);
        $questions = $quiz->questions()->createMany([
            [
                'prompt' => 'Apa fungsi klorofil?',
                'choices' => ['Menyerap cahaya', 'Menyimpan air', 'Menguatkan akar', 'Menghasilkan tanah'],
                'correct_choice' => 0,
                'explanation' => 'Klorofil membantu menyerap cahaya.',
                'sort_order' => 1,
            ],
            [
                'prompt' => 'Gas apa yang dibutuhkan fotosintesis?',
                'choices' => ['Oksigen', 'Nitrogen', 'Karbon dioksida', 'Helium'],
                'correct_choice' => 2,
                'explanation' => 'Fotosintesis membutuhkan karbon dioksida.',
                'sort_order' => 2,
            ],
        ]);

        $this->actingAs($user)->post(route('quiz.start', $quiz))->assertRedirect();
        $this->actingAs($user)->post(route('quiz.answer', $quiz), [
            'question_id' => $questions[0]->id,
            'choice' => 0,
        ])->assertRedirect();
        $this->actingAs($user)->post(route('quiz.answer', $quiz), [
            'question_id' => $questions[1]->id,
            'choice' => 0,
        ])->assertRedirect();

        $attempt = QuizAttempt::query()->where('user_id', $user->id)->where('quiz_set_id', $quiz->id)->first();

        $this->assertNotNull($attempt);
        $this->assertSame(QuizAttempt::STATUS_COMPLETED, $attempt->status);
        $this->assertSame(1, $attempt->score);
        $this->assertSame(2, $attempt->total_questions);
        $this->assertEquals(50.0, $attempt->percentage);
        $this->assertDatabaseCount('quiz_attempt_answers', 2);
        $this->assertDatabaseHas('feature_events', [
            'user_id' => $user->id,
            'feature_key' => 'quiz_complete',
            'action' => 'completed',
        ]);
    }
}
