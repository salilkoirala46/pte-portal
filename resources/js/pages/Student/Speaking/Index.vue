<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ questionTypes: Array });

const typeInfo = {
    'read-aloud':           { icon: '📄', prep: '40s prep', rec: '40s recording' },
    'repeat-sentence':      { icon: '🔁', prep: 'Listen first', rec: '15s recording' },
    'describe-image':       { icon: '🖼️', prep: '25s prep', rec: '40s recording' },
    'retell-lecture':       { icon: '🎓', prep: '10s after audio', rec: '40s recording' },
    'answer-short-question':{ icon: '❓', prep: 'Listen first', rec: '10s recording' },
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge-speaking">Speaking</span>
                <span class="text-gray-400 text-sm">5 question types</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Speaking Practice</h1>
            <p class="text-gray-500 mt-1">Record your spoken responses and receive instant feedback on pronunciation, fluency, and content.</p>
        </div>

        <!-- Scoring tip -->
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <h3 class="font-semibold text-purple-800 text-sm mb-1">💡 PTE Speaking is scored on:</h3>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full">Content</span>
                <span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full">Oral Fluency</span>
                <span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full">Pronunciation</span>
            </div>
        </div>

        <!-- Question type cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="type in questionTypes"
                :key="type.id"
                :href="route('student.speaking.practice', type.slug)"
                class="module-card group"
            >
                <div class="flex items-start gap-4">
                    <div class="text-3xl w-12 text-center flex-shrink-0">
                        {{ typeInfo[type.slug]?.icon ?? '🎤' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 group-hover:text-purple-700 transition-colors">{{ type.name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5 mb-2">{{ type.description }}</p>
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <span>{{ typeInfo[type.slug]?.prep }}</span>
                            <span>·</span>
                            <span>{{ typeInfo[type.slug]?.rec }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ type.question_count }} questions</span>
                            <span class="text-xs text-purple-600 font-medium">{{ type.attempts_count }} attempts</span>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
