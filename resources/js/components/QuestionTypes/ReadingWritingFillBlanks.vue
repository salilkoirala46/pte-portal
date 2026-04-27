<script setup>
import { ref, computed } from 'vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

const filled = ref({});

// Each blank has a set of options from question.blanks: [{id, options:["word1","word2",...], answer}]
const segments = computed(() => {
    const text  = props.question.content ?? '';
    const parts = [];
    let   last  = 0;
    const re    = /\{(\d+)\}/g;
    let   m;
    while ((m = re.exec(text)) !== null) {
        if (m.index > last) parts.push({ type: 'text', value: text.slice(last, m.index) });
        const blankId  = parseInt(m[1]);
        const blankDef = (props.question.blanks ?? []).find(b => b.id === blankId);
        parts.push({ type: 'blank', id: blankId, options: blankDef?.options ?? [] });
        last = m.index + m[0].length;
    }
    if (last < text.length) parts.push({ type: 'text', value: text.slice(last) });
    return parts;
});

const canSubmit = computed(() =>
    (props.question.blanks ?? []).every(b => filled.value[b.id])
);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-cyan-200 bg-cyan-50">
            <h2 class="font-semibold text-cyan-800">Reading &amp; Writing: Fill in the Blanks</h2>
            <p class="text-sm text-cyan-700 mt-1">For each blank, select the correct word from the dropdown list.</p>
        </div>

        <div class="card p-6">
            <p class="text-gray-700 leading-loose text-base">
                <template v-for="seg in segments" :key="seg.id ?? seg.value">
                    <span v-if="seg.type === 'text'">{{ seg.value }}</span>
                    <select
                        v-else
                        v-model="filled[seg.id]"
                        class="inline mx-1 px-2 py-0.5 border-2 rounded-lg text-sm align-middle transition-colors cursor-pointer"
                        :class="filled[seg.id]
                            ? 'border-primary-500 bg-primary-50 text-primary-800'
                            : 'border-gray-300 text-gray-500'"
                    >
                        <option value="" disabled selected>— select —</option>
                        <option v-for="opt in seg.options" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                </template>
            </p>
        </div>

        <div class="flex justify-end">
            <button
                @click="emit('submit', { filled_blanks: filled })"
                :disabled="!canSubmit || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
