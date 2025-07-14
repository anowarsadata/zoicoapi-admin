<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\State;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;

class CityResource extends Resource
{
    protected static ?string $model = City::class;
    protected static ?string $navigationGroup = 'Location Management';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('state_id')
                ->relationship('state', 'name')
                ->required()
                ->label('State'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('state.name')->label('State')->sortable()->searchable(),
            ])
            ->filters([
                SelectFilter::make('state_id')
                    ->label('Filter by State')
                    ->options(
                        State::whereIn('id', City::select('state_id')->distinct())
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
            ])
            ->groups([
                Group::make('state.name')->label('State'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
