<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalAccessTokenResource\Pages;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

//use Filament\Tables\Columns\DateTimeColumn;

class PersonalAccessTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Authentication';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('tokenable_id')
                ->label('User')
                ->options(User::all()->pluck('name', 'id'))
                ->required(),

            TextInput::make('tokenable_type')
                ->default('App\\Models\\User')
                ->required(),

            TextInput::make('name')->required(),

            TextInput::make('token')->required(),

            TextInput::make('abilities')->required(),

            DateTimePicker::make('expires_at'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('tokenable_type')->label('Owner Type'),
                TextColumn::make('tokenable_id')->label('Owner ID'),
                TextColumn::make('abilities'),
                TextColumn::make('last_used_at')->label('Last Used')->dateTime(),
                TextColumn::make('expires_at')->label('Expires At')->dateTime(),
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
            'index' => Pages\ListPersonalAccessTokens::route('/'),
            'create' => Pages\CreatePersonalAccessToken::route('/create'),
            'edit' => Pages\EditPersonalAccessToken::route('/{record}/edit'),
        ];
    }
}
