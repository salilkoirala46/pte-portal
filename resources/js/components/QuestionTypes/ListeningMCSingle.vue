<script setup>
import { ref } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const selected     = ref(null);
const audioStarted = ref(false);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50">
            <h2 class="font-semibold text-amber-800">Listening: Multiple Choice — Single Answer</h2>
            <p class="text-sm text-amber-700 mt-1">Listen to the recording and select the best answer.</p>
        </div>

        <AudioPlayer v-if="question.audio_url" :src="question.audio_url" :play-once="true" label="Recording (plays once)" @started="audioStarted = true"/>

        <div class="card p-6 space-y-4" :class="{ 'opacity-50 pointer-events-none': !audioStarted && question.audio_url }">
            <p class="font-semibold text-gray-800">{{ question.title }}</p>
            <div class="space-y-2">
                <label
                    v-for="opt in question.options" :key="opt.id"
                    class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                    :class="selected === opt.id ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'"
                >
                    <input type="radio" :value="opt.id" v-model="selected" class="mt-0.5 accent-primary-600"/>
                    <span class="font-semibold text-gray-500 w-5">{{ opt.label }}.</span>
                    <span class="text-gray-700 text-sm">{{ opt.content }}</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button @click="$emit('submit', { selected_options: selected ? [selected] : [] })" :disabled="!selected || submitting" class="btn-primary btn-lg px-8">
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
