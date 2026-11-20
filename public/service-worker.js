const CACHE_NAME = 'dor-public-static-v3';

const STATIC_ASSETS = [
    '/assets/pwa/icon-192.png',
    '/assets/pwa/icon-512.png',
    '/assets/pwa/icon-maskable-192.png',
    '/assets/pwa/icon-maskable-512.png',
    '/favicon.png',
    '/apple-touch-icon.png',
    '/assets/css_2021/bootstrap.min.css',
    '/assets/css_2021/fontawesome-all.min.css',
    '/assets/css_2021/slick.min.css',
    '/assets/css_2021/slick-theme.min.css',
    '/assets/css_2021/style.css',
    '/assets/css_2021/responsive.css',
    '/assets/css_2021/website-theme.css',
    '/assets/js_2021/jquery.min.js',
    '/assets/js_2021/bootstrap.bundle.min.js',
    '/assets/js_2021/acmeticker.min.js',
    '/assets/js_2021/slick.min.js',
    '/assets/js_2021/script.js'
];

self.addEventListener('install', event => {
    event.waitUntil((async () => {
        const cache = await caches.open(CACHE_NAME);
        await Promise.all(
            STATIC_ASSETS.map(async path => {
                try {
                    const response = await fetch(path, { cache: 'reload' });
                    if (response.ok && response.type === 'basic') {
                        await cache.put(path, response);
                    }
                } catch (_) {
                    // A missing optional asset must not block installation.
                }
            })
        );
    })());
});

self.addEventListener('activate', event => {
    event.waitUntil((async () => {
        const keys = await caches.keys();
        await Promise.all(
            keys
                .filter(key => key.startsWith('dor-public-') && key !== CACHE_NAME)
                .map(key => caches.delete(key))
        );
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', event => {
    const request = event.request;

    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    // Always request HTML pages from the server; never cache dynamic pages.
    if (request.mode === 'navigate' || request.destination === 'document') {
        event.respondWith(fetch(request, { cache: 'no-store' }));
        return;
    }

    if (!STATIC_ASSETS.includes(url.pathname)) return;

    event.respondWith((async () => {
        const cache = await caches.open(CACHE_NAME);

        try {
            const response = await fetch(request, { cache: 'no-store' });
            if (response.ok && response.type === 'basic') {
                await cache.put(request, response.clone());
            }
            return response;
        } catch (error) {
            const cached = await cache.match(request);
            if (cached) return cached;
            throw error;
        }
    })());
});
