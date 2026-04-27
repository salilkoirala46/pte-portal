<script setup>
import { ref, computed } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const highlighted = ref([]);

// Split additional_content (transcript) into words
const words = computed(() =>
    (props.question.additional_content ?? props.question.content ?? '')
        .split(/(\s+)/)
        .filter(w => w.trim())
);

function toggle(word, index) {
    const key = `${index}:${word}`;
    const i   = highlighted.value.indexOf(key);
    i === -1 ? highlighted.value.push(key) : highlighted.value.splice(i, 1);
}

function isHighlighted(word, index) {
    return highlighted.value.includes(`${index}:${word}`);
}
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50">
            <h2 class="font-semibold text-amber-800">Highlight Incorrect Words</h2>
            <p class="text-sm text-amber-700 mt-1">Listen to the recording. Some words in the transcript are different from what the speaker said. Click on those words.</p>
        </div>

        <AudioPlayer v-if="question.audio_url" :src="question.audio_url" label="Recording"/>

        <div class="card p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Transcript — click incorrect words</h3>
            <p class="text-gray-700 leading-loose text-base">
                <span
                    v-for="(word, i) in words"
                    :key="i"
                    @click="toggle(word, i)"
                    class="cursor-pointer rounded px-0.5 transition-colors select-none"
                    :class="isHighlighted(word, i)
                        ? 'bg-red-200 text-red-800 line-through'
                        : 'hover:bg-yellow-100'"
                >{{ word }} </span>
            </p>
            <p class="text-xs text-gray-400 mt-3">{{ highlighted.length }} word(s) highlighted</p>
        </div>

        <div class="flex justify-end">
            <button
                @click="$emit('submit', { highlighted_words: highlighted })"
                :disabled="submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
