<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            Textarea::make('description'),
            Textarea::make('short_description'),
            TextInput::make('price_uk')->numeric()->required(),
            TextInput::make('price_usa')->numeric()->required(),
            TextInput::make('discount')->numeric(),
            Toggle::make('featured')->label('Featured'),
            Select::make('product_category_id')
                ->relationship('productCategory', 'name')
                ->required(),
            Select::make('product_discount_type_id')
                ->relationship('discountType', 'name')
                ->label('Discount Type'),

            // Product Attributes shown in Edit view only
            Repeater::make('productAttributes')
                ->label('Product Attributes')
                ->relationship('productAttributes')
                ->schema([
                    TextInput::make('name')->disabled(),
                    TextInput::make('value')->disabled(),
                    TextInput::make('unit')->disabled(),
                ])
                ->columns(3)
                ->disabled()
                ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('price_uk')->label('UK Price'),
            TextColumn::make('price_usa')->label('USA Price'),
            TextColumn::make('productCategory.name')->label('Category'),
            TextColumn::make('discountType.name')->label('Discount Type'),
            TextColumn::make('discount')->label('Discount'),
            BooleanColumn::make('featured')->label('Featured'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
