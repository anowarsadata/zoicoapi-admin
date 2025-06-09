<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\EditAction;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

 
    public function getInfolistSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextEntry::make('user.name')->label('User'),
                TextEntry::make('currency.name')->label('Currency'),
                TextEntry::make('payment_method')->label('Payment Method'),
                TextEntry::make('subtotal')->label('Subtotal'),
                TextEntry::make('total')->label('Total'),
                TextEntry::make('status')->label('Status'),
                TextEntry::make('remarks')->label('Remarks'),
            ]),
            Grid::make(2)->schema([
                TextEntry::make('billingAddress.street')->label('Billing Address'),
                TextEntry::make('shippingAddress.street')->label('Shipping Address'),
            ]),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); 
    }
}
