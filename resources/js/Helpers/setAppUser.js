import axios from 'axios';
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

export function useTelegramAuth() {
    onMounted(() => {
        const initData = window.Telegram?.WebApp?.initData;
        if (initData) {
            const params = new URLSearchParams(initData);
            const userParam = params.get('user');
            if (userParam) {
                const user = JSON.parse(decodeURIComponent(userParam));
                axios.post('/telegram/auth', { id: user.id })
                    .then(res => {
                        if (res.data?.redirect) {
                            const url = new URL(res.data.redirect, window.location.origin);
                            router.visit(url.pathname + url.search);

                            // router.visit(res.data.redirect);
                        }
                    });
            }
        }
    });
}
