<script setup>
import { ref } from 'vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

const selected = ref(null);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-cyan-200 bg-cyan-50">
            <h2 class="font-semibold text-cyan-800">Multiple Choice — Single Answer</h2>
            <p class="text-sm text-cyan-700 mt-1">Read the passage and choose the single best answer.</p>
        </div>

        <!-- Passage -->
        <div class="card p-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Reading Passage</h3>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">{{ question.content }}</div>
        </div>

        <!-- Question prompt (use title as the question) -->
        <div class="card p-6 space-y-4">
            <p class="font-semibold text-gray-800">{{ question.title }}</p>
            <div class="space-y-2">
                <label
                    v-for="opt in question.options"
                    :key="opt.id"
                    class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                    :class="selected === opt.id
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                >
                    <input type="radio" :value="opt.id" v-model="selected" class="mt-0.5 accent-primary-600"/>
                    <span class="font-semibold text-gray-500 w-5">{{ opt.label }}.</span>
                    <span class="text-gray-700 text-sm">{{ opt.content }}</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                @click="emit('submit', { selected_options: selected ? [selected] : [] })"
                :disabled="!selected || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
