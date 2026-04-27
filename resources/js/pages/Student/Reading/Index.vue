<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ questionTypes: Array });

const typeInfo = {
    'reading-multiple-choice-single':   { icon: '☑️',  time: '2-3 min' },
    'reading-multiple-choice-multiple': { icon: '✅',  time: '2-3 min' },
    'reorder-paragraphs':               { icon: '🔀',  time: '2-3 min' },
    'reading-fill-blanks':              { icon: '🧩',  time: '2-3 min' },
    'reading-writing-fill-blanks':      { icon: '📝',  time: '6-7 min' },
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge-reading">Reading</span>
                <span class="text-gray-400 text-sm">5 question types</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Reading Practice</h1>
            <p class="text-gray-500 mt-1">Practise reading comprehension, vocabulary, and text structure skills.</p>
        </div>

        <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4">
            <h3 class="font-semibold text-cyan-800 text-sm mb-2">💡 PTE Reading is scored on:</h3>
            <div class="flex flex-wrap gap-2">
                <span class="bg-cyan-100 text-cyan-700 text-xs px-2.5 py-1 rounded-full">Reading</span>
                <span class="bg-cyan-100 text-cyan-700 text-xs px-2.5 py-1 rounded-full">Writing (for R&W Fill Blanks)</span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="type in questionTypes"
                :key="type.id"
                :href="route('student.reading.practice', type.slug)"
                class="module-card group"
            >
                <div class="flex items-start gap-4">
                    <div class="text-3xl w-12 text-center flex-shrink-0">
                        {{ typeInfo[type.slug]?.icon ?? '📖' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 group-hover:text-cyan-700 transition-colors">{{ type.name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 mb-2">{{ type.description }}</p>
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-xs text-gray-400">{{ type.question_count }} questions</span>
                            <span class="text-xs text-cyan-600 font-medium">{{ typeInfo[type.slug]?.time }}</span>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
