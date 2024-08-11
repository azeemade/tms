<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_get_their_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->create(); // Other user's tasks

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'title', 'description', 'user_id', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_user_can_view_their_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ]
            ]);
    }

    public function test_user_cannot_view_nonexistent_task()
    {
        $response = $this->actingAs($this->user)->getJson("/api/tasks/999");

        $response->assertStatus(404);
    }

    public function test_user_can_create_task()
    {
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks/create', $taskData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'title', 'description', 'user_id', 'created_at', 'updated_at']
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_update_their_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => true
        ];

        $response = $this->actingAs($this->user)->putJson("/api/tasks/update/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $task->id,
                    'title' => 'Updated Title',
                    'completed' => true,
                ]
            ]);
    }

    public function test_user_cannot_update_nonexistent_task()
    {
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($this->user)->putJson("/api/tasks/update/999", $updatedData);

        $response->assertStatus(404);
    }

    public function test_user_can_delete_their_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/destroy/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_nonexistent_task()
    {
        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/destroy/999");

        $response->assertStatus(404);
    }
}