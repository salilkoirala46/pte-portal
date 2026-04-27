import { ref, computed, onUnmounted } from 'vue';

export function useTimer(initialSeconds = 0, onExpire = null) {
    const seconds  = ref(initialSeconds);
    const running  = ref(false);
    let   interval = null;

    const formatted = computed(() => {
        const m = Math.floor(seconds.value / 60).toString().padStart(2, '0');
        const s = (seconds.value % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    });

    const isWarning  = computed(() => seconds.value > 0 && seconds.value <= 10);
    const isDanger   = computed(() => seconds.value > 0 && seconds.value <= 5);
    const isExpired  = computed(() => seconds.value <= 0);

    function start(fromSeconds = null) {
        if (fromSeconds !== null) seconds.value = fromSeconds;
        running.value = true;
        interval = setInterval(() => {
            if (seconds.value <= 0) {
                stop();
                onExpire?.();
                return;
            }
            seconds.value--;
        }, 1000);
    }

    function stop() {
        running.value = false;
        clearInterval(interval);
    }

    function reset(to = initialSeconds) {
        stop();
        seconds.value = to;
    }

    onUnmounted(stop);

    return { seconds, formatted, running, isWarning, isDanger, isExpired, start, stop, reset };
}
