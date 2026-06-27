import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h } from 'vue';

import PrimeVue from 'primevue/config';
import tooltipDirective from '@/Directives/tooltipDirective.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) => createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title ? title + ' - ' : ''}${appName}`,
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ App, props, plugin }) {
        const ziggyData = page.props?.ziggy;
        const ziggyRoutes = ziggyData?.routes || {};

        const app = createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, { ssr: true });

        // Build URLs from ziggyData.routes, bypassing Ziggy Router at runtime.
        const ssrRoute = (name, params) => {
            try {
                const def = ziggyRoutes[name];
                if (!def) return '#';
                let url = def.uri;
                if (params && typeof params === 'object') {
                    for (const [k, v] of Object.entries(params)) {
                        url = url.replace(`{${k}}`, encodeURIComponent(v));
                        url = url.replace(`{${k}?}`, encodeURIComponent(v));
                    }
                }
                url = url.replace(/\/\{[^}?]+\?\}/g, '');
                return url.startsWith('/') ? url : '/' + url;
            } catch {
                return '#';
            }
        };

        app.config.globalProperties.route = ssrRoute;
        app.config.globalProperties.$route = ssrRoute;
        app.directive('tooltip', tooltipDirective);

        return app;
    }
}));
