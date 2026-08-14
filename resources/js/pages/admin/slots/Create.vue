<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { store } from '@/actions/App/Http/Controllers/Admin/SlotController'
import admin from '@/routes/admin'
import { dashboard } from '@/routes'
import { toast } from 'vue-sonner'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Create Slot', href: '/admin/slots/create' },
        ],
    },
})

defineProps<{
    services: { id: number; name: string }[]
}>()

const form = useForm({
    service_id: '',
    date: '',
    start_time: '',
})

function submit() {
    form.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            toast.success('Slot created successfully.')
        },
    })
}
</script>

<template>
    <Head title="Create Slot" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Card class="mx-auto w-full max-w-lg border-border bg-card">
            <CardHeader>
                <CardTitle>Create Time Slot</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="service_id">Service</Label>
                        <select
                            id="service_id"
                            v-model="form.service_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            required
                        >
                            <option value="" disabled>Select a service</option>
                            <option
                                v-for="service in services"
                                :key="service.id"
                                :value="service.id"
                            >
                                {{ service.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.service_id" class="text-sm text-destructive">
                            {{ form.errors.service_id }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="date">Date</Label>
                        <Input
                            id="date"
                            v-model="form.date"
                            type="date"
                            required
                        />
                        <p v-if="form.errors.date" class="text-sm text-destructive">
                            {{ form.errors.date }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="start_time">Start Time</Label>
                        <Input
                            id="start_time"
                            v-model="form.start_time"
                            type="time"
                            required
                        />
                        <p v-if="form.errors.start_time" class="text-sm text-destructive">
                            {{ form.errors.start_time }}
                        </p>
                    </div>

                    <Button type="submit" :disabled="form.processing" class="w-full">
                        <template v-if="form.processing">Creating...</template>
                        <template v-else>Create Slot</template>
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
