<?php

namespace Tests\Feature;

use App\Models\StudyRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyRoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_rooms_page(): void
    {
        $this->get(route('rooms.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_room(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('rooms.store'), [
            'name' => 'Biologi Intensif',
            'topic' => 'Biologi',
            'description' => 'Diskusi bab sel',
            'visibility' => 'public',
            'max_members' => 25,
        ])->assertRedirect();

        $this->assertDatabaseHas('study_rooms', [
            'name' => 'Biologi Intensif',
            'owner_id' => $user->id,
        ]);
    }
}
