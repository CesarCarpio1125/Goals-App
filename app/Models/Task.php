<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'goal_id',
        'title',
        'description',
        'completed',
        'order',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'order' => 'integer',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('completed', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'asc');
    }

    public function markAsCompleted(): bool
    {
        return $this->update(['completed' => true]);
    }

    public function markAsPending(): bool
    {
        return $this->update(['completed' => false]);
    }

    public function toggleCompletion(): bool
    {
        $this->completed = !$this->completed;
        return $this->save();
    }
}
