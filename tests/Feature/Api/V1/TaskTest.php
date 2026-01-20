<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_get_list_of_task(): void
    {
        $tasks = \App\Models\Task::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/tasks');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'is_completed',
                ]
            ]
        ]);
    }

    public function test_user_can_get_single_task(): void
    {
        $task = \App\Models\Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_completed',
            ]
        ]);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'name' => $task->name,
                'is_completed' => $task->is_completed,
            ]
        ]);
    }

    public function test_user_can_create_task(): void
    {
        $taskData = [
            'name' => 'New Task',
        ];

        $response = $this->postJson('/api/v1/tasks', $taskData);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_completed',
            ]
        ]);
        $this->assertDatabaseHas('tasks', [
            'name' => 'New Task',
            'is_completed' => false,
        ]);
    }

    public function test_user_cannot_create_invalid_task(): void
    {
        $taskData = [
            'name' => '',
        ];

        $response = $this->postJson('/api/v1/tasks', $taskData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_update_task(): void
    {
        $task = \App\Models\Task::factory()->create();

        $updateData = [
            'name' => 'Updated Task Name',
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $updateData);

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Updated Task Name',
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
        ]);
    }

    public function test_user_cannot_update_invalid_task(): void
    {
        $task = \App\Models\Task::factory()->create();

        $updateData = [
            'name' => '',
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $updateData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_toggle_task_completion(): void
    {
        $task = \App\Models\Task::factory()->create([
            'is_completed' => false,
        ]);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/complete", [
            'is_completed' => true,
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'is_completed' => true,
        ]);
    }

    public function test_user_can_delete_task(): void
    {
        $task = \App\Models\Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
