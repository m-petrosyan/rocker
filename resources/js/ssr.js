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
        console.log('Page Props:', JSON.stringify(page.props, null, 2));
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

                        // Create a route function with the config baked in via closure.
                        // Catches any Ziggy errors during SSR to prevent 502 crashes.
                        const routeWithConfig = (name, params, absolute) => {
                            try {
                                return ziggyRoute(name, params, absolute, {
                                    ...ziggyConfig,
                                    location: new URL(ziggyLocation)
                                });
                            } catch (e) {
                                console.error('SSR route fallback for:', name, e.message);
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
