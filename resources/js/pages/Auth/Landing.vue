<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ tenant: Object });

const modules = [
    { name: 'Speaking', icon: '🎙️', color: 'bg-purple-100 text-purple-700 border-purple-200', desc: 'Read Aloud, Repeat Sentence, Describe Image, Re-tell Lecture' },
    { name: 'Reading',  icon: '📖', color: 'bg-cyan-100 text-cyan-700 border-cyan-200',     desc: 'MCQ, Re-order Paragraphs, Fill in the Blanks' },
    { name: 'Writing',  icon: '✍️', color: 'bg-emerald-100 text-emerald-700 border-emerald-200', desc: 'Summarize Written Text, Write Essay' },
    { name: 'Listening',icon: '🎧', color: 'bg-amber-100 text-amber-700 border-amber-200',  desc: 'Summarize Spoken Text, Dictation, Fill Blanks, and more' },
];
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700">
        <!-- Nav -->
        <nav class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold">PTE</span>
                </div>
                <span class="text-white font-bold text-xl">{{ tenant?.name || 'PTE Portal' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <Link :href="route('login')" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Sign in</Link>
                <Link :href="route('register')" class="bg-white text-primary-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-primary-50 transition-colors">
                    Get started
                </Link>
            </div>
        </nav>

        <!-- Hero -->
        <div class="max-w-6xl mx-auto px-6 pt-16 pb-24 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 text-white/90 text-sm px-4 py-1.5 rounded-full mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Official PTE Academic practice platform
            </div>
            <h1 class="text-4xl sm:text-6xl font-bold text-white leading-tight mb-6">
                Master your<br>
                <span class="text-amber-300">PTE Academic</span> exam
            </h1>
            <p class="text-lg text-primary-200 max-w-2xl mx-auto mb-10">
                Practice all four modules — Speaking, Reading, Writing, and Listening — with real exam-format questions and instant AI-powered scoring.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link :href="route('register')" class="btn bg-amber-400 text-amber-900 hover:bg-amber-300 btn-lg font-bold px-8 w-full sm:w-auto">
                    Start free trial
                </Link>
                <Link :href="route('login')" class="btn bg-white/10 text-white border border-white/20 hover:bg-white/20 btn-lg px-8 w-full sm:w-auto">
                    Sign in
                </Link>
            </div>
        </div>

        <!-- Modules -->
        <div class="max-w-6xl mx-auto px-6 pb-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="m in modules" :key="m.name"
                     :class="['bg-white/10 backdrop-blur border border-white/10 rounded-2xl p-5 text-white hover:bg-white/15 transition-colors']">
                    <div class="text-3xl mb-3">{{ m.icon }}</div>
                    <h3 class="font-bold text-lg mb-1">{{ m.name }}</h3>
                    <p class="text-sm text-primary-200">{{ m.desc }}</p>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white">
            <div class="max-w-6xl mx-auto px-6 py-16">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Everything you need to score high</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div v-for="f in [
                        { icon: '🎯', title: 'Real exam format', desc: 'Questions identical to the actual PTE Academic exam.' },
                        { icon: '⚡', title: 'Instant scoring', desc: 'Automated scoring with detailed feedback right after submission.' },
                        { icon: '📊', title: 'Progress tracking', desc: 'Monitor your improvement across all four modules over time.' },
                    ]" :key="f.title" class="text-center">
                        <div class="text-4xl mb-4">{{ f.icon }}</div>
                        <h3 class="font-bold text-gray-900 mb-2">{{ f.title }}</h3>
                        <p class="text-gray-500 text-sm">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
