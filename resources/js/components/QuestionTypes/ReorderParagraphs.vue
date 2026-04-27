<script setup>
import { ref, computed } from 'vue';

const props = defineProps({ question: Object, attempt: Object, submitting: Boolean });
const emit  = defineEmits(['submit']);

// Shuffle source list; ordered list starts empty
const source  = ref([...(props.question.paragraphs ?? [])].sort(() => Math.random() - 0.5));
const ordered = ref([]);

function moveToOrdered(item) {
    source.value  = source.value.filter(p => p.id !== item.id);
    ordered.value = [...ordered.value, item];
}

function moveToSource(item) {
    ordered.value = ordered.value.filter(p => p.id !== item.id);
    source.value  = [...source.value, item];
}

function moveUp(index) {
    if (index === 0) return;
    const arr = [...ordered.value];
    [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
    ordered.value = arr;
}

function moveDown(index) {
    if (index === ordered.value.length - 1) return;
    const arr = [...ordered.value];
    [arr[index], arr[index + 1]] = [arr[index + 1], arr[index]];
    ordered.value = arr;
}

const canSubmit = computed(() => ordered.value.length > 0);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-cyan-200 bg-cyan-50">
            <h2 class="font-semibold text-cyan-800">Re-order Paragraphs</h2>
            <p class="text-sm text-cyan-700 mt-1">The paragraphs have been shuffled. Click them to move to the right panel in the correct order.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Source panel -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source (click to add)</h3>
                <div class="min-h-32 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-3 space-y-2">
                    <div
                        v-for="item in source"
                        :key="item.id"
                        @click="moveToOrdered(item)"
                        class="bg-white border border-gray-200 rounded-lg p-3 text-sm text-gray-700 cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-colors select-none"
                    >
                        {{ item.content }}
                    </div>
                    <p v-if="!source.length" class="text-xs text-gray-400 text-center py-4">All paragraphs moved</p>
                </div>
            </div>

            <!-- Ordered panel -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Your Order</h3>
                <div class="min-h-32 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-3 space-y-2">
                    <div
                        v-for="(item, i) in ordered"
                        :key="item.id"
                        class="bg-white border border-primary-200 rounded-lg p-3 text-sm text-gray-700 flex items-start gap-2"
                    >
                        <div class="flex flex-col gap-0.5 flex-shrink-0">
                            <button @click="moveUp(i)" :disabled="i===0" class="text-gray-300 hover:text-gray-600 disabled:opacity-20 text-xs">▲</button>
                            <button @click="moveDown(i)" :disabled="i===ordered.length-1" class="text-gray-300 hover:text-gray-600 disabled:opacity-20 text-xs">▼</button>
                        </div>
                        <span class="w-5 font-bold text-primary-500 flex-shrink-0">{{ i + 1 }}.</span>
                        <span class="flex-1">{{ item.content }}</span>
                        <button @click="moveToSource(item)" class="text-gray-300 hover:text-red-400 flex-shrink-0 text-xs ml-1">✕</button>
                    </div>
                    <p v-if="!ordered.length" class="text-xs text-gray-400 text-center py-4">Click paragraphs on the left to add them here</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                @click="emit('submit', { arranged_order: ordered.map(p => p.id) })"
                :disabled="!canSubmit || submitting"
                class="btn-primary btn-lg px-8"
            >
                {{ submitting ? 'Submitting…' : 'Submit Answer' }}
            </button>
        </div>
    </div>
</template>
