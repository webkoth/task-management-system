<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->task = Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::PENDING,
        ]);
    }

    /** @test */
    public function user_can_view_tasks_list(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'user_id',
                        'assigned_to_user_id',
                        'due_date',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta',
                'links'
            ]);
    }

    /** @test */
    public function user_can_create_task(): void
    {
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => TaskStatus::PENDING->value,
            'assigned_to_user_id' => $this->user->id,
            'due_date' => now()->addDays(5)->format('Y-m-d H:i:s')
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
            ]);
    }

    /** @test */
    public function user_can_view_task_details(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks/{$this->task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $this->task->id,
                'title' => $this->task->title,
            ]);
    }

    /** @test */
    public function user_can_update_own_task(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'status' => TaskStatus::IN_PROGRESS->value,
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$this->task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
                'status' => TaskStatus::IN_PROGRESS->value,
            ]);
    }

    /** @test */
    public function user_can_delete_own_task(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$this->task->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('tasks', ['id' => $this->task->id]);
    }

    /** @test */
    public function user_cannot_update_others_task(): void
    {
        $otherUser = User::factory()->create();
        $othersTask = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$othersTask->id}", [
                'title' => 'Updated Title'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_delete_others_task(): void
    {
        $otherUser = User::factory()->create();
        $othersTask = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$othersTask->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function assignee_can_update_task_status(): void
    {
        $assignedTask = Task::factory()->create([
            'user_id' => User::factory()->create()->id,
            'assigned_to_user_id' => $this->user->id,
            'status' => TaskStatus::PENDING
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$assignedTask->id}", [
                'status' => TaskStatus::IN_PROGRESS->value
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'status' => TaskStatus::IN_PROGRESS->value
            ]);
    }

    /** @test */
    public function task_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'assigned_to_user_id']);
    }

    /** @test */
    public function task_can_be_filtered_by_status(): void
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => TaskStatus::COMPLETED
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks?status=' . TaskStatus::COMPLETED->value);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
