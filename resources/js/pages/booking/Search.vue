<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { home } from '@/routes'
import { search } from '@/routes/booking'
import { Search } from '@lucide/vue'

interface BookingItem {
    id: number
    customer_name: string
    phone: string
    status: string
    date: string
    start_time: string
    service_name: string
}

const props = defineProps<{
    phone: string
    bookings: BookingItem[]
}>()

const phoneInput = ref(props.phone)

function searchBookings() {
    if (!phoneInput.value.trim()) return
    router.get(search.url({ query: { phone: phoneInput.value.trim() } }), {}, { preserveState: true })
}

function formatDate(dateStr: string): string {
    const date = new Date(dateStr + 'T00:00:00')
    return date.toLocaleDateString('en-US', {
        weekday: 'short',
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
    <Head title="Check My Booking" />

    <div class="min-h-screen bg-background">
        <header class="border-b border-border bg-card/50 backdrop-blur-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <h1 class="text-xl font-bold text-primary">Salon Booking</h1>
                <nav>
                    <Link
                        :href="home.url()"
                        class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                    >
                        Book Appointment
                    </Link>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-6 py-10">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-foreground">Check My Booking</h2>
                <p class="mt-2 text-muted-foreground">
                    Enter your phone number to find your bookings
                </p>
            </div>

            <Card class="border-border bg-card">
                <CardContent class="pt-6">
                    <form @submit.prevent="searchBookings" class="flex gap-2">
                        <div class="flex-1">
                            <Label for="phone" class="sr-only">Phone Number</Label>
                            <Input
                                id="phone"
                                v-model="phoneInput"
                                placeholder="Enter your phone number"
                                required
                            />
                        </div>
                        <Button type="submit">
                            <Search class="mr-2 size-4" />
                            Search
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <div v-if="phone" class="mt-6 space-y-4">
                <div v-if="bookings.length === 0" class="py-12 text-center">
                    <p class="text-muted-foreground">No bookings found for this phone number.</p>
                    <Button as-child variant="outline" class="mt-4">
                        <Link :href="home.url()">Book an Appointment</Link>
                    </Button>
                </div>

                <Card
                    v-for="booking in bookings"
                    :key="booking.id"
                    class="border-border bg-card"
                >
                    <CardContent class="pt-6">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <p class="font-semibold text-foreground">{{ booking.service_name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ formatDate(booking.date) }} at {{ booking.start_time.slice(0, 5) }}
                                </p>
                                <p class="text-sm text-muted-foreground">{{ booking.customer_name }}</p>
                            </div>
                            <span
                                :class="[
                                    'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize',
                                    statusColor(booking.status),
                                ]"
                            >
                                {{ booking.status }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </main>
    </div>
</template>
