<script setup>
import { ref } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const answer       = ref('');
const audioPlayed  = ref(false);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50">
            <h2 class="font-semibold text-amber-800">Write from Dictation</h2>
            <p class="text-sm text-amber-700 mt-1">You will hear a sentence once. Type it exactly as you hear it — spelling and punctuation matter.</p>
        </div>

        <AudioPlayer
            v-if="question.audio_url"
            :src="question.audio_url"
            :play-once="true"
            label="Sentence (plays once only)"
            @ended="audioPlayed = true"
        />

        <div class="card p-6 space-y-3" :class="{ 'opacity-50 pointer-events-none': !audioPlayed && question.audio_url }">
            <label class="form-label">Type what you heard</label>
            <input
                v-model="answer"
                type="text"
                class="form-input text-base"
                placeholder="Type the sentence exactly as you heard it…"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
            />
            <p class="text-xs text-gray-400">{{ answer.trim().split(/\s+/).filter(Boolean).length }} words typed</p>
        </div>

        <div class="flex justify-end">
            <button
                @click="$emit('submit', { text_answer: answer })"
                :disabled="!answer.trim() || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
