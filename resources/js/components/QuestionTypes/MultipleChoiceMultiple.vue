<script setup>
import { ref } from 'vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

const selected = ref([]);

function toggle(id) {
    const idx = selected.value.indexOf(id);
    if (idx === -1) selected.value.push(id);
    else selected.value.splice(idx, 1);
}
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-cyan-200 bg-cyan-50">
            <h2 class="font-semibold text-cyan-800">Multiple Choice — Multiple Answers</h2>
            <p class="text-sm text-cyan-700 mt-1">Read the passage and select ALL correct answers. More than one answer is correct.</p>
        </div>

        <div class="card p-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Reading Passage</h3>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">{{ question.content }}</div>
        </div>

        <div class="card p-6 space-y-4">
            <p class="font-semibold text-gray-800">{{ question.title }}</p>
            <p class="text-xs text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg">Select all that apply. Wrong selections deduct points.</p>
            <div class="space-y-2">
                <label
                    v-for="opt in question.options"
                    :key="opt.id"
                    class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                    :class="selected.includes(opt.id)
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'"
                    @click="toggle(opt.id)"
                >
                    <input type="checkbox" :value="opt.id" v-model="selected" class="mt-0.5 accent-primary-600 pointer-events-none"/>
                    <span class="font-semibold text-gray-500 w-5">{{ opt.label }}.</span>
                    <span class="text-gray-700 text-sm">{{ opt.content }}</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                @click="emit('submit', { selected_options: selected })"
                :disabled="selected.length === 0 || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
