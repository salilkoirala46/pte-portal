<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ students: Object, filters: Object });

const search = ref(props.filters?.search ?? '');

function applySearch() {
    router.get(route('admin.students.index'), { search: search.value }, { preserveState: true, replace: true });
}

function toggleActive(student) {
    router.put(route('admin.students.update', student.id), { is_active: !student.is_active });
}

function remove(student) {
    if (!confirm(`Remove ${student.name}?`)) return;
    router.delete(route('admin.students.destroy', student.id));
}
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Students</h1>
                <p class="text-gray-500 text-sm">{{ students.total }} total</p>
            </div>
            <div class="flex gap-2">
                <input v-model="search" @keyup.enter="applySearch" type="text" placeholder="Search by name or email…" class="form-input w-64"/>
            </div>
        </div>

        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Student</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Subscription</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Exam date</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="s in students.data" :key="s.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(s.name)}&background=EBF4FF&color=7F9CF5`"
                                         :alt="s.name" class="w-8 h-8 rounded-full"/>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ s.name }}</p>
                                        <p class="text-xs text-gray-400">{{ s.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span v-if="s.active_subscription" class="badge bg-emerald-100 text-emerald-700">
                                    {{ s.active_subscription.plan?.name }}
                                </span>
                                <span v-else class="badge bg-gray-100 text-gray-500">None</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ s.exam_date ? new Date(s.exam_date).toLocaleDateString() : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span :class="s.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600'" class="badge">
                                    {{ s.is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 justify-end">
                                    <button @click="toggleActive(s)" class="btn-secondary btn-sm">
                                        {{ s.is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                    <button @click="remove(s)" class="btn-danger btn-sm">Remove</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!students.data.length">
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400">No students found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
