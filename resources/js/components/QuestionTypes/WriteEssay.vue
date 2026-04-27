<script setup>
import { ref, computed } from 'vue';
import Timer from '@/components/Timer.vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

const answer   = ref('');
const wordCount= computed(() => answer.value.trim().split(/\s+/).filter(Boolean).length);
const inRange  = computed(() => wordCount.value >= 200 && wordCount.value <= 300);
const pct      = computed(() => Math.min(100, (wordCount.value / 300) * 100));

function barColor() {
    if (wordCount.value < 200) return 'bg-amber-400';
    if (wordCount.value <= 300) return 'bg-emerald-500';
    return 'bg-red-500';
}
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-emerald-200 bg-emerald-50 flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-emerald-800">Write Essay</h2>
                <p class="text-sm text-emerald-700 mt-0.5">Write a well-structured essay of 200–300 words in 20 minutes.</p>
            </div>
            <Timer :seconds="1200" />
        </div>

        <!-- Prompt -->
        <div class="card p-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Essay Prompt</h3>
            <p class="text-gray-800 leading-relaxed font-medium">{{ question.content }}</p>
        </div>

        <!-- Writing tips -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
            <p class="font-semibold mb-1">Structure your essay:</p>
            <ol class="list-decimal list-inside space-y-0.5 text-blue-700">
                <li>Introduction — state your position</li>
                <li>Body paragraph 1 — first point with support</li>
                <li>Body paragraph 2 — second point with support</li>
                <li>Conclusion — summarise and restate position</li>
            </ol>
        </div>

        <!-- Text area -->
        <div class="card p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Your Essay</h3>
                <span class="text-sm font-medium" :class="inRange ? 'text-emerald-600' : wordCount > 300 ? 'text-red-500' : 'text-amber-500'">
                    {{ wordCount }} words
                </span>
            </div>
            <textarea
                v-model="answer"
                rows="14"
                class="form-input resize-y"
                placeholder="Start writing your essay here…"
            />
            <!-- Word count bar -->
            <div class="space-y-1">
                <div class="progress-bar">
                    <div :class="['progress-fill', barColor()]" :style="{ width: pct + '%' }"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>0</span>
                    <span class="font-medium text-amber-500">200</span>
                    <span class="font-medium text-emerald-600">300</span>
                </div>
            </div>
            <p v-if="wordCount > 300" class="text-xs text-red-500">Over the 300-word limit. Please reduce.</p>
            <p v-else-if="wordCount > 0 && wordCount < 200" class="text-xs text-amber-500">{{ 200 - wordCount }} more words needed.</p>
        </div>

        <div class="flex justify-end">
            <button
                @click="emit('submit', { text_answer: answer })"
                :disabled="!inRange || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Essay' }}
            </button>
        </div>
    </div>
</template>
