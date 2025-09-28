import '../css/app.css';
import '../css/main.scss';
import '../css/prime.scss';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { route as ziggyRoute, ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueGtag from 'vue-gtag-next';
import PrimeVue from 'primevue/config';
import PreloaderPwa from '@/Components/Preloader/PreloaderPwa.vue';
import tooltipPlugin from '@/Plugins/tooltipPlugin.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js');
    });
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue)
            .use(ZiggyVue, Ziggy)
            .use(VueGtag, {
                property: {
                    id: 'G-FJN078W8B8',
                    params: {
                        app_name: appName,
                        app_version: '1.0.0'
                    }
                }
            })

            .use(tooltipPlugin);
        app.config.globalProperties.$route = ziggyRoute;
        app.component('PwaLoader', PreloaderPwa);

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
        color: '#FF5722'
    }
});
// useTelegramAuth();
