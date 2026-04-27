<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/auth';
import { useSubscriptionStore } from '@/stores/subscription';
import FlashMessage from '@/components/UI/FlashMessage.vue';

const auth = useAuthStore();
const sub  = useSubscriptionStore();

const mobileOpen = ref(false);
const userMenuOpen = ref(false);

const navItems = computed(() => {
    if (auth.isTenantAdmin) {
        return [
            { label: 'Dashboard',  href: route('admin.dashboard'),  icon: 'home' },
            { label: 'Questions',  href: route('admin.questions.index'), icon: 'document' },
            { label: 'Students',   href: route('admin.students.index'),  icon: 'users' },
            { label: 'Plans',      href: route('admin.plans.index'),     icon: 'credit-card' },
            { label: 'Reports',    href: route('admin.reports'),          icon: 'chart' },
            { label: 'Settings',   href: route('admin.settings'),         icon: 'cog' },
        ];
    }
    return [
        { label: 'Dashboard',  href: route('student.dashboard'),           icon: 'home' },
        { label: 'Speaking',   href: route('student.speaking.index'),      icon: 'microphone', color: 'text-purple-600' },
        { label: 'Reading',    href: route('student.reading.index'),       icon: 'book-open',  color: 'text-cyan-600' },
        { label: 'Writing',    href: route('student.writing.index'),       icon: 'pencil',     color: 'text-emerald-600' },
        { label: 'Listening',  href: route('student.listening.index'),     icon: 'headphone',  color: 'text-amber-600' },
        { label: 'My Results', href: route('student.results.index'),       icon: 'chart' },
    ];
});

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <Link :href="auth.isStudent ? route('student.dashboard') : route('admin.dashboard')" class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">PTE</span>
                            </div>
                            <span class="font-bold text-gray-900 hidden sm:block">{{ auth.tenant?.name || 'PTE Portal' }}</span>
                        </Link>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden lg:flex items-center gap-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="$page.url.startsWith(new URL(item.href).pathname)
                                ? 'bg-primary-50 text-primary-700'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                        >
                            {{ item.label }}
                        </Link>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-3">
                        <!-- Subscription badge -->
                        <div v-if="auth.isStudent && sub.isActive" class="hidden sm:flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium px-2.5 py-1 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ sub.statusLabel }}
                        </div>

                        <!-- User menu -->
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                <img :src="auth.user?.avatar_url" :alt="auth.user?.name" class="w-8 h-8 rounded-full object-cover"/>
                                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth.user?.name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <Transition enter-from-class="opacity-0 scale-95" enter-active-class="transition duration-100" leave-to-class="opacity-0 scale-95" leave-active-class="transition duration-75">
                                <div v-if="userMenuOpen" @click.away="userMenuOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ auth.user?.name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ auth.user?.email }}</p>
                                    </div>
                                    <Link v-if="auth.isStudent" :href="route('student.subscription.index')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        Subscription
                                    </Link>
                                    <button @click="logout" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Sign out
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile nav -->
            <Transition enter-from-class="-translate-y-2 opacity-0" enter-active-class="transition duration-150">
                <div v-if="mobileOpen" class="lg:hidden border-t border-gray-200 bg-white px-4 py-3 space-y-1">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="block px-3 py-2 rounded-lg text-sm font-medium"
                        :class="$page.url.startsWith(new URL(item.href).pathname)
                            ? 'bg-primary-50 text-primary-700'
                            : 'text-gray-600 hover:bg-gray-100'"
                        @click="mobileOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                </div>
            </Transition>
        </nav>

        <!-- Flash messages -->
        <FlashMessage />

        <!-- Page content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <slot />
        </main>
    </div>
</template>
