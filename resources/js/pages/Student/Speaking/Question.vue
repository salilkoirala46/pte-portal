<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ExamLayout from '@/layouts/ExamLayout.vue';
import ReadAloud from '@/components/QuestionTypes/ReadAloud.vue';
import RepeatSentence from '@/components/QuestionTypes/RepeatSentence.vue';
import DescribeImage from '@/components/QuestionTypes/DescribeImage.vue';
import RetellLecture from '@/components/QuestionTypes/RetellLecture.vue';
import AnswerShortQuestion from '@/components/QuestionTypes/AnswerShortQuestion.vue';

defineOptions({ layout: ExamLayout });

const props = defineProps({
    question: Object,
    attempt:  Object,
});

const layoutProps = computed(() => ({
    question:     props.question,
    attempt:      props.attempt,
    module:       'speaking',
    instructions: props.question.question_type?.instructions ?? '',
    timeLimit:    0,
}));

const componentMap = {
    'read-aloud':            ReadAloud,
    'repeat-sentence':       RepeatSentence,
    'describe-image':        DescribeImage,
    'retell-lecture':        RetellLecture,
    'answer-short-question': AnswerShortQuestion,
};

const QuestionComponent = computed(() =>
    componentMap[props.question.question_type?.slug] ?? ReadAloud
);

const submitting = ref(false);

async function handleRecorded({ blob, duration }) {
    submitting.value = true;

    const formData = new FormData();
    formData.append('attempt_id', props.attempt.id);
    formData.append('audio', blob, 'response.webm');
    formData.append('duration', duration);

    router.post(route('student.speaking.submit'), formData, {
        forceFormData: true,
        onError: ()  => { submitting.value = false; },
    });
}
</script>

<template>
    <component :is="'div'">
        <!-- Pass layout props via provide/inject workaround -->
        <component
            :is="QuestionComponent"
            :question="question"
            :attempt="attempt"
            :submitting="submitting"
            @recorded="handleRecorded"
        />
    </component>
</template>
