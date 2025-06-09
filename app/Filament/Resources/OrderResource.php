<?php
namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->searchable(),
            Forms\Components\Select::make('currency_id')->relationship('currency', 'name'),
            Forms\Components\Select::make('billing_address_id')->relationship('billingAddress', 'street'),
            Forms\Components\Select::make('shipping_address_id')->relationship('shippingAddress', 'street'),
            Forms\Components\TextInput::make('payment_method'),
            Forms\Components\TextInput::make('subtotal'),
            Forms\Components\TextInput::make('total'),
            Forms\Components\Textarea::make('remarks'),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'cancel' => 'Cancelled',
                    'completed' => 'Completed',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id'),
            TextColumn::make('user.name'),
            TextColumn::make('currency.name')->label('Currency'),
            TextColumn::make('status'),
            TextColumn::make('total'),
            TextColumn::make('created_at')->dateTime(),
        ])
        ->actions([
            ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager::class,
        ];
    }

}
