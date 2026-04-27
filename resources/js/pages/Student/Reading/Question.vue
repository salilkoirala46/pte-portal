<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ExamLayout from '@/layouts/ExamLayout.vue';
import MultipleChoiceSingle   from '@/components/QuestionTypes/MultipleChoiceSingle.vue';
import MultipleChoiceMultiple from '@/components/QuestionTypes/MultipleChoiceMultiple.vue';
import ReorderParagraphs      from '@/components/QuestionTypes/ReorderParagraphs.vue';
import FillInTheBlanks        from '@/components/QuestionTypes/FillInTheBlanks.vue';
import ReadingWritingFillBlanks from '@/components/QuestionTypes/ReadingWritingFillBlanks.vue';

defineOptions({ layout: ExamLayout });

const props = defineProps({ question: Object, attempt: Object });

const componentMap = {
    'reading-multiple-choice-single':   MultipleChoiceSingle,
    'reading-multiple-choice-multiple': MultipleChoiceMultiple,
    'reorder-paragraphs':               ReorderParagraphs,
    'reading-fill-blanks':              FillInTheBlanks,
    'reading-writing-fill-blanks':      ReadingWritingFillBlanks,
};

const QuestionComponent = computed(() => componentMap[props.question.question_type?.slug] ?? MultipleChoiceSingle);
const submitting = ref(false);

function handleSubmit(answerData) {
    submitting.value = true;
    router.post(route('student.reading.submit'), {
        attempt_id: props.attempt.id,
        ...answerData,
    }, {
        onError: () => { submitting.value = false; },
    });
}
</script>

<template>
    <div>
        <component
            :is="QuestionComponent"
            :question="question"
            :attempt="attempt"
            :submitting="submitting"
            @submit="handleSubmit"
        />
    </div>
</template>
