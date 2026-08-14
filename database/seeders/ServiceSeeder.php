<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = ['Haircut', 'Hair Color', 'Manicure', 'Pedicure'];

        foreach ($services as $name) {
            Service::firstOrCreate(['name' => $name]);
        }
    }
}
