<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ExamLayout from '@/layouts/ExamLayout.vue';
import SummarizeSpokenText      from '@/components/QuestionTypes/SummarizeSpokenText.vue';
import ListeningMCMultiple      from '@/components/QuestionTypes/ListeningMCMultiple.vue';
import ListeningFillBlanks      from '@/components/QuestionTypes/ListeningFillBlanks.vue';
import HighlightCorrectSummary  from '@/components/QuestionTypes/HighlightCorrectSummary.vue';
import ListeningMCSingle        from '@/components/QuestionTypes/ListeningMCSingle.vue';
import SelectMissingWord        from '@/components/QuestionTypes/SelectMissingWord.vue';
import HighlightIncorrectWords  from '@/components/QuestionTypes/HighlightIncorrectWords.vue';
import WriteFromDictation       from '@/components/QuestionTypes/WriteFromDictation.vue';

defineOptions({ layout: ExamLayout });

const props = defineProps({ question: Object, attempt: Object });

const componentMap = {
    'summarize-spoken-text':             SummarizeSpokenText,
    'listening-multiple-choice-multiple':ListeningMCMultiple,
    'listening-fill-blanks':             ListeningFillBlanks,
    'highlight-correct-summary':         HighlightCorrectSummary,
    'listening-multiple-choice-single':  ListeningMCSingle,
    'select-missing-word':               SelectMissingWord,
    'highlight-incorrect-words':         HighlightIncorrectWords,
    'write-from-dictation':              WriteFromDictation,
};

const QuestionComponent = computed(() => componentMap[props.question.question_type?.slug] ?? ListeningMCSingle);
const submitting = ref(false);

function handleSubmit(data) {
    submitting.value = true;
    router.post(route('student.listening.submit'), {
        attempt_id: props.attempt.id,
        ...data,
    }, { onError: () => { submitting.value = false; } });
}
</script>

<template>
    <component :is="QuestionComponent" :question="question" :attempt="attempt" :submitting="submitting" @submit="handleSubmit"/>
</template>
