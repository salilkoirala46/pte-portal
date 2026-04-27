<script setup>
import { ref } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const selected = ref(null);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50">
            <h2 class="font-semibold text-amber-800">Highlight Correct Summary</h2>
            <p class="text-sm text-amber-700 mt-1">Listen to the recording and click the paragraph that best relates to it.</p>
        </div>

        <AudioPlayer v-if="question.audio_url" :src="question.audio_url" label="Recording"/>

        <div class="card p-6 space-y-4">
            <p class="font-semibold text-gray-800">Which paragraph best summarises the recording?</p>
            <div class="space-y-3">
                <div
                    v-for="opt in question.options" :key="opt.id"
                    @click="selected = opt.id"
                    class="p-4 rounded-xl border-2 cursor-pointer transition-all text-sm text-gray-700 leading-relaxed"
                    :class="selected === opt.id ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'"
                >
                    <span class="font-bold text-gray-500 mr-2">{{ opt.label }}.</span>{{ opt.content }}
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button @click="$emit('submit', { selected_options: selected ? [selected] : [] })" :disabled="!selected || submitting" class="btn-primary btn-lg px-8">
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
