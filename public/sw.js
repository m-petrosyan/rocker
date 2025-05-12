self.addEventListener('fetch', event => {
    const isInertiaRequest = event.request.headers.get('X-Inertia');

    if (isInertiaRequest) {
        // Let browser handle Inertia navigation without interference
        return;
    }

    event.respondWith(
        fetch(event.request).then(response => {
            if (new URL(event.request.url).pathname === '/') {
                const newHeaders = new Headers(response.headers);
                newHeaders.set('X-PWA', 'ios');
                return new Response(response.body, {
                    status: response.status,
                    statusText: response.statusText,
                    headers: newHeaders
                });
            }
            return response;
        })
    );
});
