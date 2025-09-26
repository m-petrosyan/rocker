import axios from 'axios';
import { router } from '@inertiajs/vue3';

export function useTelegramAuth() {
    const initData = webApp(true);
    if (initData) {
        const params = new URLSearchParams(initData);
        const userParam = params.get('user');
        if (userParam) {
            const user = JSON.parse(decodeURIComponent(userParam));
            axios.post('/telegram/auth', { id: user.id })
                .then(res => {
                    if (res.data?.redirect) {
                        const url = new URL(res.data.redirect, window.location.origin);
                        // setTimeout(() => {
                        router.visit(url.pathname + url.search);
                        // }, 300);
                        // router.visit(res.data.redirect);
                    }
                });
        }
    }
}

export function webApp(returnInitData = false) {
    const initData = window.Telegram?.WebApp?.initData;

    return returnInitData ? initData : !!initData;
}
