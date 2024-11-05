<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function getTasks(
        ?string $status,
        ?string $dueDate,
        ?string $search,
        int $userId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->taskRepository->getTasksForUser(
            userId: $userId,
            status: $status ? TaskStatus::from($status) : null,
            dueDate: $dueDate,
            search: $search,
            perPage: $perPage
        );
    }

    /**
     * @throws Exception
     */
    public function createTask(array $data): Task
    {
        try {
            DB::beginTransaction();

            $task = Task::create($data);

            DB::commit();

            return $task->load(['creator', 'assignee']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTaskStatus(Task $task, TaskStatus $newStatus): Task
    {
        try {
            DB::beginTransaction();

            $task->update(['status' => $newStatus]);

            DB::commit();

            return $task->fresh(['creator', 'assignee']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function updateTask(Task $task, array $data): Task
    {
        try {
            DB::beginTransaction();

            if (isset($data['status']) && $data['status'] !== $task->status) {
                $this->updateTaskStatus($task, TaskStatus::from($data['status']));
                unset($data['status']);
            }

            $task->update($data);

            DB::commit();

            return $task->fresh(['creator', 'assignee']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteTask(Task $task): bool
    {
        try {
            DB::beginTransaction();

            $result = $task->delete();

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function restoreTask(Task $task): bool
    {
        try {
            DB::beginTransaction();

            $result = $task->restore();

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUserTasks(User $user): LengthAwarePaginator
    {
        return Task::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('assigned_to_user_id', $user->id);
            })
            ->with(['creator', 'assignee'])
            ->latest()
            ->paginate(15);
    }
}
