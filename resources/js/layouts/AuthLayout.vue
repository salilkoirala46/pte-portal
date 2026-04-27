<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const tenant = page.props.tenant;
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 flex flex-col">
        <!-- Header -->
        <header class="py-6 px-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold">PTE</span>
                </div>
                <span class="text-white font-bold text-lg">{{ tenant?.name || 'PTE Portal' }}</span>
            </div>
        </header>

        <!-- Content -->
        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white rounded-2xl shadow-2xl p-8">
                    <slot />
                </div>

                <!-- Footer links -->
                <p class="mt-6 text-center text-sm text-primary-200">
                    <slot name="footer" />
                </p>
            </div>
        </div>

        <!-- Background decoration -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-white/5"></div>
        </div>
    </div>
</template>
