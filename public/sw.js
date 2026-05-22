const CACHE_NAME = 'sirp-parkir-v1';
const OFFLINE_URL = '/offline.html';

// Assets to cache on install
const STATIC_CACHE = [
    '/',
    '/manifest.json',
    '/offline.html',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
];

// ─── Install ───────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_CACHE).catch(() => {});
        })
    );
    self.skipWaiting();
});

// ─── Activate ──────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((k) => k !== CACHE_NAME)
                    .map((k) => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ─── Fetch ─────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and API requests
    if (request.method !== 'GET') return;
    if (url.pathname.startsWith('/api/')) return;

    // For navigation requests: network first, fallback to offline
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Cache page on success
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((c) => c.put(request, clone));
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(request).then(
                        (cached) => cached || caches.match(OFFLINE_URL)
                    )
                )
        );
        return;
    }

    // For static assets: cache first, then network
    if (
        url.pathname.match(/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff2?|ttf)$/) ||
        url.hostname.includes('cdnjs.cloudflare.com') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com')
    ) {
        event.respondWith(
            caches.match(request).then(
                (cached) =>
                    cached ||
                    fetch(request).then((response) => {
                        if (response.ok) {
                            const clone = response.clone();
                            caches.open(CACHE_NAME).then((c) => c.put(request, clone));
                        }
                        return response;
                    })
            )
        );
        return;
    }
});

// ─── Background Sync ───────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-transaksi') {
        event.waitUntil(syncPendingTransaksi());
    }
});

async function syncPendingTransaksi() {
    // Placeholder for background sync logic
    console.log('[SW] Background sync triggered');
}

// ─── Push Notifications ────────────────────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'SIRP Parkir', {
            body:    data.body || '',
            icon:    '/icons/icon-192.png',
            badge:   '/icons/icon-72.png',
            vibrate: [200, 100, 200],
            data:    { url: data.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url || '/'));
});
