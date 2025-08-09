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
                // alert(user);
                // router.post('/telegram/auth', { id: user });
                router.post('/telegram/auth', { id: user.id });
            }
        }
    });
}
