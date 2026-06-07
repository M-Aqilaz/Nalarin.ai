<?php

namespace Tests\Feature;

use App\Models\StudyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_study_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('matchmaking.profile.update'), [
            'education_level' => 'SMA',
            'primary_subject' => 'Kimia',
            'goal' => 'Persiapan UTBK',
            'study_style' => 'Diskusi',
            'bio' => 'Suka belajar kelompok',
            'availability' => 'Malam',
            'is_matchmaking_enabled' => '1',
        ])->assertRedirect(route('matchmaking.roulette'));

        $this->assertDatabaseHas('study_profiles', [
            'user_id' => $user->id,
            'primary_subject' => 'Kimia',
        ]);
    }

    public function test_matching_search_requires_profile_enabled(): void
    {
        $user = User::factory()->create();
        StudyProfile::create([
            'user_id' => $user->id,
            'is_matchmaking_enabled' => false,
        ]);

        $this->actingAs($user)->post(route('matchmaking.search'), [
            'selected_topic' => 'Aljabar',
        ])->assertSessionHasErrors('matchmaking');
    }
}
