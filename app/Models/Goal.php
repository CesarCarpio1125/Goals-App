<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'target_value',
        'current_value',
        'deadline',
        'hash',
    ];

    protected $casts = [
        'deadline' => 'date',
        'target_value' => 'integer',
        'current_value' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($goal) {
            // Solo generar hash si la columna existe en la BD
            try {
                // Verificar si la columna existe haciendo una consulta segura
                $schema = \Illuminate\Support\Facades\Schema::getColumnListing('goals');
                if (in_array('hash', $schema) && empty($goal->hash)) {
                    $goal->hash = self::generateUniqueHash();
                }
            } catch (\Exception $e) {
                // Si hay cualquier error, simplemente no generar hash
                // Esto permite que la aplicación funcione sin la columna hash
            }
        });
    }

    public static function generateUniqueHash(): string
    {
        // Verificar si la columna existe antes de generar
        try {
            $schema = \Illuminate\Support\Facades\Schema::getColumnListing('goals');
            if (!in_array('hash', $schema)) {
                return ''; // Retornar vacío si la columna no existe
            }
        } catch (\Exception $e) {
            return ''; // Retornar vacío si hay error
        }
        
        do {
            $hash = 'goal_' . str()->random(12);
        } while (self::where('hash', $hash)->exists());
        
        return $hash;
    }

    // Scope para encontrar por hash (con fallback si la columna no existe)
    public function scopeByHash($query, $hash)
    {
        try {
            $schema = \Illuminate\Support\Facades\Schema::getColumnListing('goals');
            if (!in_array('hash', $schema)) {
                // La columna no existe, devolver query vacío
                return $query->whereRaw('1 = 0');
            }
            return $query->where('hash', $hash);
        } catch (\Exception $e) {
            // Si hay cualquier error, devolver query vacío
            return $query->whereRaw('1 = 0');
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->ordered();
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_value === 0) return 0;
        
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return 0;
        
        $completedTasks = $this->tasks()->completed()->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    public function getTaskProgressAttribute(): array
    {
        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->tasks()->completed()->count();
        $pendingTasks = $totalTasks - $completedTasks;
        
        return [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'pending' => $pendingTasks,
            'percentage' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0
        ];
    }

    public function getMotivationalMessageAttribute(): string
    {
        $progress = $this->task_progress;
        $percentage = $progress['percentage'];
        
        if ($percentage === 0) {
            return "¡El primer paso es el más importante! Comienza con una pequeña tarea hoy.";
        } elseif ($percentage < 25) {
            return "¡Buen comienzo! Cada tarea completada te acerca más a tu objetivo.";
        } elseif ($percentage < 50) {
            return "¡Vas por buen camino! Sigue así, estás haciendo un gran progreso.";
        } elseif ($percentage < 75) {
            return "¡Excelente trabajo! Más de la mitad completado, no te detengas ahora.";
        } elseif ($percentage < 100) {
            return "¡Casi ahí! Queda poco para alcanzar tu objetivo, ¡dá el último empujón!";
        } else {
            return "¡Felicidades! 🎉 Has completado tu objetivo. ¡Eres un campeón!";
        }
    }

    public function getRecommendationsAttribute(): array
    {
        $progress = $this->task_progress;
        $percentage = $progress['percentage'];
        $recommendations = [];
        
        if ($percentage === 0) {
            $recommendations[] = "Empieza con la tarea más fácil para ganar momentum.";
            $recommendations[] = "Establece un tiempo específico hoy para trabajar en tu objetivo.";
        } elseif ($percentage < 50) {
            $recommendations[] = "Celebra cada pequeña victoria para mantenerte motivado.";
            $recommendations[] = "Identifica qué tareas te están bloqueando y busca soluciones.";
        } elseif ($percentage < 100) {
            $recommendations[] = "Revisa las tareas restantes y prioriza las más importantes.";
            $recommendations[] = "Visualiza el éxito final para mantener la energía alta.";
        }
        
        // Check if overdue
        if ($this->deadline && $this->deadline->isPast() && $percentage < 100) {
            $recommendations[] = "Tu fecha límite ha pasado. Considera ajustar tu objetivo o fecha.";
            $recommendations[] = "No te desanimes. Aprende de esta experiencia para próximos objetivos.";
        }
        
        return $recommendations;
    }

    public function updateProgress(): void
    {
        $this->update([
            'current_value' => $this->task_progress['completed']
        ]);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->current_value >= $this->target_value;
    }

    public function getRemainingValueAttribute(): int
    {
        return max(0, $this->target_value - $this->current_value);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && $this->deadline->isPast() && !$this->is_completed;
    }

    public function scopeCompleted($query)
    {
        return $query->where('current_value', '>=', 'target_value');
    }

    public function scopeInProgress($query)
    {
        return $query->where('current_value', '<', 'target_value');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->where('current_value', '<', 'target_value');
    }

    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('deadline', '<=', now()->addDays($days))
                     ->where('deadline', '>', now())
                     ->where('current_value', '<', 'target_value');
    }
}
