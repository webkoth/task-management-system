<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $this->user()->can('update', $task);
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', 'string', new Enum(TaskStatus::class)],
            'assigned_to_user_id' => ['sometimes', 'required', 'exists:users,id'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after:today'],
        ];

    }
}
