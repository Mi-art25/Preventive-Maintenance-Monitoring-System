<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceLogResource\Pages;
use App\Models\MaintenanceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceLogResource extends Resource
{
    protected static ?string $model = MaintenanceLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Log Information')
                ->schema([
                    Forms\Components\Select::make('maintenance_schedule_id')
                        ->label('Maintenance Schedule')
                        ->relationship('maintenanceSchedule', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('equipment_id')
                        ->label('Equipment')
                        ->relationship('equipment', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('technician_id')
                        ->label('Technician')
                        ->relationship('technician', 'employee_id')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\DatePicker::make('performed_date')
                        ->label('Performed Date')
                        ->required()
                        ->default(now()),
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending'     => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed'   => 'Completed',
                            'overdue'     => 'Overdue',
                        ])
                        ->default('pending')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Maintenance Details')
                ->schema([
                    Forms\Components\Textarea::make('findings')
                        ->label('Findings / Observations')
                        ->rows(3),
                    Forms\Components\Textarea::make('actions_taken')
                        ->label('Actions Taken')
                        ->rows(3),
                    Forms\Components\TextInput::make('parts_replaced')
                        ->label('Parts Replaced')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cost')
                        ->label('Cost (₱)')
                        ->numeric()
                        ->prefix('₱'),
                    Forms\Components\Textarea::make('remarks')
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('maintenanceSchedule.title')
                    ->label('Schedule')
                    ->limit(30),
                Tables\Columns\TextColumn::make('technician.employee_id')
                    ->label('Technician')
                    ->sortable(),
                Tables\Columns\TextColumn::make('performed_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger'  => 'overdue',
                    ]),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Cost')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('performed_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'overdue'     => 'Overdue',
                    ]),
                Tables\Filters\SelectFilter::make('equipment')
                    ->relationship('equipment', 'name'),
                Tables\Filters\SelectFilter::make('technician')
                    ->relationship('technician', 'employee_id'),
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
            'index'  => Pages\ListMaintenanceLogs::route('/'),
            'create' => Pages\CreateMaintenanceLog::route('/create'),
            'edit'   => Pages\EditMaintenanceLog::route('/{record}/edit'),
        ];
    }
}