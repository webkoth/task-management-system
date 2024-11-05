<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'assigned_to_user_id',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
