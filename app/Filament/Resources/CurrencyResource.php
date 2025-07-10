<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Location Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('country_id')
                ->relationship('country', 'name')
                ->label('Country')
                ->searchable()
                ->required(),

            TextInput::make('name')
                ->label('Currency Name')
                ->required()
                ->maxLength(100),

            TextInput::make('symbol')
                ->label('Currency Symbol')
                ->required()
                ->maxLength(10),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label('Filter by Country')
                    ->relationship('country', 'name'),
            ])
            ->groups([
                Group::make('country.name')
                    ->label('Country'),
            ])
            ->defaultSort('country.name')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
