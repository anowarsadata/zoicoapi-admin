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

class UserAddressResource extends Resource
{
    protected static ?string $model = UserAddress::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        // Distinct options for dropdowns
        $countryOptions = UserAddress::query()->distinct()->pluck('country', 'country')->filter()->toArray();
        $stateOptions = UserAddress::query()->distinct()->pluck('state', 'state')->filter()->toArray();
        $cityOptions = UserAddress::query()->distinct()->pluck('city', 'city')->filter()->toArray();

        return $form->schema([
            Grid::make(2)->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('street')->required(),
                TextInput::make('zip_code')->required(),

                Select::make('country')
                    ->label('Country')
                    ->options($countryOptions)
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('state', null))
                    ->required(),

                Select::make('state')
                    ->label('State')
                    ->options(fn (callable $get) => 
                        UserAddress::query()
                            ->where('country', $get('country'))
                            ->distinct()
                            ->pluck('state', 'state')
                            ->filter()
                            ->toArray()
                    )
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('city', null))
                    ->required(),

                Select::make('city')
                    ->label('City')
                    ->options(fn (callable $get) => 
                        UserAddress::query()
                            ->where('state', $get('state'))
                            ->distinct()
                            ->pluck('city', 'city')
                            ->filter()
                            ->toArray()
                    )
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
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('street'),
                TextColumn::make('zip_code'),
                TextColumn::make('city'),
                TextColumn::make('state'),
                TextColumn::make('country'),
                TextColumn::make('phone'),
                TextColumn::make('type'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->label('Filter by Country')
                    ->options(UserAddress::query()->distinct()->pluck('country', 'country')->toArray()),

                Tables\Filters\SelectFilter::make('state')
                    ->label('Filter by State')
                    ->options(UserAddress::query()->distinct()->pluck('state', 'state')->toArray()),

                Tables\Filters\SelectFilter::make('city')
                    ->label('Filter by City')
                    ->options(UserAddress::query()->distinct()->pluck('city', 'city')->toArray()),
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
            'index' => Pages\ListUserAddresses::route('/'),
            'create' => Pages\CreateUserAddress::route('/create'),
            'edit' => Pages\EditUserAddress::route('/{record}/edit'),
        ];
    }
}
