import { defineStore } from 'pinia';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export const useSubscriptionStore = defineStore('subscription', () => {
    const page         = usePage();
    const subscription = computed(() => page.props.auth?.user?.subscription);
    const isActive     = computed(() => !!page.props.auth?.user?.has_subscription);
    const plan         = computed(() => subscription.value?.plan);

    const daysRemaining = computed(() => {
        if (!subscription.value?.ends_at) return null;
        const end  = new Date(subscription.value.ends_at);
        const now  = new Date();
        const diff = Math.ceil((end - now) / (1000 * 60 * 60 * 24));
        return Math.max(0, diff);
    });

    const statusLabel = computed(() => {
        if (!subscription.value) return 'No subscription';
        return {
            trial:     'Free Trial',
            active:    'Active',
            expired:   'Expired',
            cancelled: 'Cancelled',
        }[subscription.value.status] ?? 'Unknown';
    });

    return { subscription, isActive, plan, daysRemaining, statusLabel };
});
