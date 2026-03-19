<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'maintenance_schedule_id',
        'equipment_id',
        'technician_id',
        'performed_date',
        'findings',
        'actions_taken',
        'status',
        'parts_replaced',
        'cost',
        'remarks',
    ];

    protected $casts = [
        'performed_date' => 'date',
        'cost'           => 'decimal:2',
    ];

    // ─── Auto-update schedule when log is completed ─────────────────────────────

    protected static function booted(): void
    {
        static::updated(function (MaintenanceLog $log) {
            if ($log->status === 'completed' && $log->wasChanged('status')) {
                $schedule = $log->maintenanceSchedule;

                if ($schedule) {
                    $schedule->update([
                        'last_performed_date' => $log->performed_date,
                        'next_due_date'       => $schedule->calculateNextDueDate(),
                    ]);

                    // Update equipment status back to active
                    $log->equipment?->update(['status' => 'active']);
                }
            }
        });
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
}