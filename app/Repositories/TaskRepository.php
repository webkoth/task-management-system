<?php

namespace App\Repositories;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository
{
    public function __construct(
        private readonly Task $model
    ) {}

    public function getTasksForUser(
        int $userId,
        ?TaskStatus $status = null,
        ?string $dueDate = null,
        ?string $search = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->model->newQuery()
            ->where(function (Builder $query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('assigned_to_user_id', $userId);
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($dueDate, fn ($query) => $query->whereDate('due_date', '<=', $dueDate))
            ->when($search, fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->with(['creator', 'assignee'])
            ->latest()
            ->paginate($perPage);
    }

    public function getUserTasksByStatus(int $userId, TaskStatus $status): Collection
    {
        return $this->model->newQuery()
            ->where(function (Builder $query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('assigned_to_user_id', $userId);
            })
            ->where('status', $status)
            ->with(['creator', 'assignee'])
            ->get();
    }
}
