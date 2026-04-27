<script setup>
import { ref, computed } from 'vue';
import Timer from '@/components/Timer.vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

const answer   = ref('');
const wordCount= computed(() => answer.value.trim().split(/\s+/).filter(Boolean).length);
const inRange  = computed(() => wordCount.value >= 5 && wordCount.value <= 75);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-emerald-200 bg-emerald-50 flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-emerald-800">Summarize Written Text</h2>
                <p class="text-sm text-emerald-700 mt-0.5">Read the passage and write a one-sentence summary (5–75 words).</p>
            </div>
            <Timer :seconds="600" />
        </div>

        <!-- Passage -->
        <div class="card p-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Passage</h3>
            <div class="text-gray-700 leading-relaxed">{{ question.content }}</div>
        </div>

        <!-- Write summary -->
        <div class="card p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Your Summary <span class="text-gray-400 font-normal text-sm">(one sentence)</span></h3>
                <span class="text-sm font-medium" :class="inRange ? 'text-emerald-600' : 'text-red-500'">
                    {{ wordCount }} / 75 words
                </span>
            </div>
            <textarea
                v-model="answer"
                rows="4"
                class="form-input resize-none"
                placeholder="Type your one-sentence summary here…"
            />
            <p v-if="wordCount > 75" class="text-xs text-red-500">Exceeds 75-word limit. Shorten your sentence.</p>
            <p v-else-if="wordCount > 0 && wordCount < 5" class="text-xs text-amber-500">Too short. Minimum 5 words required.</p>
        </div>

        <div class="flex justify-end">
            <button
                @click="emit('submit', { text_answer: answer })"
                :disabled="!inRange || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Summary' }}
            </button>
        </div>
    </div>
</template>
