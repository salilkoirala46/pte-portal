<script setup>
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps({ moduleStats: Object });

const moduleConfig = {
    speaking:  { label: 'Speaking',  icon: '🎙️', color: 'text-purple-600', bg: 'bg-purple-50', bar: 'bg-purple-500' },
    reading:   { label: 'Reading',   icon: '📖', color: 'text-cyan-600',   bg: 'bg-cyan-50',   bar: 'bg-cyan-500' },
    writing:   { label: 'Writing',   icon: '✍️', color: 'text-emerald-600',bg: 'bg-emerald-50',bar: 'bg-emerald-500' },
    listening: { label: 'Listening', icon: '🎧', color: 'text-amber-600',  bg: 'bg-amber-50',  bar: 'bg-amber-500' },
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>

        <!-- Module performance -->
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Module Performance</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="(stats, module) in moduleStats" :key="module" class="card p-5 space-y-3">
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center text-xl', moduleConfig[module]?.bg]">
                        {{ moduleConfig[module]?.icon }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ moduleConfig[module]?.label }}</h3>
                        <p class="text-3xl font-bold mt-1" :class="moduleConfig[module]?.color">
                            {{ Math.round(stats.avg_score) }}%
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">avg score</p>
                    </div>
                    <div class="progress-bar">
                        <div :class="['progress-fill', moduleConfig[module]?.bar]" :style="{ width: stats.avg_score + '%' }"></div>
                    </div>
                    <p class="text-xs text-gray-500">{{ stats.total_attempts }} total attempts</p>
                </div>
            </div>
        </div>
    </div>
</template>
