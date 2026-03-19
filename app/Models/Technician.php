<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'specialization',
        'contact_number',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    // ─── Auto-generate employee ID on creation ──────────────────────────────────
    // Format: TECH-00001

    protected static function booted(): void
    {
        static::creating(function (Technician $technician) {
            if (empty($technician->employee_id)) {
                $technician->employee_id = self::generateEmployeeId();
            }
        });
    }

    public static function generateEmployeeId(): string
    {
        $last = self::orderByDesc('employee_id')->value('employee_id');

        $next = $last
            ? (int) substr($last, -5) + 1
            : 1;

        return 'TECH-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    // ─── Accessor ───────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown';
    }
}