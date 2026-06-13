<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public const TENANT_ID = '00000000-0000-4000-8000-000000000001';
    public const EMAIL = 'recepcionista@demo.test';
    public const PASSWORD = 'password';

    public function run(): void
    {
        $tenant = Tenant::query()->findOrFail(self::TENANT_ID);

        /** @var User $user */
        $user = User::query()->updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'email' => self::EMAIL,
            ],
            [
                'name' => 'Recepcionista Demo',
                'password' => Hash::make(self::PASSWORD),
            ],
        );

        $user->assignRole('Recepcionista');
    }
}
