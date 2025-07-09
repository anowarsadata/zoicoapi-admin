<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OauthAccessTokenResource\Pages;
use App\Models\OauthAccessToken;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class OauthAccessTokenResource extends Resource
{
    protected static ?string $model = OauthAccessToken::class;
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Authentication';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('id')
                ->label('Access Token')
                ->readOnly()
                ->columnSpanFull(),

            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->preload(),

            TextInput::make('client_id')->required()->numeric(),
            TextInput::make('name')->required(),
            TextInput::make('scopes'),
            Toggle::make('revoked')->label('Revoked'),
            DateTimePicker::make('expires_at'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('id')
                ->label('Access Token')
                ->copyable()
                ->limit(40)
                ->tooltip(fn ($record) => $record->id),

            TextColumn::make('user.name')->label('User')->searchable(),
            TextColumn::make('client_id'),
            TextColumn::make('name'),
            IconColumn::make('revoked')->boolean(),
            TextColumn::make('created_at')->dateTime(),
            TextColumn::make('expires_at')->dateTime(),
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
            'index' => Pages\ListOauthAccessTokens::route('/'),
            'create' => Pages\CreateOauthAccessToken::route('/create'),
            'edit' => Pages\EditOauthAccessToken::route('/{record}/edit'),
        ];
    }
}
