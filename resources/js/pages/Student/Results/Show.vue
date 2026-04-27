<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ScoreCard from '@/components/ScoreCard.vue';

defineOptions({ layout: AppLayout });
defineProps({ attempt: Object });

const moduleRoutes = {
    speaking:  'student.speaking',
    reading:   'student.reading',
    writing:   'student.writing',
    listening: 'student.listening',
};
</script>

<template>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('student.results.index')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </Link>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ attempt.question?.title }}</h1>
                <p class="text-sm text-gray-500">{{ attempt.question?.question_type?.name }}</p>
            </div>
        </div>

        <ScoreCard v-if="attempt.score" :score="attempt.score" :module="attempt.question?.question_type?.module"/>

        <!-- Audio response -->
        <div v-if="attempt.answer?.audio_url" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Your Recording</h3>
            <audio :src="attempt.answer.audio_url" controls class="w-full"/>
        </div>

        <!-- Written response -->
        <div v-if="attempt.answer?.text_answer" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-2">Your Written Response</h3>
            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ attempt.answer.text_answer }}</p>
        </div>

        <!-- Original audio -->
        <div v-if="attempt.question?.audio_url" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Original Audio</h3>
            <audio :src="attempt.question.audio_url" controls class="w-full"/>
        </div>

        <!-- Question content -->
        <div v-if="attempt.question?.content" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-2">Question / Passage</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ attempt.question.content }}</p>
        </div>

        <!-- Image -->
        <div v-if="attempt.question?.image_url" class="card p-5">
            <img :src="attempt.question.image_url" :alt="attempt.question.title" class="max-h-64 w-full object-contain rounded-lg"/>
        </div>

        <!-- Sample answer -->
        <div v-if="attempt.question?.sample_answer" class="card p-5 bg-emerald-50 border-emerald-200">
            <h3 class="font-semibold text-emerald-800 mb-2">Model Answer</h3>
            <p class="text-emerald-700 text-sm leading-relaxed whitespace-pre-wrap">{{ attempt.question.sample_answer }}</p>
        </div>

        <div class="flex gap-3">
            <Link
                :href="route(`${moduleRoutes[attempt.question?.question_type?.module]}.index`)"
                class="btn-secondary flex-1 text-center"
            >← {{ attempt.question?.question_type?.module }} practice</Link>
            <Link
                :href="route(`${moduleRoutes[attempt.question?.question_type?.module]}.question`, attempt.question?.id)"
                class="btn-primary flex-1 text-center"
            >Try again</Link>
        </div>
    </div>
</template>
