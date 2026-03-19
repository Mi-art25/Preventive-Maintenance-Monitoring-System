<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicianResource\Pages;
use App\Models\Technician;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TechnicianResource extends Resource
{
    protected static ?string $model = Technician::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Technician Details')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('User Account')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('employee_id')
                        ->label('Employee ID')
                        ->disabled()
                        ->placeholder('Auto-generated on save')
                        ->helperText('Format: TECH-XXXXX'),
                    Forms\Components\TextInput::make('specialization')
                        ->maxLength(255)
                        ->placeholder('e.g. Electrical, HVAC, Mechanical'),
                    Forms\Components\TextInput::make('contact_number')
                        ->label('Contact Number')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_available')
                        ->label('Available')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialization')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->label('Contact'),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean(),
                Tables\Columns\TextColumn::make('maintenance_schedules_count')
                    ->label('Schedules')
                    ->counts('maintenanceSchedules')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_available')->label('Available'),
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
            'index'  => Pages\ListTechnicians::route('/'),
            'create' => Pages\CreateTechnician::route('/create'),
            'edit'   => Pages\EditTechnician::route('/{record}/edit'),
        ];
    }
}