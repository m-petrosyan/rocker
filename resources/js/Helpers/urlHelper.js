import { usePage } from '@inertiajs/vue3';

export const getUrlQuery = (key) => {
  const page = usePage();
  const queryString = page.url.split('?')[1] || '';
  const urlParams = new URLSearchParams(queryString);
  const query = Object.fromEntries(urlParams.entries());
  return query[key] ?? null;
};

export const fullUrl = () => {
  // const base = window.location.origin;
  // return base + page.url;
  if (typeof window !== 'undefined') {
    const url = new URL(window.location.href);
    return url.href;
  }
};

export const getHostname = (url) => {
  try {
    const hostname = new URL(url).hostname;
    return hostname.startsWith('www.') ? hostname.slice(4) : hostname;
  } catch (e) {
    return url;
  }
};
