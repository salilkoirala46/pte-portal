<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    questions:     Object,
    questionTypes: Array,
    filters:       Object,
});

const search = ref(props.filters?.search ?? '');
const module = ref(props.filters?.module ?? '');

function applyFilters() {
    router.get(route('admin.questions.index'), { search: search.value, module: module.value }, { preserveState: true, replace: true });
}

function deleteQuestion(id) {
    if (!confirm('Delete this question? This cannot be undone.')) return;
    router.delete(route('admin.questions.destroy', id));
}

const moduleColors = {
    speaking: 'badge-speaking', reading: 'badge-reading',
    writing:  'badge-writing',  listening: 'badge-listening',
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Questions</h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ questions.total }} total questions</p>
            </div>
            <Link :href="route('admin.questions.create')" class="btn-primary gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Question
            </Link>
        </div>

        <!-- Filters -->
        <div class="flex gap-3 flex-wrap">
            <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Search questions…" class="form-input w-64"/>
            <select v-model="module" @change="applyFilters" class="form-input w-40">
                <option value="">All modules</option>
                <option v-for="m in ['speaking','reading','writing','listening']" :key="m" :value="m" class="capitalize">{{ m }}</option>
            </select>
        </div>

        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Question</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Type</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Difficulty</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="q in questions.data" :key="q.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 max-w-xs truncate">{{ q.title }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <span :class="moduleColors[q.question_type?.module]">{{ q.question_type?.module }}</span>
                                    <p class="text-xs text-gray-400">{{ q.question_type?.name }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="capitalize text-gray-600">{{ q.difficulty }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="q.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'" class="badge">
                                    {{ q.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('admin.questions.edit', q.id)" class="btn-secondary btn-sm">Edit</Link>
                                    <button @click="deleteQuestion(q.id)" class="btn-danger btn-sm">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!questions.data.length">
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400">No questions found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="questions.last_page > 1" class="flex gap-1 justify-center">
            <Link v-for="link in questions.links" :key="link.label"
                  :href="link.url ?? '#'"
                  v-html="link.label"
                  class="px-3 py-1.5 text-sm rounded-lg border border-gray-200"
                  :class="link.active ? 'bg-primary-600 text-white border-primary-600' : 'text-gray-600 hover:bg-gray-50'"
            />
        </div>
    </div>
</template>
