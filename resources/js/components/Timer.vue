<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    seconds:   { type: Number, required: true },
    autoStart: { type: Boolean, default: true },
    label:     { type: String, default: 'Time remaining' },
});
const emit = defineEmits(['expire', 'tick']);

const remaining = ref(props.seconds);
let   timer     = null;

const formatted = computed(() => {
    const m = Math.floor(remaining.value / 60).toString().padStart(2, '0');
    const s = (remaining.value % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
});

const isWarning = computed(() => remaining.value <= 30 && remaining.value > 10);
const isDanger  = computed(() => remaining.value <= 10);
const pct       = computed(() => Math.max(0, (remaining.value / props.seconds) * 100));

function start() {
    timer = setInterval(() => {
        if (remaining.value <= 0) {
            clearInterval(timer);
            emit('expire');
            return;
        }
        remaining.value--;
        emit('tick', remaining.value);
    }, 1000);
}

onMounted(() => { if (props.autoStart) start(); });
onUnmounted(() => clearInterval(timer));

watch(() => props.seconds, (v) => { remaining.value = v; });

defineExpose({ start, stop: () => clearInterval(timer), remaining });
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="text-xs text-gray-500 hidden sm:block">{{ label }}</div>
        <div
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-mono font-semibold text-sm transition-colors"
            :class="isDanger  ? 'bg-red-100 text-red-700 animate-pulse' :
                    isWarning ? 'bg-amber-100 text-amber-700' :
                                'bg-gray-100 text-gray-700'"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                <path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/>
            </svg>
            {{ formatted }}
        </div>
    </div>
</template>
