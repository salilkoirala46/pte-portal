<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ScoreCard from '@/components/ScoreCard.vue';

defineOptions({ layout: AppLayout });
defineProps({ attempt: Object });
</script>

<template>
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <span class="badge-writing">Writing Result</span>
            <h1 class="text-xl font-bold text-gray-900 mt-1">{{ attempt.question?.title }}</h1>
            <p class="text-sm text-gray-400">{{ attempt.question?.question_type?.name }}</p>
        </div>

        <ScoreCard v-if="attempt.score" :score="attempt.score" module="writing"/>

        <!-- Your answer -->
        <div class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-2">Your Response</h3>
            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">{{ attempt.answer?.text_answer }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ attempt.answer?.text_answer?.trim().split(/\s+/).filter(Boolean).length ?? 0 }} words</p>
        </div>

        <!-- Passage / prompt -->
        <div v-if="attempt.question?.content" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-2">
                {{ attempt.question.question_type?.slug === 'summarize-written-text' ? 'Original Passage' : 'Essay Prompt' }}
            </h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ attempt.question.content }}</p>
        </div>

        <!-- Model answer -->
        <div v-if="attempt.question?.sample_answer" class="card p-5 bg-emerald-50 border-emerald-200">
            <h3 class="font-semibold text-emerald-800 mb-2">Sample Answer</h3>
            <p class="text-emerald-700 text-sm leading-relaxed whitespace-pre-wrap">{{ attempt.question.sample_answer }}</p>
        </div>

        <div class="flex gap-3">
            <Link :href="route('student.writing.practice', attempt.question?.question_type?.slug)" class="btn-secondary flex-1 text-center">← Back</Link>
            <Link :href="route('student.writing.question', attempt.question?.id)" class="btn-primary flex-1 text-center">Try again</Link>
        </div>
    </div>
</template>
