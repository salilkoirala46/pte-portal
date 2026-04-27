<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ExamTimer from '@/components/Timer.vue';

const props = defineProps({
    question:     { type: Object, required: true },
    attempt:      { type: Object, required: true },
    module:       { type: String, required: true },
    instructions: { type: String, default: '' },
    timeLimit:    { type: Number, default: 0 },
});

const emit = defineEmits(['time-up']);

const moduleColors = {
    speaking:  { bg: 'bg-purple-600', light: 'bg-purple-50', text: 'text-purple-700', border: 'border-purple-200' },
    reading:   { bg: 'bg-cyan-600',   light: 'bg-cyan-50',   text: 'text-cyan-700',   border: 'border-cyan-200' },
    writing:   { bg: 'bg-emerald-600',light: 'bg-emerald-50',text: 'text-emerald-700',border: 'border-emerald-200' },
    listening: { bg: 'bg-amber-500',  light: 'bg-amber-50',  text: 'text-amber-700',  border: 'border-amber-200' },
};

const colors = computed(() => moduleColors[props.module] || moduleColors.reading);

const backRoutes = {
    speaking:  'student.speaking.index',
    reading:   'student.reading.index',
    writing:   'student.writing.index',
    listening: 'student.listening.index',
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Exam header bar -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between gap-4">
                <!-- Back + module label -->
                <div class="flex items-center gap-3">
                    <Link :href="route(backRoutes[module])" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </Link>
                    <span :class="[colors.light, colors.text, colors.border, 'border px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide']">
                        {{ module }}
                    </span>
                    <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ question.question_type?.name }}</span>
                </div>

                <!-- Timer -->
                <ExamTimer v-if="timeLimit > 0" :seconds="timeLimit" @expire="$emit('time-up')" />

                <!-- Status -->
                <div class="text-xs text-gray-500">
                    Attempt #{{ attempt.id }}
                </div>
            </div>
        </div>

        <!-- Instructions banner -->
        <div v-if="instructions" class="bg-blue-50 border-b border-blue-200">
            <div class="max-w-5xl mx-auto px-4 py-3">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Instructions:</span> {{ instructions }}
                </p>
            </div>
        </div>

        <!-- Main content -->
        <main class="max-w-5xl mx-auto px-4 py-6">
            <slot />
        </main>
    </div>
</template>
