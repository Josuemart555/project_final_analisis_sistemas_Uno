<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::query()->firstOrCreate(
            ['slug' => 'san-marcos-demo'],
            [
                'id' => '00000000-0000-4000-8000-000000000001',
                'name' => 'Hospital General San Marcos (demo)',
                'data' => [],
            ]
        );
    }
}
