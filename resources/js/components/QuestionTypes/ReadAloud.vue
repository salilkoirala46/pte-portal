<script setup>
import { ref } from 'vue';
import AudioRecorder from '@/components/AudioRecorder.vue';

const props  = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit   = defineEmits(['recorded']);
const recorder = ref(null);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <!-- Header -->
        <div class="card p-5 border-purple-200 bg-purple-50">
            <h2 class="font-semibold text-purple-800 mb-1">Read Aloud</h2>
            <p class="text-sm text-purple-700">Read the following text aloud as naturally and clearly as possible.</p>
        </div>

        <!-- Text to read -->
        <div class="card p-6">
            <p class="text-gray-800 text-lg leading-relaxed font-medium select-none">
                {{ question.content }}
            </p>
        </div>

        <!-- Recorder -->
        <div class="card p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Your Response</h3>
            <AudioRecorder
                ref="recorder"
                :prep-seconds="40"
                :max-seconds="40"
                @recorded="emit('recorded', $event)"
            />
        </div>

        <!-- Submit overlay -->
        <div v-if="submitting" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
                <div class="animate-spin w-10 h-10 border-4 border-primary-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="font-medium text-gray-700">Scoring your response…</p>
            </div>
        </div>
    </div>
</template>
