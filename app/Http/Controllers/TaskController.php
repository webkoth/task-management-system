<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Services\TaskService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->getTasks(
            $request->get('status'),
            $request->get('due_date'),
            $request->get('search'),
            $request->user()->id
        );

        return response()->json($tasks);
    }

    /**
     * @throws AuthorizationException
     * @throws Exception
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;

        $task = $this->taskService->createTask($validatedData);

        return response()->json($task, 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json($task->load(['creator', 'assignee']));
    }

    /**
     * @throws AuthorizationException
     * @throws Exception
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->taskService->updateTask($task, $request->validated());

        return response()->json($task);
    }

    /**
     * @throws AuthorizationException|Exception
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateTaskStatus($task, $request->validated()['status']);

        return response()->json($task);
    }

    /**
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json(null, 204);
    }

    public function userTasks(Request $request): JsonResponse
    {
        $tasks = $this->taskService->getUserTasks($request->user());

        return response()->json($tasks);
    }
}
