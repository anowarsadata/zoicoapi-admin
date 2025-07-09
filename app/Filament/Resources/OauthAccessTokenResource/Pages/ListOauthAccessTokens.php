<?php

namespace App\Filament\Resources\OauthAccessTokenResource\Pages;

use App\Filament\Resources\OauthAccessTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOauthAccessTokens extends ListRecords
{
    protected static string $resource = OauthAccessTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
