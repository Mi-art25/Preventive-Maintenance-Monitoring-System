<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Equipment Information')
                ->schema([
                    Forms\Components\TextInput::make('serial_number')
                        ->label('Serial Number')
                        ->disabled()
                        ->placeholder('Auto-generated on save')
                        ->helperText('Format: EQP-YYYY-XXXXX'),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('brand')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('model')
                        ->maxLength(255),
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('location_id')
                        ->label('Location')
                        ->relationship('location', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'active'            => 'Active',
                            'inactive'          => 'Inactive',
                            'under_maintenance' => 'Under Maintenance',
                            'retired'           => 'Retired',
                        ])
                        ->default('active')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Purchase Details')
                ->schema([
                    Forms\Components\DatePicker::make('purchase_date'),
                    Forms\Components\DatePicker::make('warranty_expiry')
                        ->label('Warranty Expiry'),
                    Forms\Components\Textarea::make('notes')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('QR Code')
                ->schema([
                    Forms\Components\Placeholder::make('qr_code_preview')
                        ->label('QR Code')
                        ->content(function ($record) {
                            if (!$record || !$record->qr_code_path) {
                                return 'QR code will be generated after saving.';
                            }
                            $url = $record->qr_code_url;
                            return new \Illuminate\Support\HtmlString(
                                "<img src='{$url}' style='width:150px;height:150px;' />"
                                . "<p style='margin-top:8px;font-size:12px;color:#666;'>{$record->serial_number}</p>"
                            );
                        }),
                ])->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial No.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'under_maintenance',
                        'danger'  => 'retired',
                        'gray'    => 'inactive',
                    ]),
                Tables\Columns\TextColumn::make('warranty_expiry')
                    ->label('Warranty')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record?->warranty_expiry?->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index'  => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit'   => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }
}