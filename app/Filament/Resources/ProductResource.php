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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Validation\Rule;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Products';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'This product name already exists.',
                ]),

            Textarea::make('description')->columnSpanFull(),
            Textarea::make('short_description')->columnSpanFull(),

            TextInput::make('price_uk')->numeric()->required(),
            TextInput::make('price_usa')->numeric()->required(),
            TextInput::make('discount')->numeric(),

            Toggle::make('featured')->label('Featured'),

            Select::make('product_category_id')
                ->relationship('productCategory', 'name')
                ->label('Category')
                ->searchable()
                ->required(),

            Select::make('product_discount_type_id')
                ->relationship('discountType', 'name')
                ->label('Discount Type')
                ->searchable(),

            Repeater::make('productAttributes')
    ->label('Product Attributes')
    ->relationship('productAttributes')
    ->schema([
        Forms\Components\Grid::make(3)->schema([
            TextInput::make('name')->label('Attribute Name')->required(),
            TextInput::make('value')->label('Value')->required(),
            TextInput::make('unit')->label('Unit'),
        ]),
    ])
    ->columns(1)
    ->defaultItems(1)
    ->addActionLabel('Add Attribute')
    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
    ->cloneable()
    ->reorderable()
    ->disableLabel()
    ->columnSpanFull()
    ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('short_description')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->short_description),
                TextColumn::make('price_uk')->label('UK Price')->sortable(),
                TextColumn::make('price_usa')->label('USA Price')->sortable(),
                TextColumn::make('productCategory.name')->label('Category')->sortable()->toggleable(),
                TextColumn::make('discountType.name')->label('Discount Type')->sortable()->toggleable(),
                TextColumn::make('discount')->label('Discount')->sortable()->toggleable(),
                BooleanColumn::make('featured')->label('Featured')->sortable(),
            ])
            ->filters([
                SelectFilter::make('product_category_id')
                    ->label('Category')
                    ->relationship('productCategory', 'name'),

                SelectFilter::make('product_discount_type_id')
                    ->label('Discount Type')
                    ->relationship('discountType', 'name'),

                SelectFilter::make('featured')
                    ->label('Featured')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                // Optional custom export stub
                // Tables\Actions\Action::make('export')->label('Export Selected')->icon('heroicon-o-arrow-down-tray')->action(fn ($records) => ...),
            ])
            ->defaultSort('name');
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
