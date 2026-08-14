<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { home } from '@/routes'
import { CheckCircle } from '@lucide/vue'

defineProps<{
    booking: {
        id: number
        customer_name: string
        phone: string
        status: string
        date: string
        start_time: string
        service_name: string
    }
}>()

function formatDate(dateStr: string): string {
    const date = new Date(dateStr + 'T00:00:00')
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

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
</script>

<template>
    <Head title="Booking Confirmed" />

    <div class="flex min-h-screen items-center justify-center bg-background px-4">
        <Card class="w-full max-w-md border-border bg-card">
            <CardHeader class="text-center">
                <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-full bg-primary/15">
                    <CheckCircle class="size-8 text-primary" />
                </div>
                <CardTitle class="text-xl">Booking Submitted!</CardTitle>
                <p class="mt-1 text-sm text-muted-foreground">
                    Your appointment request has been received
                </p>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="rounded-lg bg-muted/50 p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Service</span>
                        <span class="text-sm font-medium text-foreground">{{ booking.service_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Date</span>
                        <span class="text-sm font-medium text-foreground">{{ formatDate(booking.date) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Time</span>
                        <span class="text-sm font-medium text-foreground">{{ booking.start_time.slice(0, 5) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Name</span>
                        <span class="text-sm font-medium text-foreground">{{ booking.customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Phone</span>
                        <span class="text-sm font-medium text-foreground">{{ booking.phone }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Status</span>
                        <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', statusColor(booking.status)]">
                            {{ booking.status }}
                        </span>
                    </div>
                </div>

                <Button as-child class="w-full">
                    <Link :href="home.url()">Book Another Appointment</Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
