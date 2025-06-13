<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountTypeResource\Pages;
use App\Models\DiscountType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DiscountTypeResource extends Resource
{
    protected static ?string $model = DiscountType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Products';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Discount Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Discount Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Created'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y')->label('Updated'),
            ])
            ->filters([])
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
            'index' => Pages\ListDiscountTypes::route('/'),
            'create' => Pages\CreateDiscountType::route('/create'),
            'edit' => Pages\EditDiscountType::route('/{record}/edit'),
        ];
    }
}
