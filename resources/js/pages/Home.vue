<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { CalendarDate, today, getLocalTimeZone } from '@internationalized/date'
import { Calendar } from '@/components/ui/calendar'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
    DialogDescription,
} from '@/components/ui/dialog'
import { home } from '@/routes'
import { store } from '@/routes/booking'
import { login } from '@/routes'

interface SlotItem {
    id: number
    start_time: string
}

interface ServiceSlots {
    id: number
    name: string
    slots: SlotItem[]
}

const props = defineProps<{
    availableDates: string[]
    selectedDate: string | null
    slotsByService: ServiceSlots[]
}>()

const selectedCalendarDate = ref(
    props.selectedDate
        ? parseToCalendarDate(props.selectedDate)
        : undefined,
)

function parseToCalendarDate(dateStr: string): CalendarDate {
    const [y, m, d] = dateStr.split('-').map(Number)
    return new CalendarDate(y, m, d)
}

const todayDate = today(getLocalTimeZone())

function isDateUnavailable(date: CalendarDate): boolean {
    return date.compare(todayDate) < 0
}

function onDateSelect(date: CalendarDate) {
    selectedCalendarDate.value = date
    const dateStr = `${date.year}-${String(date.month).padStart(2, '0')}-${String(date.day).padStart(2, '0')}`
    router.get(home.url({ query: { date: dateStr } }), {}, { preserveState: true, preserveScroll: true })
}

const showBookingDialog = ref(false)
const selectedSlot = ref<{ id: number; serviceName: string; startTime: string } | null>(null)

const bookingForm = useForm({
    customer_name: '',
    phone: '',
})

function openBookingDialog(slot: SlotItem, serviceName: string) {
    selectedSlot.value = {
        id: slot.id,
        serviceName,
        startTime: slot.start_time,
    }
    bookingForm.reset()
    showBookingDialog.value = true
}

function submitBooking() {
    if (!selectedSlot.value) return
    bookingForm.post(store.url(selectedSlot.value.id), {
        onSuccess: () => {
            showBookingDialog.value = false
        },
    })
}

const formattedSelectedDate = computed(() => {
    if (!props.selectedDate) return ''
    const date = new Date(props.selectedDate + 'T00:00:00')
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
})
</script>

<template>
    <Head title="Book an Appointment" />

    <div class="min-h-screen bg-background">
        <header class="border-b border-border bg-card/50 backdrop-blur-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <h1 class="text-xl font-bold text-primary">Salon Booking</h1>
                <nav class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="'/dashboard'"
                        class="rounded-md bg-primary/10 px-4 py-2 text-sm font-medium text-primary transition hover:bg-primary/20"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login.url()"
                            class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground"
                        >
                            Log in
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-foreground">Book Your Appointment</h2>
                <p class="mt-2 text-muted-foreground">
                    Select a date to see available services and time slots
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[auto_1fr]">
                <div>
                    <Card class="border-border bg-card">
                        <CardContent class="p-4">
                            <Calendar
                                v-model="selectedCalendarDate"
                                :is-date-unavailable="isDateUnavailable"
                                :highlighted-dates="availableDates"
                                weekday-format="short"
                                class="rounded-md"
                                @update:model-value="(v: any) => v && onDateSelect(v)"
                            />
                        </CardContent>
                    </Card>
                    <p class="mt-3 text-center text-xs text-muted-foreground">
                        <span class="inline-block size-2 rounded-full bg-primary/30"></span>
                        Highlighted dates have available slots
                    </p>
                </div>

                <div>
                    <template v-if="selectedDate">
                        <h3 class="mb-4 text-lg font-semibold text-foreground">
                            {{ formattedSelectedDate }}
                        </h3>

                        <div class="space-y-4">
                            <Card
                                v-for="service in slotsByService"
                                :key="service.id"
                                class="border-border bg-card transition-shadow hover:shadow-md"
                            >
                                <CardHeader class="pb-3">
                                    <CardTitle class="text-base font-semibold text-foreground">
                                        {{ service.name }}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div v-if="service.slots.length > 0" class="flex flex-wrap gap-2">
                                        <Button
                                            v-for="slot in service.slots"
                                            :key="slot.id"
                                            variant="outline"
                                            size="sm"
                                            class="border-primary/30 font-mono text-primary hover:bg-primary hover:text-primary-foreground"
                                            @click="openBookingDialog(slot, service.name)"
                                        >
                                            {{ slot.start_time.slice(0, 5) }}
                                        </Button>
                                    </div>
                                    <p v-else class="text-sm text-muted-foreground">
                                        No slots available
                                    </p>
                                </CardContent>
                            </Card>
                        </div>
                    </template>

                    <template v-else>
                        <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border">
                            <p class="text-muted-foreground">
                                Pick a date on the calendar to see available slots
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </div>

    <Dialog v-model:open="showBookingDialog">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Book Appointment</DialogTitle>
                <DialogDescription v-if="selectedSlot">
                    {{ selectedSlot.serviceName }} &mdash; {{ formattedSelectedDate }} at
                    {{ selectedSlot.startTime.slice(0, 5) }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitBooking" class="space-y-4">
                <div class="space-y-2">
                    <Label for="customer_name">Name</Label>
                    <Input
                        id="customer_name"
                        v-model="bookingForm.customer_name"
                        placeholder="Your full name"
                        required
                    />
                    <p v-if="bookingForm.errors.customer_name" class="text-sm text-destructive">
                        {{ bookingForm.errors.customer_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        v-model="bookingForm.phone"
                        placeholder="Your phone number"
                        required
                    />
                    <p v-if="bookingForm.errors.phone" class="text-sm text-destructive">
                        {{ bookingForm.errors.phone }}
                    </p>
                </div>

                <DialogFooter>
                    <Button
                        type="submit"
                        :disabled="bookingForm.processing"
                        class="w-full"
                    >
                        <template v-if="bookingForm.processing">Booking...</template>
                        <template v-else>Confirm Booking</template>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
