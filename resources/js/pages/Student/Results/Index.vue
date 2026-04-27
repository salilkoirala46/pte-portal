<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ attempts: Object });

const moduleBadge = {
    speaking:  'badge-speaking',
    reading:   'badge-reading',
    writing:   'badge-writing',
    listening: 'badge-listening',
};

function gradeColor(pct) {
    if (pct >= 79) return 'text-emerald-600';
    if (pct >= 65) return 'text-blue-600';
    if (pct >= 50) return 'text-amber-600';
    return 'text-red-600';
}
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Results</h1>
            <p class="text-gray-500 mt-1">All your completed practice attempts</p>
        </div>

        <div v-if="attempts.data.length" class="space-y-3">
            <div v-for="attempt in attempts.data" :key="attempt.id" class="card p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center">
                    <span class="text-xl">
                        {{ { speaking: '🎙️', reading: '📖', writing: '✍️', listening: '🎧' }[attempt.question?.question_type?.module] ?? '📋' }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ attempt.question?.title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span :class="moduleBadge[attempt.question?.question_type?.module]">
                            {{ attempt.question?.question_type?.module }}
                        </span>
                        <span class="text-xs text-gray-400">{{ attempt.question?.question_type?.name }}</span>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p :class="['text-xl font-bold', gradeColor(attempt.score?.percentage ?? 0)]">
                        {{ Math.round(attempt.score?.percentage ?? 0) }}%
                    </p>
                    <p class="text-xs text-gray-400">{{ attempt.score?.total_score }} / {{ attempt.score?.max_score }}</p>
                </div>
                <Link :href="route('student.results.show', attempt.id)" class="btn-secondary btn-sm flex-shrink-0">
                    Review
                </Link>
            </div>
        </div>

        <div v-else class="card p-12 text-center text-gray-500">
            <p class="text-4xl mb-3">📊</p>
            <p class="font-medium text-gray-700 mb-1">No results yet</p>
            <p class="text-sm">Complete a practice question to see your results here.</p>
        </div>

        <!-- Pagination -->
        <div v-if="attempts.last_page > 1" class="flex justify-center gap-2">
            <Link
                v-for="page in attempts.last_page"
                :key="page"
                :href="attempts.links[page]?.url ?? '#'"
                class="btn-secondary btn-sm"
                :class="{ 'bg-primary-50 border-primary-300 text-primary-700': page === attempts.current_page }"
            >
                {{ page }}
            </Link>
        </div>
    </div>
</template>
