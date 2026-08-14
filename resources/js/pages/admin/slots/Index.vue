<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { destroy } from '@/actions/App/Http/Controllers/Admin/SlotController'
import { toast } from 'vue-sonner'
import { Trash2 } from '@lucide/vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Slots', href: '/admin/slots' },
        ],
    },
})

interface SlotItem {
    id: number
    service_name: string
    date: string
    start_time: string
    is_available: boolean
}

defineProps<{
    slots: SlotItem[]
}>()

function deleteSlot(slot: SlotItem) {
    if (!confirm('Delete this slot?')) return
    router.delete(destroy.url(slot.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Slot deleted.'),
    })
}
</script>

<template>
    <Head title="Slots" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Card class="border-border bg-card">
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle>All Slots</CardTitle>
                <Button as-child size="sm">
                    <Link href="/admin/slots/create">Create Slot</Link>
                </Button>
            </CardHeader>
            <CardContent>
                <div v-if="slots.length === 0" class="py-8 text-center text-muted-foreground">
                    No slots yet.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left">
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Service</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Date</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Time</th>
                                <th class="pb-3 pr-4 font-medium text-muted-foreground">Status</th>
                                <th class="pb-3 font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="slot in slots"
                                :key="slot.id"
                                class="border-b border-border/50 last:border-0"
                            >
                                <td class="py-3 pr-4 text-foreground">{{ slot.service_name }}</td>
                                <td class="py-3 pr-4 text-foreground">{{ slot.date }}</td>
                                <td class="py-3 pr-4 font-mono text-foreground">{{ slot.start_time.slice(0, 5) }}</td>
                                <td class="py-3 pr-4">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            slot.is_available
                                                ? 'bg-green-500/15 text-green-400'
                                                : 'bg-red-500/15 text-red-400',
                                        ]"
                                    >
                                        {{ slot.is_available ? 'Available' : 'Booked' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="deleteSlot(slot)"
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
