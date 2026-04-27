<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps({ questionType: Object, questions: Array });
</script>

<template>
    <div class="space-y-6 max-w-3xl">
        <div class="flex items-center gap-3">
            <Link :href="route('student.listening.index')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </Link>
            <div>
                <span class="badge-listening">Listening</span>
                <h1 class="text-xl font-bold text-gray-900 mt-0.5">{{ questionType.name }}</h1>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">{{ questionType.instructions }}</div>

        <div v-if="questions.length" class="space-y-3">
            <Link
                v-for="(q, i) in questions"
                :key="q.id"
                :href="route('student.listening.question', q.id)"
                class="card p-4 flex items-center gap-4 hover:shadow-md transition-shadow group"
            >
                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-sm flex items-center justify-center flex-shrink-0">{{ i + 1 }}</div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ q.title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs capitalize text-gray-400">{{ q.difficulty }}</span>
                        <span v-if="q.audio_duration" class="text-xs text-gray-400">· {{ q.audio_duration }}s audio</span>
                    </div>
                </div>
                <span class="text-amber-600 text-sm font-medium group-hover:underline flex-shrink-0">Practice →</span>
            </Link>
        </div>
        <div v-else class="card p-12 text-center text-gray-500">
            <p class="text-4xl mb-3">📭</p><p>No questions available yet.</p>
        </div>
    </div>
</template>
