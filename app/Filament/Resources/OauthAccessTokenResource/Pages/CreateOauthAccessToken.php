<?php

namespace App\Filament\Resources\OauthAccessTokenResource\Pages;

use App\Filament\Resources\OauthAccessTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOauthAccessToken extends CreateRecord
{
    protected static string $resource = OauthAccessTokenResource::class;
}
