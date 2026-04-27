<script setup>
import { ref, computed } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const filled       = ref({});
const audioStarted = ref(false);

// Parse {1} tokens from content
const segments = computed(() => {
    const text  = props.question.content ?? '';
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

const canSubmit = computed(() =>
    (props.question.blanks ?? []).every(b => (filled.value[b.id] ?? '').trim())
);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50">
            <h2 class="font-semibold text-amber-800">Listening: Fill in the Blanks</h2>
            <p class="text-sm text-amber-700 mt-1">You will hear a recording. Type the missing words in each blank while listening.</p>
        </div>

        <AudioPlayer v-if="question.audio_url" :src="question.audio_url" label="Recording — type as you listen" @started="audioStarted = true"/>

        <div class="card p-6">
            <p class="text-gray-700 leading-loose text-base">
                <template v-for="seg in segments" :key="seg.id ?? seg.value">
                    <span v-if="seg.type === 'text'">{{ seg.value }}</span>
                    <input
                        v-else
                        v-model="filled[seg.id]"
                        type="text"
                        class="blank-input"
                        style="min-width:80px"
                        placeholder="..."
                    />
                </template>
            </p>
        </div>

        <div class="flex justify-end">
            <button @click="$emit('submit', { filled_blanks: filled })" :disabled="!canSubmit || submitting" class="btn-primary btn-lg px-8">
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
