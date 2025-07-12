<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAddressResource\Pages;
use App\Models\UserAddress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Grouping\Group;

class UserAddressResource extends Resource
{
    protected static ?string $model = UserAddress::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Location Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('street')->required(),
                TextInput::make('zip_code')->required(),

                Select::make('country')
                    ->label('Country')
                    ->options(UserAddress::query()->distinct()->pluck('country', 'country')->filter()->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('state', null))
                    ->required(),

                Select::make('state')
                    ->label('State')
                    ->options(fn (callable $get) => UserAddress::query()
                        ->where('country', $get('country'))
                        ->distinct()
                        ->pluck('state', 'state')
                        ->filter()
                        ->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('city', null))
                    ->required(),

                Select::make('city')
                    ->label('City')
                    ->options(fn (callable $get) => UserAddress::query()
                        ->where('state', $get('state'))
                        ->distinct()
                        ->pluck('city', 'city')
                        ->filter()
                        ->toArray())
                    ->searchable()
                    ->required(),

                TextInput::make('phone')->required(),

                Select::make('type')
                    ->label('Address Type')
                    ->options([
                        'Shipping' => 'Shipping',
                        'Billing' => 'Billing',
                    ])
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->sortable()->searchable(),
                TextColumn::make('street')->sortable()->searchable(),
                TextColumn::make('zip_code')->label('ZIP Code')->sortable(),
                TextColumn::make('city')->sortable()->searchable(),
                TextColumn::make('state')->sortable()->searchable(),
                TextColumn::make('country')->sortable()->searchable(),
                TextColumn::make('phone')->sortable(),
                TextColumn::make('type')->sortable(),
            ])
            ->filters([
                SelectFilter::make('country')
                    ->label('Country')
                    ->options(UserAddress::query()->distinct()->pluck('country', 'country')->toArray()),

                SelectFilter::make('state')
                    ->label('State')
                    ->options(UserAddress::query()->distinct()->pluck('state', 'state')->toArray()),

                SelectFilter::make('city')
                    ->label('City')
                    ->options(UserAddress::query()->distinct()->pluck('city', 'city')->toArray()),
            ])
            ->groups([
                Group::make('country')
                    ->label('Grouped by Country'),
            ])
            ->defaultSort('country')
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
            'index' => Pages\ListUserAddresses::route('/'),
            'create' => Pages\CreateUserAddress::route('/create'),
            'edit' => Pages\EditUserAddress::route('/{record}/edit'),
        ];
    }
}
