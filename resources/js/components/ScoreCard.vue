<script setup>
import { computed } from 'vue';

const props = defineProps({
    score: { type: Object, required: true },
    module:{ type: String, default: '' },
});

const grade = computed(() => {
    const p = props.score.percentage;
    if (p >= 79) return { label: 'A', color: 'text-emerald-700', bg: 'bg-emerald-50', border: 'border-emerald-200' };
    if (p >= 65) return { label: 'B', color: 'text-blue-700',    bg: 'bg-blue-50',    border: 'border-blue-200' };
    if (p >= 50) return { label: 'C', color: 'text-amber-700',   bg: 'bg-amber-50',   border: 'border-amber-200' };
    if (p >= 36) return { label: 'D', color: 'text-orange-700',  bg: 'bg-orange-50',  border: 'border-orange-200' };
    return { label: 'E', color: 'text-red-700', bg: 'bg-red-50', border: 'border-red-200' };
});

const subScores = computed(() => {
    const s = props.score;
    const rows = [];
    if (s.content_score       != null) rows.push({ label: 'Content',      value: s.content_score,       max: 36 });
    if (s.fluency_score       != null) rows.push({ label: 'Fluency',      value: s.fluency_score,       max: 27 });
    if (s.pronunciation_score != null) rows.push({ label: 'Pronunciation',value: s.pronunciation_score, max: 27 });
    if (s.grammar_score       != null) rows.push({ label: 'Grammar',      value: s.grammar_score,       max: 4  });
    if (s.vocabulary_score    != null) rows.push({ label: 'Vocabulary',   value: s.vocabulary_score,    max: 5  });
    if (s.spelling_score      != null) rows.push({ label: 'Spelling',     value: s.spelling_score,      max: 3  });
    if (s.form_score          != null) rows.push({ label: 'Form',         value: s.form_score,          max: 2  });
    return rows;
});

function barColor(pct) {
    if (pct >= 79) return 'bg-emerald-500';
    if (pct >= 65) return 'bg-blue-500';
    if (pct >= 50) return 'bg-amber-500';
    return 'bg-red-500';
}
</script>

<template>
    <div class="card p-6 space-y-6">
        <!-- Overall score -->
        <div class="flex items-center gap-6">
            <div :class="['w-20 h-20 rounded-2xl border-2 flex flex-col items-center justify-center flex-shrink-0', grade.bg, grade.border]">
                <span :class="['text-3xl font-bold', grade.color]">{{ grade.label }}</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Overall Score</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-bold text-gray-900">{{ Math.round(score.total_score) }}</span>
                    <span class="text-gray-400">/ {{ score.max_score }}</span>
                </div>
                <div class="mt-2 progress-bar w-48">
                    <div :class="['progress-fill', barColor(score.percentage)]"
                         :style="{ width: score.percentage + '%' }"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ Math.round(score.percentage) }}%</p>
            </div>
        </div>

        <!-- Sub-scores -->
        <div v-if="subScores.length" class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Breakdown</h4>
            <div v-for="item in subScores" :key="item.label" class="space-y-1">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ item.label }}</span>
                    <span class="font-medium text-gray-800">{{ item.value }} / {{ item.max }}</span>
                </div>
                <div class="progress-bar">
                    <div :class="['progress-fill', barColor((item.value / item.max) * 100)]"
                         :style="{ width: ((item.value / item.max) * 100) + '%' }"></div>
                </div>
            </div>
        </div>

        <!-- Feedback -->
        <div v-if="score.feedback" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-1">Feedback</h4>
            <p class="text-sm text-blue-700">{{ score.feedback }}</p>
        </div>

        <!-- Detailed feedback -->
        <div v-if="score.detailed_feedback" class="space-y-2">
            <h4 class="text-sm font-semibold text-gray-700">Detailed Feedback</h4>
            <div v-for="(detail, key) in score.detailed_feedback" :key="key"
                 class="bg-gray-50 rounded-lg p-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium capitalize text-gray-700">{{ key }}</span>
                    <span v-if="detail.score != null" class="text-gray-600">{{ detail.score }} / {{ detail.max }}</span>
                </div>
                <p v-if="detail.comment" class="text-xs text-gray-500">{{ detail.comment }}</p>
                <p v-if="detail.word_count" class="text-xs text-gray-500">Word count: {{ detail.word_count }} (required: {{ detail.required }})</p>
            </div>
        </div>
    </div>
</template>
