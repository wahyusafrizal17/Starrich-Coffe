const CACHE_NAME = 'starrich-pos-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE = [
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    if (url.origin !== location.origin) {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((res) => {
                    if (res && res.ok) {
                        const copy = res.clone();
                        caches.open(CACHE_NAME).then((c) => c.put(event.request, copy));
                    }
                    return res;
                })
                .catch(() =>
                    caches.match(event.request).then((cached) => cached || caches.match(OFFLINE_URL))
                )
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) =>
            cached ||
            fetch(event.request)
                .then((res) => {
                    if (res && res.ok && event.request.url.startsWith(self.location.origin)) {
                        const copy = res.clone();
                        caches.open(CACHE_NAME).then((c) => c.put(event.request, copy));
                    }
                    return res;
                })
                .catch(() => cached)
        )
    );
});
