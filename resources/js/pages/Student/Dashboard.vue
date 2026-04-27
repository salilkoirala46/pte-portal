<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useSubscriptionStore } from '@/stores/subscription';

defineOptions({ layout: AppLayout });

const props = defineProps({
    user:           Object,
    stats:          Object,
    recentAttempts: Array,
});

const sub = useSubscriptionStore();

const modules = [
    { name: 'Speaking',  route: 'student.speaking.index',  icon: '🎙️', color: 'from-purple-500 to-purple-700', textColor: 'text-purple-600', bg: 'bg-purple-50', statKey: 'speaking_attempts',  desc: '5 question types' },
    { name: 'Reading',   route: 'student.reading.index',   icon: '📖', color: 'from-cyan-500 to-cyan-700',     textColor: 'text-cyan-600',   bg: 'bg-cyan-50',   statKey: 'reading_attempts',   desc: '5 question types' },
    { name: 'Writing',   route: 'student.writing.index',   icon: '✍️', color: 'from-emerald-500 to-emerald-700',textColor: 'text-emerald-600',bg: 'bg-emerald-50',statKey: 'writing_attempts',   desc: '2 question types' },
    { name: 'Listening', route: 'student.listening.index', icon: '🎧', color: 'from-amber-500 to-amber-600',   textColor: 'text-amber-600',  bg: 'bg-amber-50',  statKey: 'listening_attempts', desc: '8 question types' },
];

function moduleColor(type) {
    return { speaking: 'text-purple-600', reading: 'text-cyan-600', writing: 'text-emerald-600', listening: 'text-amber-600' }[type] ?? 'text-gray-600';
}

const daysToExam = computed(() => {
    if (!props.user.exam_date) return null;
    const d = Math.ceil((new Date(props.user.exam_date) - new Date()) / 86400000);
    return Math.max(0, d);
});
</script>

<template>
    <div class="space-y-6">
        <!-- Welcome header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ user.name.split(' ')[0] }} 👋</h1>
                <p class="text-gray-500 text-sm mt-0.5">Keep practising — every attempt improves your score.</p>
            </div>
            <div class="flex items-center gap-3">
                <div v-if="daysToExam !== null" class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ daysToExam }}</p>
                    <p class="text-xs text-amber-500">days to exam</p>
                </div>
                <div v-if="!sub.isActive" class="bg-red-50 border border-red-200 rounded-xl px-4 py-2">
                    <p class="text-xs font-semibold text-red-600">No active subscription</p>
                    <Link :href="route('student.subscription.index')" class="text-xs text-red-500 hover:underline">Subscribe now →</Link>
                </div>
            </div>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="card p-4 text-center">
                <p class="text-3xl font-bold text-gray-900">{{ stats.total_attempts }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Attempts</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-3xl font-bold text-primary-600">{{ Math.round(stats.average_score) }}%</p>
                <p class="text-xs text-gray-500 mt-1">Avg Score</p>
            </div>
            <div class="card p-4 text-center col-span-2 sm:col-span-2">
                <div class="flex justify-around">
                    <div v-for="m in modules" :key="m.name" class="text-center">
                        <p class="text-lg font-bold" :class="m.textColor">{{ stats[m.statKey] }}</p>
                        <p class="text-xs text-gray-400">{{ m.name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module cards -->
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Practice Modules</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link
                    v-for="m in modules"
                    :key="m.name"
                    :href="sub.isActive ? route(m.route) : route('student.subscription.index')"
                    class="module-card group relative overflow-hidden"
                >
                    <div :class="['absolute inset-0 opacity-5 bg-gradient-to-br', m.color]"></div>
                    <div class="relative">
                        <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center text-2xl mb-4', m.bg]">
                            {{ m.icon }}
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ m.name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ m.desc }}</p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ stats[m.statKey] }} attempts</span>
                            <span :class="['text-sm font-medium group-hover:underline', m.textColor]">Practice →</span>
                        </div>
                    </div>
                    <div v-if="!sub.isActive" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center rounded-xl">
                        <span class="text-sm font-semibold text-gray-600">🔒 Subscribe to unlock</span>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Recent attempts -->
        <div v-if="recentAttempts?.length">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Practice</h2>
                <Link :href="route('student.results.index')" class="text-sm text-primary-600 hover:underline">View all →</Link>
            </div>
            <div class="card divide-y divide-gray-100">
                <div v-for="attempt in recentAttempts" :key="attempt.id" class="flex items-center gap-4 p-4">
                    <div :class="['w-2 h-10 rounded-full flex-shrink-0', {
                        'bg-purple-400': attempt.question?.question_type?.module === 'speaking',
                        'bg-cyan-400':   attempt.question?.question_type?.module === 'reading',
                        'bg-emerald-400':attempt.question?.question_type?.module === 'writing',
                        'bg-amber-400':  attempt.question?.question_type?.module === 'listening',
                    }]"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ attempt.question?.title }}</p>
                        <p class="text-xs text-gray-400">{{ attempt.question?.question_type?.name }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-gray-900">{{ Math.round(attempt.score?.percentage ?? 0) }}%</p>
                        <p class="text-xs text-gray-400">{{ attempt.score?.total_score }} / {{ attempt.score?.max_score }}</p>
                    </div>
                    <Link :href="route('student.results.show', attempt.id)" class="btn-secondary btn-sm">Review</Link>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="card p-12 text-center text-gray-500">
            <div class="text-5xl mb-4">🚀</div>
            <h3 class="font-semibold text-gray-700 mb-2">Start your first practice session</h3>
            <p class="text-sm mb-4">Pick a module above and start practising to see your results here.</p>
        </div>
    </div>
</template>
