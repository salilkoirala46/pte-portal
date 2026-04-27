import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useExamStore = defineStore('exam', () => {
    const currentQuestion  = ref(null);
    const currentAttempt   = ref(null);
    const phase            = ref('idle');   // idle | prep | recording | answering | submitted
    const timeRemaining    = ref(0);
    const prepTimeRemaining= ref(0);
    const isRecording      = ref(false);
    const hasSubmitted     = ref(false);
    const answer           = ref({
        text: '',
        selectedOptions: [],
        arrangedOrder: [],
        filledBlanks: {},
        highlightedWords: [],
        audioBlob: null,
        audioDuration: 0,
    });

    const questionType = computed(() => currentQuestion.value?.question_type);
    const isPrepping   = computed(() => phase.value === 'prep');
    const isAnswering  = computed(() => ['recording', 'answering'].includes(phase.value));

    function setQuestion(question, attempt) {
        currentQuestion.value  = question;
        currentAttempt.value   = attempt;
        phase.value            = 'idle';
        hasSubmitted.value     = false;
        resetAnswer();
    }

    function resetAnswer() {
        answer.value = {
            text: '',
            selectedOptions: [],
            arrangedOrder: [],
            filledBlanks: {},
            highlightedWords: [],
            audioBlob: null,
            audioDuration: 0,
        };
    }

    function startPrep(seconds) {
        phase.value             = 'prep';
        prepTimeRemaining.value = seconds;
    }

    function startAnswering(seconds) {
        phase.value       = 'answering';
        timeRemaining.value = seconds;
    }

    function startRecording(seconds) {
        phase.value       = 'recording';
        isRecording.value = true;
        timeRemaining.value = seconds;
    }

    function stopRecording() {
        isRecording.value = false;
        if (phase.value === 'recording') phase.value = 'answered';
    }

    function setAudio(blob, duration) {
        answer.value.audioBlob    = blob;
        answer.value.audioDuration= duration;
    }

    function submit() {
        hasSubmitted.value = true;
        phase.value        = 'submitted';
    }

    function reset() {
        currentQuestion.value  = null;
        currentAttempt.value   = null;
        phase.value            = 'idle';
        timeRemaining.value    = 0;
        prepTimeRemaining.value= 0;
        isRecording.value      = false;
        hasSubmitted.value     = false;
        resetAnswer();
    }

    return {
        currentQuestion, currentAttempt, phase, timeRemaining,
        prepTimeRemaining, isRecording, hasSubmitted, answer,
        questionType, isPrepping, isAnswering,
        setQuestion, startPrep, startAnswering, startRecording,
        stopRecording, setAudio, submit, reset, resetAnswer,
    };
});
