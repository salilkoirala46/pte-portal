<script setup>
import { ref, computed } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';
import Timer from '@/components/Timer.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const answer    = ref('');
const wordCount = computed(() => answer.value.trim().split(/\s+/).filter(Boolean).length);
const inRange   = computed(() => wordCount.value >= 50 && wordCount.value <= 70);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50 flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-amber-800">Summarize Spoken Text</h2>
                <p class="text-sm text-amber-700 mt-0.5">Listen to the recording and write a 50–70 word summary.</p>
            </div>
            <Timer :seconds="600" />
        </div>

        <AudioPlayer v-if="question.audio_url" :src="question.audio_url" label="Listen carefully — you may play this once"/>

        <div class="card p-6 space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">Your Summary</h3>
                <span class="text-sm font-medium" :class="inRange ? 'text-emerald-600' : 'text-amber-500'">
                    {{ wordCount }} / 70 words
                </span>
            </div>
            <textarea v-model="answer" rows="6" class="form-input resize-none" placeholder="Write your summary here (50–70 words)…"/>
            <p v-if="wordCount > 70" class="text-xs text-red-500">Over the 70-word limit.</p>
        </div>

        <div class="flex justify-end">
            <button @click="$emit('submit', { text_answer: answer })" :disabled="!inRange || submitting" class="btn-primary btn-lg px-8">
                {{ submitting ? 'Submitting…' : 'Submit Summary' }}
            </button>
        </div>
    </div>
</template>
