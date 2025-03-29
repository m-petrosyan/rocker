// Регистрация Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('ServiceWorker registration successful');

                // Проверка обновлений каждые 24 часа
                setInterval(() => {
                    registration.update().then(() => {
                        console.log('Checked for service worker update');
                    });
                }, 24 * 60 * 60 * 1000);
            })
            .catch(err => {
                console.log('ServiceWorker registration failed: ', err);
            });
    });
}

// Обработка обновлений
navigator.serviceWorker.addEventListener('controllerchange', () => {
    window.location.reload();
});
