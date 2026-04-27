<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineProps({ tenant: Object });

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login.post'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <AuthLayout>
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
            <p class="mt-2 text-sm text-gray-600">Sign in to your account to continue</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="form-label">Email address</label>
                <input
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    required
                    class="form-input"
                    :class="{ 'border-red-300': form.errors.email }"
                    placeholder="you@example.com"
                />
                <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="form-label">Password</label>
                <input
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    required
                    class="form-input"
                    :class="{ 'border-red-300': form.errors.password }"
                    placeholder="••••••••"
                />
                <p v-if="form.errors.password" class="form-error">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"/>
                    Remember me
                </label>
            </div>

            <button
                type="submit"
                class="btn-primary w-full btn-lg"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="flex items-center gap-2 justify-center">
                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Signing in...
                </span>
                <span v-else>Sign in</span>
            </button>
        </form>

        <template #footer>
            Don't have an account?
            <Link v-if="tenant" :href="route('register')" class="text-white font-medium hover:underline ml-1">
                Register here
            </Link>
            <span v-else class="text-primary-300 ml-1">Contact your institution to register.</span>
        </template>
    </AuthLayout>
</template>
