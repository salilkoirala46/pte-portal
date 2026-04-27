<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ questionTypes: Array });

const typeInfo = {
    'summarize-spoken-text':            { icon: '📝', time: '10 min' },
    'listening-multiple-choice-multiple':{ icon: '✅', time: '2-3 min' },
    'listening-fill-blanks':            { icon: '🧩', time: '2-3 min' },
    'highlight-correct-summary':        { icon: '🔍', time: '2-3 min' },
    'listening-multiple-choice-single': { icon: '☑️', time: '2-3 min' },
    'select-missing-word':              { icon: '🔊', time: '1-2 min' },
    'highlight-incorrect-words':        { icon: '❌', time: '2-3 min' },
    'write-from-dictation':             { icon: '✍️', time: '1-2 min' },
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge-listening">Listening</span>
                <span class="text-gray-400 text-sm">8 question types</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Listening Practice</h1>
            <p class="text-gray-500 mt-1">Practise listening comprehension with authentic audio recordings.</p>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <h3 class="font-semibold text-amber-800 text-sm mb-2">💡 PTE Listening is scored on:</h3>
            <div class="flex flex-wrap gap-2">
                <span v-for="s in ['Listening', 'Writing (for Summarize & Dictation)']" :key="s"
                      class="bg-amber-100 text-amber-700 text-xs px-2.5 py-1 rounded-full">{{ s }}</span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Link
                v-for="type in questionTypes"
                :key="type.id"
                :href="route('student.listening.practice', type.slug)"
                class="module-card group"
            >
                <div class="text-3xl mb-3">{{ typeInfo[type.slug]?.icon ?? '🎧' }}</div>
                <h3 class="font-semibold text-gray-900 group-hover:text-amber-700 transition-colors text-sm">{{ type.name }}</h3>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-xs text-gray-400">{{ type.question_count }} q's</span>
                    <span class="text-xs text-amber-600">{{ typeInfo[type.slug]?.time }}</span>
                </div>
            </Link>
        </div>
    </div>
</template>
