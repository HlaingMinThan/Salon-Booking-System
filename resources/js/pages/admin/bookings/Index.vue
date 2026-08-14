<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { approve, reject } from '@/actions/App/Http/Controllers/Admin/BookingController'
import { toast } from 'vue-sonner'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Bookings', href: '/admin/bookings' },
        ],
    },
})

interface BookingItem {
    id: number
    customer_name: string
    phone: string
    status: string
    date: string
    start_time: string
    service_name: string
    created_at: string
}

defineProps<{
    bookings: BookingItem[]
}>()

function statusColor(status: string): string {
    switch (status) {
        case 'approved':
            return 'bg-green-500/15 text-green-400'
        case 'rejected':
            return 'bg-red-500/15 text-red-400'
        default:
            return 'bg-yellow-500/15 text-yellow-400'
    }
}

function approveBooking(bookingId: number) {
    router.patch(approve.url(bookingId), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Booking approved.'),
    })
}

function rejectBooking(bookingId: number) {
    router.patch(reject.url(bookingId), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Booking rejected. Slot reopened.'),
    })
}
</script>

<template>
    <Head title="Bookings" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Card class="border-border bg-card">
            <CardHeader>
                <CardTitle>All Bookings</CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="bookings.length === 0" class="py-8 text-center text-muted-foreground">
                    No bookings yet.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left">
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Customer</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Phone</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Service</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Date</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Time</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Status</th>
                                <th class="pb-3 font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="booking in bookings"
                                :key="booking.id"
                                class="border-b border-border/50 last:border-0"
                            >
                                <td class="py-3 pr-4 text-foreground">{{ booking.customer_name }}</td>
                                <td class="py-3 pr-4 text-foreground">{{ booking.phone }}</td>
                                <td class="py-3 pr-4 text-foreground">{{ booking.service_name }}</td>
                                <td class="py-3 pr-4 text-foreground">{{ booking.date }}</td>
                                <td class="py-3 pr-4 font-mono text-foreground">{{ booking.start_time.slice(0, 5) }}</td>
                                <td class="py-3 pr-4">
                                    <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', statusColor(booking.status)]">
                                        {{ booking.status }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div v-if="booking.status === 'pending'" class="flex gap-2">
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            class="border-green-500/30 text-green-400 hover:bg-green-500/15"
                                            @click="approveBooking(booking.id)"
                                        >
                                            Approve
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            class="border-red-500/30 text-red-400 hover:bg-red-500/15"
                                            @click="rejectBooking(booking.id)"
                                        >
                                            Reject
                                        </Button>
                                    </div>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
