<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    plans:               Array,
    currentSubscription: Object,
});

const subscribing = ref(null);

function subscribe(plan) {
    subscribing.value = plan.id;
    router.post(route('student.subscription.subscribe', plan.id), {}, {
        onFinish: () => { subscribing.value = null; },
    });
}

function cancel() {
    if (!confirm('Are you sure you want to cancel your subscription?')) return;
    router.delete(route('student.subscription.cancel'));
}
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">Choose your plan</h1>
            <p class="mt-2 text-gray-500">Unlock all PTE practice modules with a subscription</p>
        </div>

        <!-- Current subscription notice -->
        <div v-if="currentSubscription" class="card p-5 border-emerald-200 bg-emerald-50">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-emerald-800">
                        Active: {{ currentSubscription.plan?.name }}
                    </p>
                    <p class="text-sm text-emerald-600 mt-0.5">
                        <span class="capitalize">{{ currentSubscription.status }}</span>
                        <span v-if="currentSubscription.ends_at">
                            · Renews {{ new Date(currentSubscription.ends_at).toLocaleDateString() }}
                        </span>
                    </p>
                </div>
                <button @click="cancel" class="btn-secondary btn-sm text-red-600 border-red-200 hover:bg-red-50">
                    Cancel
                </button>
            </div>
        </div>

        <!-- Plans grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div
                v-for="plan in plans"
                :key="plan.id"
                class="card p-6 relative"
                :class="plan.is_featured ? 'border-primary-400 border-2 shadow-lg' : ''"
            >
                <!-- Featured badge -->
                <div v-if="plan.is_featured"
                     class="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                    Most Popular
                </div>

                <div class="space-y-4">
                    <!-- Name & price -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ plan.name }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ plan.description }}</p>
                    </div>

                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold text-gray-900">{{ plan.currency }} {{ plan.price }}</span>
                        <span class="text-gray-400 text-sm">/ {{ plan.interval }}</span>
                    </div>

                    <p v-if="plan.trial_days > 0" class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full inline-block">
                        {{ plan.trial_days }}-day free trial
                    </p>

                    <!-- Features -->
                    <ul class="space-y-2">
                        <li v-for="feature in (plan.features ?? [])" :key="feature" class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ feature }}
                        </li>
                    </ul>

                    <!-- CTA -->
                    <button
                        v-if="!currentSubscription"
                        @click="subscribe(plan)"
                        :disabled="subscribing === plan.id"
                        class="w-full btn-lg"
                        :class="plan.is_featured ? 'btn-primary' : 'btn-secondary'"
                    >
                        <span v-if="subscribing === plan.id">Processing…</span>
                        <span v-else>
                            {{ plan.trial_days > 0 ? `Start ${plan.trial_days}-day trial` : 'Subscribe now' }}
                        </span>
                    </button>
                    <p v-else-if="currentSubscription.plan_id === plan.id"
                       class="text-sm text-center text-emerald-600 font-medium py-2">
                        ✓ Your current plan
                    </p>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400">
            Cancel anytime. No credit card required for trial.
        </p>
    </div>
</template>
