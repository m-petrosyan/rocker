export let isSSR;

export function checkSSR() {
  isSSR = typeof window === 'undefined';
}

checkSSR();