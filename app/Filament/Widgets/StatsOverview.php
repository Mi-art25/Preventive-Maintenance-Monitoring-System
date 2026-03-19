<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use App\Models\Technician;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalEquipment      = Equipment::count();
        $activeEquipment     = Equipment::where('status', 'active')->count();
        $underMaintenance    = Equipment::where('status', 'under_maintenance')->count();

        $overdueSchedules    = MaintenanceSchedule::overdue()->count();
        $dueTodaySchedules   = MaintenanceSchedule::dueToday()->count();
        $activeSchedules     = MaintenanceSchedule::where('status', 'active')->count();

        $completedThisMonth  = MaintenanceLog::completed()
            ->whereMonth('performed_date', now()->month)
            ->whereYear('performed_date', now()->year)
            ->count();

        $pendingLogs         = MaintenanceLog::pending()->count();

        $availableTechs      = Technician::where('is_available', true)->count();
        $totalTechs          = Technician::count();

        return [
            Stat::make('Total Equipment', $totalEquipment)
                ->description("{$activeEquipment} active · {$underMaintenance} under maintenance")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('primary')
                ->chart([4, 6, 8, $totalEquipment]),

            Stat::make('Overdue Schedules', $overdueSchedules)
                ->description("{$dueTodaySchedules} due today · {$activeSchedules} total active")
                ->descriptionIcon($overdueSchedules > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueSchedules > 0 ? 'danger' : 'success')
                ->chart([2, 3, $overdueSchedules]),

            Stat::make('Completed This Month', $completedThisMonth)
                ->description("{$pendingLogs} pending logs")
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success')
                ->chart([3, 5, 7, $completedThisMonth]),

            Stat::make('Available Technicians', $availableTechs)
                ->description("{$availableTechs} of {$totalTechs} available")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([$totalTechs, $availableTechs]),
        ];
    }
}