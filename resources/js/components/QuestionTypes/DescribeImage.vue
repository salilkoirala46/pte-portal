<script setup>
import AudioRecorder from '@/components/AudioRecorder.vue';

defineProps({ question: Object, attempt: Object, submitting: Boolean });
defineEmits(['recorded']);
</script>

<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div class="card p-5 border-purple-200 bg-purple-50">
            <h2 class="font-semibold text-purple-800">Describe Image</h2>
            <p class="text-sm text-purple-700 mt-1">Look at the image below. You have 25 seconds to study it, then 40 seconds to describe it in detail.</p>
        </div>

        <!-- Image -->
        <div class="card p-4">
            <img
                v-if="question.image_url"
                :src="question.image_url"
                :alt="question.title"
                class="max-h-80 w-full object-contain rounded-lg"
            />
            <div v-else class="h-48 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                <span>Image not available</span>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
            <p class="font-semibold mb-1">Describe:</p>
            <ul class="list-disc list-inside space-y-0.5 text-blue-700">
                <li>What type of image it is (graph, chart, photo)</li>
                <li>The main subject or topic</li>
                <li>Key data points or features</li>
                <li>Trends, comparisons, or conclusions</li>
            </ul>
        </div>

        <!-- Recorder with prep time -->
        <div class="card p-6">
            <AudioRecorder
                :prep-seconds="25"
                :max-seconds="40"
                @recorded="$emit('recorded', $event)"
            />
        </div>

        <div v-if="submitting" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
                <div class="animate-spin w-10 h-10 border-4 border-primary-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="font-medium text-gray-700">Scoring your response…</p>
            </div>
        </div>
    </div>
</template>
