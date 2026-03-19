<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceSchedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueMaintenanceTable extends BaseWidget
{
    protected static ?string $heading = 'Overdue & Upcoming Maintenance';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MaintenanceSchedule::query()
                    ->with(['equipment', 'technician.user'])
                    ->where('status', 'active')
                    ->orderBy('next_due_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('equipment.name')
                    ->label('Equipment')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('equipment.serial_number')
                    ->label('Serial No.')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Maintenance Task')
                    ->limit(35),

                Tables\Columns\TextColumn::make('technician.user.name')
                    ->label('Technician'),

                Tables\Columns\BadgeColumn::make('frequency')
                    ->colors([
                        'info'    => 'daily',
                        'primary' => 'weekly',
                        'success' => 'monthly',
                        'warning' => 'quarterly',
                        'danger'  => 'annually',
                    ]),

                Tables\Columns\TextColumn::make('next_due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => match(true) {
                        $record->next_due_date->isPast()    => 'danger',
                        $record->next_due_date->isToday()   => 'warning',
                        $record->next_due_date->isTomorrow()=> 'warning',
                        default                             => 'success',
                    }),

                Tables\Columns\BadgeColumn::make('due_status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => match(true) {
                        $record->next_due_date->isPast()    => 'Overdue',
                        $record->next_due_date->isToday()   => 'Due Today',
                        $record->next_due_date->isTomorrow()=> 'Due Tomorrow',
                        default                             => 'Upcoming',
                    })
                    ->colors([
                        'danger'  => 'Overdue',
                        'warning' => 'Due Today',
                        'warning' => 'Due Tomorrow',
                        'success' => 'Upcoming',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('log')
                    ->label('Log Maintenance')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->url(fn ($record) => route('filament.admin.resources.maintenance-logs.create', [
                        'equipment_id'            => $record->equipment_id,
                        'maintenance_schedule_id' => $record->id,
                        'technician_id'           => $record->technician_id,
                    ]))
                    ->color('primary'),
            ]);
    }
}