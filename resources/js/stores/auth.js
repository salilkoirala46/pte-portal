import { defineStore } from 'pinia';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
    const page = usePage();

    const user         = computed(() => page.props.auth?.user);
    const tenant       = computed(() => page.props.tenant);
    const isLoggedIn   = computed(() => !!user.value);
    const isSuperAdmin = computed(() => user.value?.role === 'super_admin');
    const isTenantAdmin= computed(() => user.value?.role === 'tenant_admin');
    const isStudent    = computed(() => user.value?.role === 'student');
    const isSubscribed = computed(() => user.value?.has_subscription ?? false);
    const subscription = computed(() => user.value?.subscription);

    return {
        user,
        tenant,
        isLoggedIn,
        isSuperAdmin,
        isTenantAdmin,
        isStudent,
        isSubscribed,
        subscription,
    };
});
