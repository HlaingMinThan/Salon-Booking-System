<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
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
import {
    store,
    update,
    destroy,
} from '@/actions/App/Http/Controllers/Admin/ServiceController'
import { toast } from 'vue-sonner'
import { ref } from 'vue'
import { Pencil, Trash2 } from '@lucide/vue'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Services', href: '/admin/services' },
        ],
    },
})

interface ServiceItem {
    id: number
    name: string
    slots_count: number
}

const props = defineProps<{
    services: ServiceItem[]
}>()

const createForm = useForm({ name: '' })
const editForm = useForm({ name: '' })
const showEditDialog = ref(false)
const editingService = ref<ServiceItem | null>(null)

function createService() {
    createForm.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset()
            toast.success('Service created.')
        },
    })
}

function openEdit(service: ServiceItem) {
    editingService.value = service
    editForm.name = service.name
    showEditDialog.value = true
}

function updateService() {
    if (!editingService.value) return
    editForm.put(update.url(editingService.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditDialog.value = false
            toast.success('Service updated.')
        },
    })
}

function deleteService(service: ServiceItem) {
    if (!confirm(`Delete "${service.name}"? This will also delete all its slots and bookings.`)) return
    router.delete(destroy.url(service.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Service deleted.'),
    })
}
</script>

<template>
    <Head title="Services" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Card class="mx-auto w-full max-w-2xl border-border bg-card">
            <CardHeader>
                <CardTitle>Services</CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
                <form @submit.prevent="createService" class="flex gap-2">
                    <div class="flex-1">
                        <Input
                            v-model="createForm.name"
                            placeholder="New service name"
                            required
                        />
                        <p v-if="createForm.errors.name" class="mt-1 text-sm text-destructive">
                            {{ createForm.errors.name }}
                        </p>
                    </div>
                    <Button type="submit" :disabled="createForm.processing">
                        Add
                    </Button>
                </form>

                <div v-if="services.length === 0" class="py-8 text-center text-muted-foreground">
                    No services yet.
                </div>
                <div v-else class="divide-y divide-border">
                    <div
                        v-for="service in services"
                        :key="service.id"
                        class="flex items-center justify-between py-3"
                    >
                        <div>
                            <span class="font-medium text-foreground">{{ service.name }}</span>
                            <span class="ml-2 text-xs text-muted-foreground">
                                {{ service.slots_count }} slot{{ service.slots_count !== 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="flex gap-1">
                            <Button
                                variant="ghost"
                                size="sm"
                                @click="openEdit(service)"
                            >
                                <Pencil class="size-4" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="sm"
                                class="text-destructive hover:text-destructive"
                                @click="deleteService(service)"
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>

    <Dialog v-model:open="showEditDialog">
        <DialogContent class="sm:max-w-sm">
            <DialogHeader>
                <DialogTitle>Edit Service</DialogTitle>
                <DialogDescription>Update the service name.</DialogDescription>
            </DialogHeader>
            <form @submit.prevent="updateService" class="space-y-4">
                <div class="space-y-2">
                    <Label for="edit_name">Name</Label>
                    <Input id="edit_name" v-model="editForm.name" required />
                    <p v-if="editForm.errors.name" class="text-sm text-destructive">
                        {{ editForm.errors.name }}
                    </p>
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="editForm.processing" class="w-full">
                        Save
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
