import { usePage } from '@inertiajs/vue3';

export const getUrlQuery = (key) => {
    const page = usePage();
    const queryString = page.url.split('?')[1] || '';
    const urlParams = new URLSearchParams(queryString);
    const query = Object.fromEntries(urlParams.entries());
    return query[key] ?? '';
};
