<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected $roleId;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store role ID and remove from form data
        $this->roleId = $data['roles'];
        unset($data['roles']);

        // Hash password
        $data['password'] = bcrypt($data['password']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $role = Role::findById($this->roleId, 'web'); // Get role name by ID
        $this->record->syncRoles($role->name);        // Use role name, not ID
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
