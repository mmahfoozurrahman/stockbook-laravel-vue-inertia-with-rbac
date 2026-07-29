import './bootstrap';
import 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

createInertiaApp({
    title: (title) => title ? `${title} · Folio` : 'Folio',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) }).use(plugin).use(ZiggyVue).mount(el);
    },
    progress: false,
});

router.on('start', () => document.documentElement.classList.add('is-navigating'));
router.on('finish', () => document.documentElement.classList.remove('is-navigating'));
