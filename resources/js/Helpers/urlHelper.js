import { usePage } from '@inertiajs/vue3';

export const getUrlQuery = (key) => {
    const page = usePage();
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get(key);
};


