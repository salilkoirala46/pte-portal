<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ScoreCard from '@/components/ScoreCard.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ attempt: Object });
</script>

<template>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="badge-speaking">Speaking Result</span>
                <h1 class="text-xl font-bold text-gray-900 mt-1">{{ attempt.question?.title }}</h1>
                <p class="text-sm text-gray-400">{{ attempt.question?.question_type?.name }}</p>
            </div>
        </div>

        <!-- Score -->
        <ScoreCard v-if="attempt.score" :score="attempt.score" module="speaking" />

        <!-- Your response playback -->
        <div v-if="attempt.answer?.audio_url" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Your Recording</h3>
            <audio :src="attempt.answer.audio_url" controls class="w-full rounded-lg"/>
        </div>

        <!-- Original content -->
        <div v-if="attempt.question?.content" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-2">
                {{ attempt.question.question_type?.slug === 'read-aloud' ? 'Original Text' : 'Question' }}
            </h3>
            <p class="text-gray-700 leading-relaxed">{{ attempt.question.content }}</p>
        </div>

        <!-- Model answer -->
        <div v-if="attempt.question?.sample_answer" class="card p-5 bg-emerald-50 border-emerald-200">
            <h3 class="font-semibold text-emerald-800 mb-2">Sample Answer</h3>
            <p class="text-emerald-700 text-sm leading-relaxed">{{ attempt.question.sample_answer }}</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
            <Link :href="route('student.speaking.practice', attempt.question?.question_type?.slug)" class="btn-secondary flex-1 text-center">
                ← Back to practice
            </Link>
            <Link :href="route('student.speaking.question', attempt.question?.id)" class="btn-primary flex-1 text-center">
                Try again
            </Link>
        </div>
    </div>
</template>
