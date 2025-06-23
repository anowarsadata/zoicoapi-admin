<?php
namespace App\Filament\Resources;

use App\Models\MenuItem;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\MenuItemResource\Pages;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = 'Menus';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('menu_id')
                ->label('Menu')
                ->relationship('menu', 'name')
                ->required(),

            TextInput::make('name')->required(),
            TextInput::make('url')->required(),
            TextInput::make('target'),
            TextInput::make('css_class'),
            TextInput::make('css_id'),
            TextInput::make('item_order')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('menu.name')->label('Menu'),
            TextColumn::make('name'),
            TextColumn::make('url'),
            TextColumn::make('item_order'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
