<script setup>
import { ref, computed, onUnmounted } from 'vue';

const props = defineProps({
    maxSeconds:  { type: Number, default: 40 },
    prepSeconds: { type: Number, default: 0 },
    autoStart:   { type: Boolean, default: false },
});

const emit = defineEmits(['recorded', 'error', 'prep-done', 'time-up']);

// State
const phase          = ref('idle');     // idle | prep | recording | done
const prepRemaining  = ref(props.prepSeconds);
const recRemaining   = ref(props.maxSeconds);
const mediaRecorder  = ref(null);
const audioChunks    = ref([]);
const audioBlob      = ref(null);
const audioUrl       = ref(null);
const errorMessage   = ref('');
const duration       = ref(0);
let   prepTimer      = null;
let   recTimer       = null;

const prepFormatted = computed(() => {
    const s = prepRemaining.value;
    return `${Math.floor(s/60).toString().padStart(2,'0')}:${(s%60).toString().padStart(2,'0')}`;
});
const recFormatted = computed(() => {
    const s = recRemaining.value;
    return `${Math.floor(s/60).toString().padStart(2,'0')}:${(s%60).toString().padStart(2,'0')}`;
});

const isIdle      = computed(() => phase.value === 'idle');
const isPrepping  = computed(() => phase.value === 'prep');
const isRecording = computed(() => phase.value === 'recording');
const isDone      = computed(() => phase.value === 'done');

async function init() {
    if (props.prepSeconds > 0) {
        await startPrep();
    } else {
        await startRecording();
    }
}

async function startPrep() {
    phase.value        = 'prep';
    prepRemaining.value= props.prepSeconds;

    prepTimer = setInterval(() => {
        prepRemaining.value--;
        if (prepRemaining.value <= 0) {
            clearInterval(prepTimer);
            emit('prep-done');
            startRecording();
        }
    }, 1000);
}

async function startRecording() {
    errorMessage.value = '';
    audioChunks.value  = [];
    duration.value     = 0;

    try {
        const stream   = await navigator.mediaDevices.getUserMedia({ audio: true });
        const mimeType = MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/ogg';

        mediaRecorder.value = new MediaRecorder(stream, { mimeType });
        mediaRecorder.value.ondataavailable = (e) => {
            if (e.data.size > 0) audioChunks.value.push(e.data);
        };
        mediaRecorder.value.onstop = () => {
            const blob      = new Blob(audioChunks.value, { type: mimeType });
            audioBlob.value = blob;
            audioUrl.value  = URL.createObjectURL(blob);
            stream.getTracks().forEach(t => t.stop());
            phase.value     = 'done';
            emit('recorded', { blob, url: audioUrl.value, duration: duration.value });
        };
        mediaRecorder.value.start(100);

        phase.value        = 'recording';
        recRemaining.value = props.maxSeconds;

        recTimer = setInterval(() => {
            duration.value++;
            recRemaining.value--;
            if (recRemaining.value <= 0) {
                clearInterval(recTimer);
                stopRecording();
                emit('time-up');
            }
        }, 1000);
    } catch (err) {
        errorMessage.value = 'Microphone access denied. Please allow microphone access and try again.';
        emit('error', errorMessage.value);
        phase.value = 'idle';
    }
}

function stopRecording() {
    clearInterval(recTimer);
    if (mediaRecorder.value?.state !== 'inactive') {
        mediaRecorder.value?.stop();
    }
}

function reset() {
    clearInterval(prepTimer);
    clearInterval(recTimer);
    if (mediaRecorder.value?.state !== 'inactive') {
        mediaRecorder.value?.stop();
    }
    phase.value        = 'idle';
    prepRemaining.value= props.prepSeconds;
    recRemaining.value = props.maxSeconds;
    audioBlob.value    = null;
    audioUrl.value     = null;
    audioChunks.value  = [];
    duration.value     = 0;
    errorMessage.value = '';
}

onUnmounted(() => {
    clearInterval(prepTimer);
    clearInterval(recTimer);
    if (mediaRecorder.value?.state !== 'inactive') mediaRecorder.value?.stop();
});

defineExpose({ init, stopRecording, reset, audioBlob, duration });
</script>

<template>
    <div class="space-y-4">
        <!-- Error -->
        <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {{ errorMessage }}
        </div>

        <!-- Prep countdown -->
        <div v-if="isPrepping" class="text-center py-6">
            <p class="text-sm text-gray-500 mb-2">Preparation time</p>
            <div class="text-5xl font-mono font-bold text-primary-600">{{ prepFormatted }}</div>
            <p class="text-xs text-gray-400 mt-2">Recording will begin automatically</p>
        </div>

        <!-- Recording state -->
        <div v-if="isRecording" class="text-center py-4 space-y-3">
            <!-- Waveform animation -->
            <div class="flex items-center justify-center gap-1 h-12">
                <div v-for="i in 7" :key="i"
                     class="waveform-bar w-1.5 bg-red-500 rounded-full"
                     :style="{ height: `${20 + (i % 3) * 15}px`, animationDelay: `${i * 0.1}s` }">
                </div>
            </div>
            <div class="flex items-center justify-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                <span class="font-medium text-red-600">Recording</span>
                <span class="font-mono text-gray-700 font-semibold">{{ recFormatted }}</span>
            </div>
            <button @click="stopRecording" class="btn-secondary btn-sm mt-2">
                Stop Recording
            </button>
        </div>

        <!-- Done state -->
        <div v-if="isDone" class="space-y-3">
            <div class="flex items-center gap-2 text-emerald-700 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Recording complete ({{ duration }}s)
            </div>
            <audio v-if="audioUrl" :src="audioUrl" controls class="w-full h-10 rounded-lg"></audio>
            <button @click="reset" class="btn-secondary btn-sm">
                Re-record
            </button>
        </div>

        <!-- Idle state -->
        <div v-if="isIdle" class="text-center">
            <button @click="init" class="btn-primary btn-lg gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                </svg>
                {{ prepSeconds > 0 ? 'Start (with prep time)' : 'Start Recording' }}
            </button>
        </div>
    </div>
</template>
