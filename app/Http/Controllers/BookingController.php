<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Slot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(Request $request): Response
    {
        $selectedDate = $request->query('date');

        $availableDates = Slot::where('is_available', true)
            ->whereDate('date', '>=', now()->toDateString())
            ->distinct()
            ->pluck('date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->values()
            ->all();

        $slotsByService = [];

        if ($selectedDate) {
            $services = Service::all();

            foreach ($services as $service) {
                $slots = $service->slots()
                    ->whereDate('date', $selectedDate)
                    ->where('is_available', true)
                    ->orderBy('start_time')
                    ->get(['id', 'start_time']);

                $slotsByService[] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slots' => $slots,
                ];
            }
        }

        return Inertia::render('Home', [
            'availableDates' => $availableDates,
            'selectedDate' => $selectedDate,
            'slotsByService' => $slotsByService,
        ]);
    }

    public function store(StoreBookingRequest $request, Slot $slot): RedirectResponse
    {
        $booking = DB::transaction(function () use ($request, $slot) {
            $slot = Slot::lockForUpdate()->findOrFail($slot->id);

            if (! $slot->is_available) {
                abort(422, 'This slot is no longer available.');
            }

            $slot->update(['is_available' => false]);

            return Booking::create([
                'slot_id' => $slot->id,
                'customer_name' => $request->validated('customer_name'),
                'phone' => $request->validated('phone'),
                'status' => 'pending',
            ]);
        });

        return redirect()->route('booking.confirmation', $booking);
    }

    public function show(Booking $booking): Response
    {
        $booking->load('slot.service');

        return Inertia::render('booking/Confirmation', [
            'booking' => [
                'id' => $booking->id,
                'customer_name' => $booking->customer_name,
                'phone' => $booking->phone,
                'status' => $booking->status,
                'date' => $booking->slot->date->format('Y-m-d'),
                'start_time' => $booking->slot->start_time,
                'service_name' => $booking->slot->service->name,
            ],
        ]);
    }
}
