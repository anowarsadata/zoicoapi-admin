<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required(),

            TextInput::make('password')
                ->password()
                ->required()
                ->visibleOn('create'),

            Select::make('roles')
                ->label('Role')
                ->relationship('roles', 'name')
                ->multiple(false)
                ->preload()
                ->searchable()
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    1 => 'Active',
                    0 => 'In-active',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('email')->sortable()->searchable(),
            TextColumn::make('created_at')->dateTime(),
            TextColumn::make('roles.name')
                ->label('Role')
                ->badge()
                ->sortable()
                ->searchable(),
            TextColumn::make('status')
                ->label('Status')
                ->getStateUsing(fn($record) => $record->status ? 'Active' : 'In-active'),
        ])
        ->filters([
            SelectFilter::make('status')
                ->label('Status')
                ->options([
                    1 => 'Active',
                    0 => 'In-active',
                ]),

            SelectFilter::make('roles')
                ->label('Role')
                ->relationship('roles', 'name'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }
}
