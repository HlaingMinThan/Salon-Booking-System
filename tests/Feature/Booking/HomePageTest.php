<?php

use App\Models\Service;
use App\Models\Slot;

test('guests can view the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Home'));
});

test('home page shows available dates', function () {
    $service = Service::factory()->create();
    Slot::factory()->create([
        'service_id' => $service->id,
        'date' => now()->addDay()->format('Y-m-d'),
        'start_time' => '10:00',
        'is_available' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
        ->component('Home')
        ->has('availableDates', 1)
    );
});

test('home page shows slots grouped by service for a selected date', function () {
    $service = Service::factory()->create(['name' => 'Haircut']);
    $date = now()->addDay()->format('Y-m-d');
    Slot::factory()->create([
        'service_id' => $service->id,
        'date' => $date,
        'start_time' => '10:00',
        'is_available' => true,
    ]);

    $response = $this->get(route('home', ['date' => $date]));

    $response->assertInertia(fn ($page) => $page
        ->component('Home')
        ->where('selectedDate', $date)
        ->has('slotsByService', 1)
        ->where('slotsByService.0.name', 'Haircut')
        ->has('slotsByService.0.slots', 1)
    );
});

test('booked slots are not shown', function () {
    $service = Service::factory()->create();
    $date = now()->addDay()->format('Y-m-d');
    Slot::factory()->create([
        'service_id' => $service->id,
        'date' => $date,
        'start_time' => '10:00',
        'is_available' => false,
    ]);

    $response = $this->get(route('home', ['date' => $date]));

    $response->assertInertia(fn ($page) => $page
        ->where('slotsByService.0.slots', [])
    );
});

test('past dates are excluded from available dates', function () {
    $service = Service::factory()->create();
    Slot::factory()->create([
        'service_id' => $service->id,
        'date' => now()->subDay()->format('Y-m-d'),
        'start_time' => '10:00',
        'is_available' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertInertia(fn ($page) => $page
        ->has('availableDates', 0)
    );
});
