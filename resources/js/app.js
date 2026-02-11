import 'primeicons/primeicons.css';
import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import { createPinia } from 'pinia';
import { useAuthStore } from '@/Stores/authStore';

const pinia = createPinia();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: '.my-app-dark'
                }
            },
            ripple: true
        })
        app.use(plugin)
        app.use(ZiggyVue)
        app.use(pinia)
        app.use(ConfirmationService)
        app.use(ToastService)
        app.directive('tooltip', Tooltip)

        const authStore = useAuthStore();
        authStore.fetchAuth();
        app.mount(el);
    },
    progress: {
        color: '#f50707ff',
        includeCSS: true,
        showSpinner: true,
        delay: 0,
    },
});
