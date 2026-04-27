<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ questionTypes: Array });

const form = useForm({
    question_type_id: '',
    title:            '',
    content:          '',
    additional_content: '',
    image:            null,
    audio:            null,
    audio_duration:   '',
    correct_answer:   '',
    sample_answer:    '',
    difficulty:       'medium',
    blanks:           [],
    word_bank:        [],
    paragraphs:       [],
    options:          [],
});

const selectedType = computed(() =>
    props.questionTypes.find(t => t.id == form.question_type_id)
);

// Reset complex fields when type changes
watch(() => form.question_type_id, () => {
    form.options    = [];
    form.blanks     = [];
    form.word_bank  = [];
    form.paragraphs = [];
});

function addOption() {
    form.options.push({ label: String.fromCharCode(65 + form.options.length), content: '', is_correct: false });
}
function removeOption(i) { form.options.splice(i, 1); }

function addParagraph() { form.paragraphs.push({ id: form.paragraphs.length + 1, content: '' }); }

function addBlank() {
    form.blanks.push({ id: form.blanks.length + 1, answer: '' });
}

const wordBankInput = ref('');
function addWordBankWords() {
    const words = wordBankInput.value.split(',').map(w => w.trim()).filter(Boolean);
    form.word_bank = [...new Set([...form.word_bank, ...words])];
    wordBankInput.value = '';
}

function submit() {
    form.post(route('admin.questions.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <div class="max-w-3xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.questions.index')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">Create Question</h1>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="card p-6 space-y-4">
                <h2 class="font-semibold text-gray-700">Basic Information</h2>

                <div>
                    <label class="form-label">Question Type *</label>
                    <select v-model="form.question_type_id" required class="form-input">
                        <option value="">Select type…</option>
                        <optgroup v-for="module in ['speaking','reading','writing','listening']" :key="module" :label="module.toUpperCase()">
                            <option v-for="t in questionTypes.filter(x => x.module === module)" :key="t.id" :value="t.id">
                                {{ t.name }}
                            </option>
                        </optgroup>
                    </select>
                    <p v-if="form.errors.question_type_id" class="form-error">{{ form.errors.question_type_id }}</p>
                </div>

                <div>
                    <label class="form-label">Title / Question *</label>
                    <input v-model="form.title" type="text" required class="form-input" placeholder="E.g. Climate Change Overview"/>
                    <p v-if="form.errors.title" class="form-error">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="form-label">Main Content / Passage</label>
                    <textarea v-model="form.content" rows="6" class="form-input" placeholder="Paste the reading passage or text here…"/>
                </div>

                <div v-if="selectedType?.requires_audio_input">
                    <label class="form-label">Additional Content (transcript for listening)</label>
                    <textarea v-model="form.additional_content" rows="4" class="form-input"/>
                </div>

                <div>
                    <label class="form-label">Difficulty</label>
                    <select v-model="form.difficulty" class="form-input w-40">
                        <option>easy</option>
                        <option>medium</option>
                        <option>hard</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Sample Answer (model answer)</label>
                    <textarea v-model="form.sample_answer" rows="4" class="form-input" placeholder="Provide a model answer students can compare against…"/>
                </div>
            </div>

            <!-- Media -->
            <div class="card p-6 space-y-4">
                <h2 class="font-semibold text-gray-700">Media Files</h2>
                <div v-if="selectedType?.module === 'speaking' || selectedType?.slug === 'describe-image'">
                    <label class="form-label">Image</label>
                    <input type="file" accept="image/*" class="form-input" @change="form.image = $event.target.files[0]"/>
                </div>
                <div v-if="selectedType?.requires_audio_input || selectedType?.requires_audio_response === false">
                    <label class="form-label">Audio File (MP3/WAV)</label>
                    <input type="file" accept=".mp3,.wav,.ogg,.m4a" class="form-input" @change="form.audio = $event.target.files[0]"/>
                    <input v-model="form.audio_duration" type="number" placeholder="Duration in seconds" class="form-input mt-2 w-48"/>
                </div>
            </div>

            <!-- MCQ Options -->
            <div v-if="selectedType?.has_options" class="card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">Answer Options</h2>
                    <button type="button" @click="addOption" class="btn-secondary btn-sm">+ Add option</button>
                </div>
                <div v-for="(opt, i) in form.options" :key="i" class="flex items-center gap-3">
                    <span class="font-bold text-gray-500 w-6">{{ opt.label }}.</span>
                    <input v-model="opt.content" type="text" class="form-input flex-1" placeholder="Option text…"/>
                    <label class="flex items-center gap-1 text-sm text-emerald-700 cursor-pointer">
                        <input type="checkbox" v-model="opt.is_correct" class="accent-emerald-600"/> Correct
                    </label>
                    <button type="button" @click="removeOption(i)" class="text-red-400 hover:text-red-600">✕</button>
                </div>
                <p v-if="!form.options.length" class="text-sm text-gray-400">No options added yet.</p>
            </div>

            <!-- Fill blanks config -->
            <div v-if="['reading-fill-blanks','reading-writing-fill-blanks','listening-fill-blanks'].includes(selectedType?.slug)" class="card p-6 space-y-4">
                <h2 class="font-semibold text-gray-700">Blank Answers</h2>
                <p class="text-xs text-gray-500">In your content, use {1}, {2}… to mark blank positions.</p>
                <div v-for="(b, i) in form.blanks" :key="i" class="flex items-center gap-3">
                    <span class="text-gray-500 text-sm w-16">Blank {{ '{' + b.id + '}' }}</span>
                    <input v-model="b.answer" type="text" class="form-input flex-1" placeholder="Correct word"/>
                </div>
                <button type="button" @click="addBlank" class="btn-secondary btn-sm">+ Add blank</button>

                <div v-if="selectedType?.slug === 'reading-fill-blanks'">
                    <h3 class="font-medium text-gray-700 mt-4 mb-2">Word Bank</h3>
                    <div class="flex gap-2">
                        <input v-model="wordBankInput" type="text" class="form-input flex-1" placeholder="word1, word2, word3 (comma-separated)"/>
                        <button type="button" @click="addWordBankWords" class="btn-secondary btn-sm">Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span v-for="w in form.word_bank" :key="w" class="bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full">{{ w }}</span>
                    </div>
                </div>
            </div>

            <!-- Reorder paragraphs -->
            <div v-if="selectedType?.slug === 'reorder-paragraphs'" class="card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">Paragraphs (correct order)</h2>
                    <button type="button" @click="addParagraph" class="btn-secondary btn-sm">+ Add</button>
                </div>
                <div v-for="(p, i) in form.paragraphs" :key="i" class="flex gap-3">
                    <span class="text-gray-400 text-sm w-6">{{ i + 1 }}.</span>
                    <textarea v-model="p.content" rows="2" class="form-input flex-1"/>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <Link :href="route('admin.questions.index')" class="btn-secondary">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn-primary px-8">
                    {{ form.processing ? 'Saving…' : 'Create Question' }}
                </button>
            </div>
        </form>
    </div>
</template>
