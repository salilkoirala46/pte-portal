<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineProps({ tenant: Object });

const form = useForm({
    name:          '',
    email:         '',
    phone:         '',
    country:       '',
    date_of_birth: '',
    target_score:  '',
    exam_date:     '',
    password:      '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register.post'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}

const countries = [
    { code: 'AU', name: 'Australia' }, { code: 'NZ', name: 'New Zealand' },
    { code: 'IN', name: 'India' },     { code: 'CN', name: 'China' },
    { code: 'PH', name: 'Philippines' },{ code: 'NP', name: 'Nepal' },
    { code: 'BD', name: 'Bangladesh' },{ code: 'PK', name: 'Pakistan' },
    { code: 'GB', name: 'United Kingdom' },{ code: 'CA', name: 'Canada' },
    { code: 'US', name: 'United States' }, { code: 'OTHER', name: 'Other' },
];
</script>

<template>
    <AuthLayout>
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
            <p class="mt-1 text-sm text-gray-600">Join {{ tenant?.name }} and start practising</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="form-label">Full Name *</label>
                    <input v-model="form.name" type="text" required class="form-input" :class="{ 'border-red-300': form.errors.name }" placeholder="Jane Smith"/>
                    <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                </div>

                <div class="col-span-2">
                    <label class="form-label">Email *</label>
                    <input v-model="form.email" type="email" required class="form-input" :class="{ 'border-red-300': form.errors.email }" placeholder="jane@example.com"/>
                    <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="form-label">Password *</label>
                    <input v-model="form.password" type="password" required minlength="8" class="form-input" :class="{ 'border-red-300': form.errors.password }" placeholder="Min. 8 characters"/>
                    <p v-if="form.errors.password" class="form-error">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="form-label">Confirm Password *</label>
                    <input v-model="form.password_confirmation" type="password" required class="form-input" placeholder="Repeat password"/>
                </div>

                <div>
                    <label class="form-label">Phone</label>
                    <input v-model="form.phone" type="tel" class="form-input" placeholder="+61 400 000 000"/>
                </div>

                <div>
                    <label class="form-label">Country</label>
                    <select v-model="form.country" class="form-input">
                        <option value="">Select country</option>
                        <option v-for="c in countries" :key="c.code" :value="c.code">{{ c.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Date of Birth</label>
                    <input v-model="form.date_of_birth" type="date" class="form-input"/>
                </div>

                <div>
                    <label class="form-label">PTE Exam Date</label>
                    <input v-model="form.exam_date" type="date" class="form-input"/>
                </div>

                <div class="col-span-2">
                    <label class="form-label">Target PTE Score</label>
                    <select v-model="form.target_score" class="form-input">
                        <option value="">Select target</option>
                        <option value="50">50+ (Basic)</option>
                        <option value="58">58+ (Skilled Worker)</option>
                        <option value="65">65+ (Good)</option>
                        <option value="79">79+ (Excellent)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full btn-lg mt-2" :disabled="form.processing">
                <span v-if="form.processing" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Creating account...
                </span>
                <span v-else>Create Account</span>
            </button>
        </form>

        <template #footer>
            Already have an account?
            <Link :href="route('login')" class="text-white font-medium hover:underline ml-1">Sign in</Link>
        </template>
    </AuthLayout>
</template>
