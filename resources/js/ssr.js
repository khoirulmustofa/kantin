import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import { createPinia } from 'pinia';
import { useAuthStore } from '@/Stores/authStore';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';


createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) =>
            resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue'),
            ),
        setup({ App, props, plugin }) {
            const pinia = createPinia();
            const app = createSSRApp({ render: () => h(App, props) });

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
            app.use(ZiggyVue, {
                ...page.props.ziggy,
                location: new URL(page.props.ziggy.location),
            })
            app.use(pinia)
            app.use(ConfirmationService)
            app.use(ToastService)
            app.directive('tooltip', Tooltip)

            return app;
        },
    }),
);

