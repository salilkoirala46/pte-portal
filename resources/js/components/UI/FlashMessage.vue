<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page    = usePage();
const visible = ref(false);
const current = ref(null);

const flash = computed(() => page.props.flash);

watch(flash, (f) => {
    const msg = f?.success || f?.error || f?.warning || f?.info;
    if (msg) {
        current.value = {
            message: msg,
            type: f.success ? 'success' : f.error ? 'error' : f.warning ? 'warning' : 'info',
        };
        visible.value = true;
        setTimeout(() => { visible.value = false; }, 4000);
    }
}, { immediate: true });

const styles = computed(() => ({
    success: 'bg-emerald-50 border-emerald-400 text-emerald-800',
    error:   'bg-red-50 border-red-400 text-red-800',
    warning: 'bg-amber-50 border-amber-400 text-amber-800',
    info:    'bg-blue-50 border-blue-400 text-blue-800',
}[current.value?.type] ?? ''));

const icons = {
    success: '✓',
    error:   '✕',
    warning: '⚠',
    info:    'ℹ',
};
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0"
        leave-active-class="transition duration-200 ease-in"
        leave-to-class="transform -translate-y-4 opacity-0"
    >
        <div v-if="visible && current" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4">
            <div :class="['flex items-start gap-3 p-4 rounded-xl border shadow-lg', styles]">
                <span class="font-bold text-lg leading-none">{{ icons[current.type] }}</span>
                <p class="text-sm font-medium flex-1">{{ current.message }}</p>
                <button @click="visible = false" class="text-current opacity-60 hover:opacity-100 ml-1">✕</button>
            </div>
        </div>
    </Transition>
</template>
