<script setup>
import { ref } from 'vue';

const props = defineProps({
    src:         { type: String, required: true },
    autoplay:    { type: Boolean, default: false },
    label:       { type: String, default: 'Listen to the audio' },
    playOnce:    { type: Boolean, default: false },
});
const emit = defineEmits(['ended', 'started']);

const audio      = ref(null);
const isPlaying  = ref(false);
const playCount  = ref(0);
const progress   = ref(0);
const currentTime= ref(0);
const duration   = ref(0);

function toggle() {
    if (!audio.value) return;
    if (props.playOnce && playCount.value >= 1) return;

    if (isPlaying.value) {
        audio.value.pause();
    } else {
        audio.value.play();
        playCount.value++;
        emit('started');
    }
}

function onTimeUpdate() {
    if (!audio.value) return;
    currentTime.value = audio.value.currentTime;
    duration.value    = audio.value.duration || 0;
    progress.value    = duration.value ? (currentTime.value / duration.value) * 100 : 0;
}

function onEnded() {
    isPlaying.value = false;
    emit('ended');
}

function fmt(s) {
    if (!s || isNaN(s)) return '0:00';
    return `${Math.floor(s/60)}:${Math.floor(s%60).toString().padStart(2,'0')}`;
}
</script>

<template>
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
        <p class="text-sm text-gray-600 font-medium">{{ label }}</p>

        <audio
            ref="audio"
            :src="src"
            :autoplay="autoplay"
            @play="isPlaying = true"
            @pause="isPlaying = false"
            @ended="onEnded"
            @timeupdate="onTimeUpdate"
            class="hidden"
        />

        <div class="flex items-center gap-3">
            <button
                @click="toggle"
                :disabled="playOnce && playCount >= 1"
                class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                :class="(playOnce && playCount >= 1) ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-primary-600 text-white hover:bg-primary-700'"
            >
                <svg v-if="!isPlaying" class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
            </button>

            <div class="flex-1 space-y-1">
                <div class="progress-bar">
                    <div class="progress-fill bg-primary-500" :style="{ width: progress + '%' }"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>{{ fmt(currentTime) }}</span>
                    <span>{{ fmt(duration) }}</span>
                </div>
            </div>
        </div>

        <p v-if="playOnce && playCount >= 1" class="text-xs text-amber-600">
            This audio can only be played once.
        </p>
    </div>
</template>
