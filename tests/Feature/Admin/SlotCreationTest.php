<?php

use App\Models\Service;
use App\Models\Slot;
use App\Models\User;

test('admin can view slot creation page', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);

    $response = $this->actingAs($admin)->get(route('admin.slots.create'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/slots/Create'));
});

test('admin can create a slot', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);
    $service = Service::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.slots.store'), [
        'service_id' => $service->id,
        'date' => now()->addDay()->format('Y-m-d'),
        'start_time' => '10:00',
    ]);

    $response->assertRedirect(route('admin.slots.create'));
    $this->assertDatabaseHas('slots', [
        'service_id' => $service->id,
    ]);
});

test('non-admin users cannot access slot creation', function () {
    $user = User::factory()->create(['email' => 'other@example.com']);

    $response = $this->actingAs($user)->get(route('admin.slots.create'));

    $response->assertForbidden();
});

test('guests cannot access slot creation', function () {
    $response = $this->get(route('admin.slots.create'));

    $response->assertRedirect(route('login'));
});

test('duplicate slot cannot be created', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);
    $service = Service::factory()->create();
    $date = now()->addDay()->format('Y-m-d');

    Slot::factory()->create([
        'service_id' => $service->id,
        'date' => $date,
        'start_time' => '10:00',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.slots.store'), [
        'service_id' => $service->id,
        'date' => $date,
        'start_time' => '10:00',
    ]);

    $response->assertSessionHasErrors('start_time');
});

test('slot date must be today or future', function () {
    $admin = User::factory()->create(['email' => 'hlaingminthan@gmail.com']);
    $service = Service::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.slots.store'), [
        'service_id' => $service->id,
        'date' => now()->subDay()->format('Y-m-d'),
        'start_time' => '10:00',
    ]);

    $response->assertSessionHasErrors('date');
});
