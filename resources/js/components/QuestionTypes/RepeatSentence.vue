<script setup>
import { ref } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';
import AudioRecorder from '@/components/AudioRecorder.vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['recorded']);

const audioFinished = ref(false);
const recorder      = ref(null);

function onAudioEnded() {
    audioFinished.value = true;
}
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-purple-200 bg-purple-50">
            <h2 class="font-semibold text-purple-800">Repeat Sentence</h2>
            <p class="text-sm text-purple-700 mt-1">You will hear a sentence. Listen carefully, then repeat it exactly as you heard it.</p>
        </div>

        <!-- Step 1: Listen -->
        <div class="card p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                <h3 class="font-semibold text-gray-700">Listen to the sentence</h3>
            </div>
            <AudioPlayer
                v-if="question.audio_url"
                :src="question.audio_url"
                :play-once="true"
                label="Play sentence (once only)"
                @ended="onAudioEnded"
            />
            <p v-else class="text-sm text-gray-400 italic">Audio not available for this question.</p>
        </div>

        <!-- Step 2: Record -->
        <div class="card p-6" :class="{ 'opacity-40 pointer-events-none': !audioFinished && question.audio_url }">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-full text-xs flex items-center justify-center font-bold"
                      :class="audioFinished ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'">2</span>
                <h3 class="font-semibold text-gray-700">Repeat the sentence</h3>
            </div>
            <AudioRecorder
                ref="recorder"
                :max-seconds="15"
                @recorded="emit('recorded', $event)"
            />
        </div>

        <div v-if="submitting" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
                <div class="animate-spin w-10 h-10 border-4 border-primary-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="font-medium text-gray-700">Scoring your response…</p>
            </div>
        </div>
    </div>
</template>
