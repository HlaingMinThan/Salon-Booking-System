<?php

use App\Models\Booking;
use App\Models\User;

test('admin can view all bookings', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);
    Booking::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('admin.bookings.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/bookings/Index')
        ->has('bookings', 3)
    );
});

test('non-admin users cannot view bookings', function () {
    $user = User::factory()->create(['email' => 'other@example.com']);

    $response = $this->actingAs($user)->get(route('admin.bookings.index'));

    $response->assertForbidden();
});

test('guests cannot view bookings', function () {
    $response = $this->get(route('admin.bookings.index'));

    $response->assertRedirect(route('login'));
});

test('admin can approve a booking', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);
    $booking = Booking::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($admin)->patch(route('admin.bookings.approve', $booking));

    $response->assertRedirect(route('admin.bookings.index'));
    expect($booking->fresh()->status)->toBe('approved');
});

test('admin can reject a booking and slot reopens', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);
    $booking = Booking::factory()->create(['status' => 'pending']);

    expect($booking->slot->is_available)->toBeFalse();

    $response = $this->actingAs($admin)->patch(route('admin.bookings.reject', $booking));

    $response->assertRedirect(route('admin.bookings.index'));
    expect($booking->fresh()->status)->toBe('rejected');
    expect($booking->slot->fresh()->is_available)->toBeTrue();
});

test('non-admin cannot approve bookings', function () {
    $user = User::factory()->create(['email' => 'other@example.com']);
    $booking = Booking::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($user)->patch(route('admin.bookings.approve', $booking));

    $response->assertForbidden();
});
