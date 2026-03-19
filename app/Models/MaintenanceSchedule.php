<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'equipment_id',
        'technician_id',
        'title',
        'description',
        'frequency',
        'start_date',
        'next_due_date',
        'last_performed_date',
        'status',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'next_due_date'       => 'date',
        'last_performed_date' => 'date',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    // ─── Next Due Date Calculator ───────────────────────────────────────────────

    public function calculateNextDueDate(): Carbon
    {
        $from = $this->last_performed_date ?? $this->start_date;

        return match ($this->frequency) {
            'daily'     => $from->copy()->addDay(),
            'weekly'    => $from->copy()->addWeek(),
            'monthly'   => $from->copy()->addMonth(),
            'quarterly' => $from->copy()->addMonths(3),
            'annually'  => $from->copy()->addYear(),
            default     => $from->copy()->addMonth(),
        };
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_due_date', '<', now()->toDateString())
                     ->where('status', 'active');
    }

    public function scopeDueToday($query)
    {
        return $query->where('next_due_date', now()->toDateString())
                     ->where('status', 'active');
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('next_due_date', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString(),
        ])->where('status', 'active');
    }

    // ─── Accessors ──────────────────────────────────────────────────────────────

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_due_date->isPast() && $this->status === 'active';
    }
}