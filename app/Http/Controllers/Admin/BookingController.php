<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        $bookings = Booking::with('slot.service')
            ->latest()
            ->get()
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'customer_name' => $booking->customer_name,
                'phone' => $booking->phone,
                'status' => $booking->status,
                'date' => $booking->slot->date->format('Y-m-d'),
                'start_time' => $booking->slot->start_time,
                'service_name' => $booking->slot->service->name,
                'created_at' => $booking->created_at->format('M d, Y H:i'),
            ]);

        return Inertia::render('admin/bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function approve(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'approved']);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking approved.');
    }

    public function reject(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'rejected']);
        $booking->slot->update(['is_available' => true]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking rejected. Slot is now available again.');
    }
}
