<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\Slot;

test('guest can book an available slot', function () {
    $service = Service::factory()->create();
    $slot = Slot::factory()->create([
        'service_id' => $service->id,
        'date' => now()->addDay()->format('Y-m-d'),
        'start_time' => '10:00',
        'is_available' => true,
    ]);

    $response = $this->post(route('booking.store', $slot), [
        'customer_name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('bookings', [
        'slot_id' => $slot->id,
        'customer_name' => 'John Doe',
        'phone' => '1234567890',
        'status' => 'pending',
    ]);

    expect($slot->fresh()->is_available)->toBeFalse();
});

test('booking requires customer name and phone', function () {
    $slot = Slot::factory()->create(['is_available' => true]);

    $response = $this->post(route('booking.store', $slot), []);

    $response->assertSessionHasErrors(['customer_name', 'phone']);
});

test('cannot book an unavailable slot', function () {
    $slot = Slot::factory()->booked()->create();

    $response = $this->post(route('booking.store', $slot), [
        'customer_name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $response->assertStatus(422);
    $this->assertDatabaseMissing('bookings', ['slot_id' => $slot->id]);
});

test('guest can view booking confirmation', function () {
    $booking = Booking::factory()->create();

    $response = $this->get(route('booking.confirmation', $booking));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('booking/Confirmation')
        ->has('booking')
        ->where('booking.customer_name', $booking->customer_name)
    );
});
