<script setup>
import { ref } from 'vue';
import AudioPlayer from '@/components/AudioPlayer.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['submit']);

const selected     = ref([]);
const audioStarted = ref(false);

function toggle(id) {
    const i = selected.value.indexOf(id);
    i === -1 ? selected.value.push(id) : selected.value.splice(i, 1);
}
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-amber-200 bg-amber-50">
            <h2 class="font-semibold text-amber-800">Listening: Multiple Choice — Multiple Answers</h2>
            <p class="text-sm text-amber-700 mt-1">Select ALL correct answers. Wrong selections deduct points.</p>
        </div>

        <AudioPlayer v-if="question.audio_url" :src="question.audio_url" label="Recording" @started="audioStarted = true"/>

        <div class="card p-6 space-y-4" :class="{ 'opacity-50 pointer-events-none': !audioStarted && question.audio_url }">
            <p class="font-semibold text-gray-800">{{ question.title }}</p>
            <div class="space-y-2">
                <label v-for="opt in question.options" :key="opt.id"
                    class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                    :class="selected.includes(opt.id) ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'"
                    @click="toggle(opt.id)">
                    <input type="checkbox" :value="opt.id" v-model="selected" class="mt-0.5 accent-primary-600 pointer-events-none"/>
                    <span class="font-semibold text-gray-500 w-5">{{ opt.label }}.</span>
                    <span class="text-gray-700 text-sm">{{ opt.content }}</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button @click="$emit('submit', { selected_options: selected })" :disabled="selected.length === 0 || submitting" class="btn-primary btn-lg px-8">
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
