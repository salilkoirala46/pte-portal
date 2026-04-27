<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps({ plans: Array });

const showCreate = ref(false);

const form = useForm({
    name:                 '',
    description:          '',
    price:                '',
    currency:             'AUD',
    interval:             'monthly',
    trial_days:           7,
    is_featured:          false,
    features:             [],
    max_attempts_per_day: 20,
    includes_mock_test:   false,
    includes_ai_feedback: false,
});

const featureInput = ref('');
function addFeature() {
    if (featureInput.value.trim()) {
        form.features.push(featureInput.value.trim());
        featureInput.value = '';
    }
}

function submit() {
    form.post(route('admin.plans.store'), { onSuccess: () => { form.reset(); showCreate.value = false; } });
}

function deactivate(plan) {
    if (!confirm('Deactivate this plan?')) return;
    router.delete(route('admin.plans.destroy', plan.id));
}
</script>

<template>
    <div class="space-y-6 max-w-4xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Subscription Plans</h1>
            <button @click="showCreate = !showCreate" class="btn-primary gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Plan
            </button>
        </div>

        <!-- Create form -->
        <div v-if="showCreate" class="card p-6 border-primary-200 space-y-4">
            <h2 class="font-semibold text-gray-800">Create Plan</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Name *</label>
                    <input v-model="form.name" type="text" required class="form-input" placeholder="e.g. Pro Plan"/>
                </div>
                <div>
                    <label class="form-label">Price *</label>
                    <div class="flex gap-2">
                        <select v-model="form.currency" class="form-input w-24">
                            <option>AUD</option><option>USD</option><option>GBP</option>
                        </select>
                        <input v-model="form.price" type="number" min="0" step="0.01" required class="form-input flex-1" placeholder="29.99"/>
                    </div>
                </div>
                <div>
                    <label class="form-label">Billing Interval</label>
                    <select v-model="form.interval" class="form-input">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Trial Days</label>
                    <input v-model="form.trial_days" type="number" min="0" class="form-input"/>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Description</label>
                    <input v-model="form.description" type="text" class="form-input"/>
                </div>
                <div class="col-span-2">
                    <label class="form-label">Features</label>
                    <div class="flex gap-2 mb-2">
                        <input v-model="featureInput" type="text" class="form-input flex-1" placeholder="Add a feature…" @keyup.enter="addFeature"/>
                        <button type="button" @click="addFeature" class="btn-secondary btn-sm">Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(f, i) in form.features" :key="i" class="bg-primary-100 text-primary-700 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
                            {{ f }} <button @click="form.features.splice(i,1)" class="ml-1 hover:text-red-600">✕</button>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" v-model="form.is_featured" class="accent-primary-600"/> Featured plan
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" v-model="form.includes_mock_test" class="accent-primary-600"/> Include mock test
                    </label>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button @click="showCreate = false" class="btn-secondary">Cancel</button>
                <button @click="submit" :disabled="form.processing" class="btn-primary">Create Plan</button>
            </div>
        </div>

        <!-- Plans list -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div v-for="plan in plans" :key="plan.id" class="card p-5 space-y-3" :class="{ 'border-primary-300': plan.is_featured }">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900">{{ plan.name }}</h3>
                            <span v-if="plan.is_featured" class="badge bg-primary-100 text-primary-700">Featured</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ plan.currency }} {{ plan.price }}<span class="text-sm font-normal text-gray-400"> / {{ plan.interval }}</span></p>
                    </div>
                    <button @click="deactivate(plan)" class="btn-secondary btn-sm text-red-600">Deactivate</button>
                </div>
                <div class="text-xs text-gray-500">{{ plan.subscriptions_count }} active subscribers</div>
                <ul class="space-y-1">
                    <li v-for="f in (plan.features ?? [])" :key="f" class="text-xs text-gray-600 flex items-center gap-1.5">
                        <span class="text-emerald-500">✓</span> {{ f }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
