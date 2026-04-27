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
            <span class="badge-reading">Reading Result</span>
            <h1 class="text-xl font-bold text-gray-900 mt-1">{{ attempt.question?.title }}</h1>
            <p class="text-sm text-gray-400">{{ attempt.question?.question_type?.name }}</p>
        </div>

        <ScoreCard v-if="attempt.score" :score="attempt.score" module="reading" />

        <!-- Show correct answers for MCQ -->
        <div v-if="attempt.question?.options?.length" class="card p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Answer Review</h3>
            <div class="space-y-2">
                <div v-for="opt in attempt.question.options" :key="opt.id"
                     class="flex items-center gap-3 p-3 rounded-lg text-sm"
                     :class="opt.is_correct ? 'bg-emerald-50 border border-emerald-200' : 'bg-gray-50'">
                    <span class="font-bold w-5 text-center"
                          :class="opt.is_correct ? 'text-emerald-600' : 'text-gray-400'">
                        {{ opt.label }}
                    </span>
                    <span :class="opt.is_correct ? 'text-emerald-800 font-medium' : 'text-gray-600'">{{ opt.content }}</span>
                    <span v-if="opt.is_correct" class="ml-auto text-emerald-600">✓ Correct</span>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <Link :href="route('student.reading.practice', attempt.question?.question_type?.slug)" class="btn-secondary flex-1 text-center">← Back</Link>
            <Link :href="route('student.reading.question', attempt.question?.id)" class="btn-primary flex-1 text-center">Try again</Link>
        </div>
    </div>
</template>
