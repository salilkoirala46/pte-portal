<script setup>
import { ref } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';
import AudioRecorder from '@/components/AudioRecorder.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['recorded']);

const audioPlayed = ref(false);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-purple-200 bg-purple-50">
            <h2 class="font-semibold text-purple-800">Answer Short Question</h2>
            <p class="text-sm text-purple-700 mt-1">Listen to the question and give a short answer — usually just one or a few words.</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                <h3 class="font-semibold text-gray-700">Listen to the question</h3>
            </div>
            <AudioPlayer
                v-if="question.audio_url"
                :src="question.audio_url"
                :play-once="true"
                label="Question audio (plays once)"
                @ended="audioPlayed = true"
            />
            <p v-else class="text-sm text-gray-500">{{ question.content }}</p>
        </div>

        <div class="card p-6" :class="{ 'opacity-40 pointer-events-none': !audioPlayed && question.audio_url }">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-full text-xs flex items-center justify-center font-bold"
                      :class="audioPlayed ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'">2</span>
                <h3 class="font-semibold text-gray-700">Give your answer</h3>
                <span class="text-xs text-gray-400">(10 seconds)</span>
            </div>
            <AudioRecorder
                :max-seconds="10"
                @recorded="$emit('recorded', $event)"
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
