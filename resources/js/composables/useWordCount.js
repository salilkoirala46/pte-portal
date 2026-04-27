import { computed } from 'vue';

export function useWordCount(text) {
    const count = computed(() => {
        const val = typeof text === 'function' ? text() : text.value;
        if (!val || !val.trim()) return 0;
        return val.trim().split(/\s+/).filter(Boolean).length;
    });

    return { count };
}
