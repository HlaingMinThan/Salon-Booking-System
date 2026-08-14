<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSlotRequest;
use App\Models\Service;
use App\Models\Slot;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SlotController extends Controller
{
    public function index(): Response
    {
        $slots = Slot::with('service')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->map(fn (Slot $slot) => [
                'id' => $slot->id,
                'service_name' => $slot->service->name,
                'date' => $slot->date->format('Y-m-d'),
                'start_time' => $slot->start_time,
                'is_available' => $slot->is_available,
            ]);

        return Inertia::render('admin/slots/Index', [
            'slots' => $slots,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/slots/Create', [
            'services' => Service::all(['id', 'name']),
        ]);
    }

    public function store(StoreSlotRequest $request): RedirectResponse
    {
        Slot::create($request->validated());

        return redirect()->route('admin.slots.create')
            ->with('success', 'Slot created successfully.');
    }

    public function destroy(Slot $slot): RedirectResponse
    {
        $slot->delete();

        return redirect()->route('admin.slots.index')
            ->with('success', 'Slot deleted.');
    }
}
