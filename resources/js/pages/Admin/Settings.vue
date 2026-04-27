<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ tenant: Object });

const form = useForm({
    name:          props.tenant?.name ?? '',
    email:         props.tenant?.email ?? '',
    phone:         props.tenant?.phone ?? '',
    address:       props.tenant?.address ?? '',
    primary_color: props.tenant?.primary_color ?? '#2563eb',
    timezone:      props.tenant?.timezone ?? 'Australia/Sydney',
});
</script>

<template>
    <div class="max-w-2xl space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Institution Settings</h1>

        <form @submit.prevent="form.put(route('admin.settings.update'))" class="card p-6 space-y-5">
            <div>
                <label class="form-label">Institution Name *</label>
                <input v-model="form.name" type="text" required class="form-input"/>
                <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Contact Email *</label>
                    <input v-model="form.email" type="email" required class="form-input"/>
                </div>
                <div>
                    <label class="form-label">Phone</label>
                    <input v-model="form.phone" type="tel" class="form-input"/>
                </div>
            </div>
            <div>
                <label class="form-label">Address</label>
                <textarea v-model="form.address" rows="2" class="form-input"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Brand Colour</label>
                    <div class="flex items-center gap-3">
                        <input v-model="form.primary_color" type="color" class="h-9 w-16 rounded border border-gray-200 cursor-pointer"/>
                        <input v-model="form.primary_color" type="text" class="form-input flex-1" pattern="^#[0-9A-Fa-f]{6}$"/>
                    </div>
                </div>
                <div>
                    <label class="form-label">Timezone</label>
                    <select v-model="form.timezone" class="form-input">
                        <option v-for="tz in ['Australia/Sydney','Australia/Melbourne','UTC','Asia/Kolkata','Asia/Kathmandu','Asia/Dhaka','Asia/Manila','Pacific/Auckland']"
                                :key="tz" :value="tz">{{ tz }}</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" :disabled="form.processing" class="btn-primary px-8">
                    {{ form.processing ? 'Saving…' : 'Save Settings' }}
                </button>
            </div>
        </form>
    </div>
</template>
