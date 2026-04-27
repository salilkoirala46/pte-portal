import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useSpeakingStore = defineStore('speaking', () => {
    const mediaRecorder  = ref(null);
    const audioChunks    = ref([]);
    const audioBlob      = ref(null);
    const audioUrl       = ref(null);
    const isRecording    = ref(false);
    const recordingError = ref(null);
    const duration       = ref(0);
    let durationTimer    = null;

    async function requestMicPermission() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            stream.getTracks().forEach(t => t.stop());
            return true;
        } catch {
            recordingError.value = 'Microphone permission denied. Please allow microphone access to record your response.';
            return false;
        }
    }

    async function startRecording() {
        recordingError.value = null;
        audioChunks.value    = [];
        audioBlob.value      = null;
        audioUrl.value       = null;
        duration.value       = 0;

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const mimeType = MediaRecorder.isTypeSupported('audio/webm')
                ? 'audio/webm'
                : 'audio/ogg';

            mediaRecorder.value = new MediaRecorder(stream, { mimeType });

            mediaRecorder.value.ondataavailable = (e) => {
                if (e.data.size > 0) audioChunks.value.push(e.data);
            };

            mediaRecorder.value.onstop = () => {
                const blob = new Blob(audioChunks.value, { type: mimeType });
                audioBlob.value = blob;
                audioUrl.value  = URL.createObjectURL(blob);
                stream.getTracks().forEach(t => t.stop());
            };

            mediaRecorder.value.start(100);
            isRecording.value = true;

            durationTimer = setInterval(() => { duration.value++; }, 1000);
        } catch (err) {
            recordingError.value = `Recording error: ${err.message}`;
        }
    }

    function stopRecording() {
        if (mediaRecorder.value && mediaRecorder.value.state !== 'inactive') {
            mediaRecorder.value.stop();
        }
        isRecording.value = false;
        clearInterval(durationTimer);
    }

    function getAudioFile(filename = 'response.webm') {
        if (!audioBlob.value) return null;
        return new File([audioBlob.value], filename, { type: audioBlob.value.type });
    }

    function reset() {
        stopRecording();
        audioChunks.value    = [];
        audioBlob.value      = null;
        audioUrl.value       = null;
        isRecording.value    = false;
        recordingError.value = null;
        duration.value       = 0;
    }

    return {
        isRecording, audioBlob, audioUrl, duration, recordingError,
        requestMicPermission, startRecording, stopRecording, getAudioFile, reset,
    };
});
