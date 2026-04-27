<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ questionTypes: Array });

const typeInfo = {
    'summarize-written-text': { icon: '📋', time: '10 min', limit: '5–75 words' },
    'write-essay':            { icon: '📝', time: '20 min', limit: '200–300 words' },
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge-writing">Writing</span>
                <span class="text-gray-400 text-sm">2 question types</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Writing Practice</h1>
            <p class="text-gray-500 mt-1">Practise academic writing skills judged on grammar, vocabulary, spelling, and content.</p>
        </div>

        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
            <h3 class="font-semibold text-emerald-800 text-sm mb-2">💡 PTE Writing is scored on:</h3>
            <div class="flex flex-wrap gap-2">
                <span v-for="s in ['Content', 'Form', 'Grammar', 'Vocabulary', 'Spelling']" :key="s"
                      class="bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full">{{ s }}</span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <Link
                v-for="type in questionTypes"
                :key="type.id"
                :href="route('student.writing.practice', type.slug)"
                class="module-card group"
            >
                <div class="text-4xl mb-4">{{ typeInfo[type.slug]?.icon ?? '✍️' }}</div>
                <h3 class="font-bold text-gray-900 text-lg group-hover:text-emerald-700 transition-colors">{{ type.name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ type.description }}</p>
                <div class="mt-4 flex items-center gap-4 text-xs text-gray-400">
                    <span>⏱ {{ typeInfo[type.slug]?.time }}</span>
                    <span>📏 {{ typeInfo[type.slug]?.limit }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-xs text-gray-400">{{ type.question_count }} questions</span>
                    <span class="text-sm text-emerald-600 font-medium group-hover:underline">Practice →</span>
                </div>
            </Link>
        </div>
    </div>
</template>
