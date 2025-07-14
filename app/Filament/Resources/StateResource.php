<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Models\State;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class StateResource extends Resource
{
    protected static ?string $model = State::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Location Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('State Name')
                ->required()
                ->maxLength(255),

            Select::make('country_id')
                ->label('Country')
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('State')->sortable()->searchable(),
                TextColumn::make('country.name')->label('Country')->sortable()->searchable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label('Filter by Country')
                    ->options(
                        Country::whereHas('states') // ✅ Only countries that have states
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
            ])
            ->defaultSort('country_id') // ✅ Sort by country (simulate grouping)
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
