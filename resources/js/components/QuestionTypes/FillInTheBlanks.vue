<script setup>
import { ref, computed } from 'vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

// Parse {1}, {2}... from content
const filled    = ref({});
const wordBank  = ref([...(props.question.word_bank ?? [])]);
const usedWords = ref({});

// Build text segments: alternate text and blank slots
const segments = computed(() => {
    const text = props.question.content ?? '';
    const parts = [];
    let   last  = 0;
    const re    = /\{(\d+)\}/g;
    let   m;
    while ((m = re.exec(text)) !== null) {
        if (m.index > last) parts.push({ type: 'text', value: text.slice(last, m.index) });
        parts.push({ type: 'blank', id: parseInt(m[1]) });
        last = m.index + m[0].length;
    }
    if (last < text.length) parts.push({ type: 'text', value: text.slice(last) });
    return parts;
});

function placeWord(word, blankId) {
    // Return previous word in blank back to bank
    const prev = filled.value[blankId];
    if (prev) wordBank.value.push(prev);

    filled.value[blankId] = word;
    wordBank.value = wordBank.value.filter(w => w !== word);
}

function removeWord(blankId) {
    const word = filled.value[blankId];
    if (word) {
        wordBank.value.push(word);
        delete filled.value[blankId];
    }
}

const dragging = ref(null);

function onDragStart(word) { dragging.value = word; }
function onDrop(blankId) {
    if (dragging.value) placeWord(dragging.value, blankId);
    dragging.value = null;
}

const canSubmit = computed(() => {
    const blanks = (props.question.blanks ?? []);
    return blanks.every(b => filled.value[b.id]);
});
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-cyan-200 bg-cyan-50">
            <h2 class="font-semibold text-cyan-800">Fill in the Blanks — Reading</h2>
            <p class="text-sm text-cyan-700 mt-1">Drag words from the box below into the correct blank positions in the text.</p>
        </div>

        <!-- Passage with blanks -->
        <div class="card p-6">
            <p class="text-gray-700 leading-loose text-base">
                <template v-for="seg in segments" :key="seg.id ?? seg.value">
                    <span v-if="seg.type === 'text'">{{ seg.value }}</span>
                    <span
                        v-else
                        class="inline-flex items-center min-w-[80px] mx-1 px-2 py-0.5 border-b-2 rounded cursor-pointer align-middle transition-colors"
                        :class="filled[seg.id]
                            ? 'border-primary-500 bg-primary-50 text-primary-800 font-medium'
                            : 'border-gray-400 bg-gray-50 text-gray-400'"
                        @dragover.prevent
                        @drop="onDrop(seg.id)"
                        @click="removeWord(seg.id)"
                    >
                        {{ filled[seg.id] ?? '     ' }}
                    </span>
                </template>
            </p>
        </div>

        <!-- Word bank -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Word Bank</h3>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="word in wordBank"
                    :key="word"
                    draggable="true"
                    @dragstart="onDragStart(word)"
                    class="px-3 py-1.5 bg-white border-2 border-gray-200 rounded-lg text-sm font-medium text-gray-700 cursor-grab hover:border-primary-400 hover:bg-primary-50 transition-colors select-none"
                >
                    {{ word }}
                </span>
                <p v-if="!wordBank.length" class="text-xs text-gray-400">All words placed</p>
            </div>
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
