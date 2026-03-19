<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceScheduleResource\Pages;
use App\Models\MaintenanceSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceScheduleResource extends Resource
{
    protected static ?string $model = MaintenanceSchedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Schedule Information')
                ->schema([
                    Forms\Components\Select::make('equipment_id')
                        ->label('Equipment')
                        ->relationship('equipment', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('technician_id')
                        ->label('Assigned Technician')
                        ->relationship('technician', 'employee_id')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Schedule Settings')
                ->schema([
                    Forms\Components\Select::make('frequency')
                        ->options([
                            'daily'     => 'Daily',
                            'weekly'    => 'Weekly',
                            'monthly'   => 'Monthly',
                            'quarterly' => 'Quarterly',
                            'annually'  => 'Annually',
                        ])
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'active'   => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->required()
                        ->default(now()),
                    Forms\Components\DatePicker::make('next_due_date')
                        ->label('Next Due Date')
                        ->required()
                        ->default(now()),
                    Forms\Components\DatePicker::make('last_performed_date')
                        ->label('Last Performed'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipment.name')
                    ->label('Equipment')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('equipment.serial_number')
                    ->label('Serial No.')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('technician.employee_id')
                    ->label('Technician')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('frequency')
                    ->colors([
                        'info'    => 'daily',
                        'primary' => 'weekly',
                        'success' => 'monthly',
                        'warning' => 'quarterly',
                        'danger'  => 'annually',
                    ]),
                Tables\Columns\TextColumn::make('next_due_date')
                    ->label('Next Due')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record?->next_due_date?->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('last_performed_date')
                    ->label('Last Done')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray'    => 'inactive',
                    ]),
            ])
            ->defaultSort('next_due_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->options([
                        'daily'     => 'Daily',
                        'weekly'    => 'Weekly',
                        'monthly'   => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'annually'  => 'Annually',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'   => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn ($query) => $query->overdue()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaintenanceSchedules::route('/'),
            'create' => Pages\CreateMaintenanceSchedule::route('/create'),
            'edit'   => Pages\EditMaintenanceSchedule::route('/{record}/edit'),
        ];
    }
}