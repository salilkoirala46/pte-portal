<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

defineProps({
    tenant:         Object,
    stats:          Object,
    recentStudents: Array,
});
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-500 mt-0.5">{{ tenant?.name }}</p>
            </div>
            <Link :href="route('admin.questions.create')" class="btn-primary gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Question
            </Link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="s in [
                { label: 'Students',       value: stats.total_students,       icon: '👥', color: 'text-blue-600' },
                { label: 'Subscriptions',  value: stats.active_subscriptions, icon: '💳', color: 'text-emerald-600' },
                { label: 'Questions',      value: stats.total_questions,      icon: '❓', color: 'text-purple-600' },
                { label: 'Attempts',       value: stats.total_attempts,       icon: '📝', color: 'text-amber-600' },
            ]" :key="s.label" class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xl">{{ s.icon }}</span>
                </div>
                <p :class="['text-3xl font-bold', s.color]">{{ s.value }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ s.label }}</p>
            </div>
        </div>

        <!-- Quick links -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <Link v-for="l in [
                { label: 'Manage Questions', href: route('admin.questions.index'), color: 'bg-purple-50 text-purple-700 hover:bg-purple-100' },
                { label: 'Manage Students',  href: route('admin.students.index'),  color: 'bg-blue-50 text-blue-700 hover:bg-blue-100' },
                { label: 'Manage Plans',     href: route('admin.plans.index'),     color: 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' },
                { label: 'View Reports',     href: route('admin.reports'),         color: 'bg-amber-50 text-amber-700 hover:bg-amber-100' },
            ]" :key="l.label" :href="l.href"
               :class="['card p-4 text-sm font-semibold text-center transition-colors', l.color]">
                {{ l.label }}
            </Link>
        </div>

        <!-- Recent students -->
        <div class="card">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Recent Students</h2>
                <Link :href="route('admin.students.index')" class="text-sm text-primary-600 hover:underline">View all</Link>
            </div>
            <div v-if="recentStudents?.length" class="divide-y divide-gray-100">
                <div v-for="s in recentStudents" :key="s.id" class="flex items-center gap-4 px-5 py-3">
                    <img :src="s.avatar_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(s.name)}&background=EBF4FF&color=7F9CF5`"
                         :alt="s.name" class="w-8 h-8 rounded-full object-cover flex-shrink-0"/>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ s.name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ s.email }}</p>
                    </div>
                    <span v-if="s.active_subscription" class="badge bg-emerald-100 text-emerald-700">Active</span>
                    <span v-else class="badge bg-gray-100 text-gray-500">No sub</span>
                </div>
            </div>
            <div v-else class="p-8 text-center text-gray-400 text-sm">No students yet.</div>
        </div>
    </div>
</template>
