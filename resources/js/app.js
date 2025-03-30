import '../css/app.css';
import '../css/main.scss';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueGtag from 'vue-gtag-next';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js');
    });
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        app.use(VueGtag, {
            property: {
                id: 'G-W17H1ZL9FX',
                params: {
                    app_name: appName,
                    app_version: '1.0.0'
                }
            }
        });

        app.config.globalProperties.$isPWA =
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone;

        app.mount(el);

        if (app.config.globalProperties.$isPWA) {
            app.$gtag.event('pwa_launch', {
                app_mode: 'standalone',
                platform: navigator.platform
            });
        }
    },
    progress: {
        color: '#4B5563'
    }
});
