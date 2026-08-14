<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Hlaing Min Than',
            'email' => 'hlaingminthan@gmail.com',
        ]);

        // Services
        $this->call(ServiceSeeder::class);
        $services = Service::all();

        // Create slots for the next 7 days
        foreach ($services as $service) {
            for ($day = 0; $day < 7; $day++) {
                $date = now()->addDays($day)->format('Y-m-d');
                $times = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];

                foreach ($times as $time) {
                    Slot::create([
                        'service_id' => $service->id,
                        'date' => $date,
                        'start_time' => $time,
                        'is_available' => true,
                    ]);
                }
            }
        }

        // Create a few sample bookings
        $slots = Slot::where('is_available', true)->inRandomOrder()->limit(5)->get();
        foreach ($slots as $slot) {
            $slot->update(['is_available' => false]);
            Booking::create([
                'slot_id' => $slot->id,
                'customer_name' => fake()->name(),
                'phone' => fake()->numerify('09#########'),
                'status' => fake()->randomElement(['pending', 'pending', 'approved']),
            ]);
        }
    }
}
