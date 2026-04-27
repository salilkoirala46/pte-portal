import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'PTE Portal';

const pages = import.meta.glob('./pages/**/*.vue');

createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : appName,

    resolve: (name) => {
        const page = pages[`./pages/${name}.vue`];
        if (!page) throw new Error(`Page not found: ${name}`);
        return page();
    },

    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .mount(el);
    },

    progress: {
        color: '#2563eb',
        includeCSS: true,
        showSpinner: false,
    },
});
