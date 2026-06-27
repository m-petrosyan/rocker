import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h } from 'vue';
import { route as ziggyRoute } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import tooltipDirective from '@/Directives/tooltipDirective.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

console.log('Starting Inertia SSR server...');

try {
    const server = createServer(async (page) => {
        console.log('PAGE DEBUG - ziggy in props:', 'ziggy' in (page.props || {}), 'props keys:', Object.keys(page.props || {}).join(','));
        try {
            return await createInertiaApp({
                page,
                render: renderToString,
                title: (title) => `${title ? title + ' - ' : ''}${appName}`,
                resolve: async (name) => {
                    console.log('Resolving page:', name);
                    const pages = import.meta.glob('./Pages/**/*.vue', { eager: false });
                    const pageComponent = await resolvePageComponent(
                        `./Pages/${name}.vue`,
                        pages
                    );
                    if (!pageComponent) {
                        console.error('Page not found:', name);
                        throw new Error(`Page ${name} not found`);
                    }
                    console.log('Resolved page component:', name);
                    return pageComponent;
                },
                setup({ App, props, plugin }) {
                    console.log('Setting up SSR app with component:', props.component || 'unknown');
                    try {
                        // Build a safe Ziggy config from page props with fallbacks
                        const ziggyData = page.props?.ziggy;
                        console.error('ZIGGY ROUTES COUNT:', ziggyData ? Object.keys(ziggyData.routes || {}).length : 0, 'defaults type:', typeof ziggyData?.defaults, Array.isArray(ziggyData?.defaults));
                        const defaultZiggy = {
                            url: 'http://localhost',
                            port: null,
                            defaults: {},
                            routes: {},
                            location: 'http://localhost',
                        };
                        const ziggyConfig = ziggyData
                            ? { ...defaultZiggy, ...ziggyData }
                            : defaultZiggy;
                        const ziggyLocation = ziggyConfig.location ?? ziggyConfig.url ?? 'http://localhost';

                        const app = createSSRApp({ render: () => h(App, props) })
                            .use(plugin)
                            .use(PrimeVue, { ssr: true });

                        // Create a route function that builds URLs directly from ziggyData.routes.
                        // This completely bypasses Ziggy's Router class which has issues during SSR.
                        const routeWithConfig = (name, params) => {
                            try {
                                const routeDef = ziggyData?.routes?.[name];
                                if (!routeDef) {
                                    console.error('SSR route not found in ziggyData:', name);
                                    return '#';
                                }
                                let url = routeDef.uri;
                                if (params && typeof params === 'object') {
                                    for (const [key, value] of Object.entries(params)) {
                                        url = url.replace(`{${key}}`, encodeURIComponent(value));
                                        url = url.replace(`{${key}?}`, encodeURIComponent(value));
                                    }
                                }
                                // Remove remaining optional parameters
                                url = url.replace(/\/{[^}?]+\?}/g, '');
                                // Ensure leading slash
                                if (!url.startsWith('/')) url = '/' + url;
                                return url;
                            } catch (e) {
                                console.error('SSR route error:', name, e.message);
                                return '#';
                            }
                        };

                        // Set both route and $route so templates and script work
                        app.config.globalProperties.route = routeWithConfig;
                        app.config.globalProperties.$route = routeWithConfig;
                        app.directive('tooltip', tooltipDirective);

                        return app;
                    } catch (setupError) {
                        console.error('Error in SSR app setup:', setupError);
                        throw setupError;
                    }
                }
            });
        } catch (error) {
            console.error('SSR Setup Error:', error);
            throw error;
        }
    });

    console.log('SSR server initialized:', server);
} catch (error) {
    console.error('SSR Server Initialization Failed:', error);
}
