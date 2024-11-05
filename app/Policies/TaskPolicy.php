<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    private const MESSAGE_UNAUTHORIZED = 'You are not authorized to perform this action.';
    private const MESSAGE_TASK_CREATOR_ONLY = 'Only the task creator can perform this action.';
    private const MESSAGE_TASK_PARTICIPANT = 'You must be the task creator or assignee to perform this action.';

    public function viewAny(User $user): Response
    {
        return Response::allow();
    }


    public function view(User $user, Task $task): Response
    {
        return $this->isTaskParticipant($user, $task)
            ? Response::allow()
            : Response::deny(self::MESSAGE_TASK_PARTICIPANT);
    }


    public function create(User $user): Response
    {
        return Response::allow();
    }


    public function update(User $user, Task $task): Response
    {
        return $this->isTaskParticipant($user, $task)
            ? Response::allow()
            : Response::deny(self::MESSAGE_TASK_PARTICIPANT);
    }


    public function delete(User $user, Task $task): Response
    {
        return $this->isTaskCreator($user, $task)
            ? Response::allow()
            : Response::deny(self::MESSAGE_TASK_CREATOR_ONLY);
    }


    public function restore(User $user, Task $task): Response
    {
        return $this->isTaskCreator($user, $task)
            ? Response::allow()
            : Response::deny(self::MESSAGE_TASK_CREATOR_ONLY);
    }


    public function forceDelete(User $user, Task $task): Response
    {
        return $this->isTaskCreator($user, $task)
            ? Response::allow()
            : Response::deny(self::MESSAGE_TASK_CREATOR_ONLY);
    }


    public function assign(User $user, Task $task): Response
    {
        return $this->isTaskCreator($user, $task)
            ? Response::allow()
            : Response::deny(self::MESSAGE_TASK_CREATOR_ONLY);
    }

    private function isTaskCreator(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    private function isTaskParticipant(User $user, Task $task): bool
    {
        return $this->isTaskCreator($user, $task) || $user->id === $task->assigned_to_user_id;
    }
}
