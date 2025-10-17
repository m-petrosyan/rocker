import axios from 'axios';
import { router } from '@inertiajs/vue3';

export function useTelegramAuth(redirect = false) {
  const initData = isWebApp(true);
  if (initData) {
    const params = new URLSearchParams(initData);
    const userParam = params.get('user');
    if (userParam) {
      const user = JSON.parse(decodeURIComponent(userParam));
      axios.post('/telegram/auth', { id: user.id })
        .then(res => {
          if (redirect && res.data?.redirect) {
            const url = new URL(res.data.redirect, window.location.origin);
            router.visit(url.pathname + url.search);
          }
        });
    }
  }
}

export function isWebApp(returnInitData = false) {
  if (typeof window === 'undefined') {
    return returnInitData ? null : false;
  }

  const initData = window?.Telegram?.WebApp?.initData;
  return returnInitData ? initData : !!initData;
}
