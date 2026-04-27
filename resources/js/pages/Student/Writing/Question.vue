<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ExamLayout from '@/layouts/ExamLayout.vue';
import SummarizeWrittenText from '@/components/QuestionTypes/SummarizeWrittenText.vue';
import WriteEssay           from '@/components/QuestionTypes/WriteEssay.vue';

defineOptions({ layout: ExamLayout });

const props = defineProps({ question: Object, attempt: Object });

const componentMap = {
    'summarize-written-text': SummarizeWrittenText,
    'write-essay':            WriteEssay,
};
const QuestionComponent = computed(() => componentMap[props.question.question_type?.slug] ?? WriteEssay);
const submitting = ref(false);

function handleSubmit({ text_answer }) {
    submitting.value = true;
    router.post(route('student.writing.submit'), {
        attempt_id:  props.attempt.id,
        text_answer,
        time_taken:  0,
    }, { onError: () => { submitting.value = false; } });
}
</script>

<template>
    <component :is="QuestionComponent" :question="question" :attempt="attempt" :submitting="submitting" @submit="handleSubmit"/>
</template>
