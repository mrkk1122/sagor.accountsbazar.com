/* Sagor Photography — Service Worker */
const CACHE  = 'sagor-v1';
const STATIC = ['/', '/css/style.css', '/js/main.js', '/manifest.json'];

// Paths that must never be cached (auth/admin/download)
const NO_CACHE = ['/admin/', '/login.php', '/logout.php', '/register.php', '/download.php', '/profile.php'];

self.addEventListener('install', e => {
    e.waitUntil(caches.open(CACHE).then(c => c.addAll(STATIC)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    const path = new URL(e.request.url).pathname;
    const bypass = NO_CACHE.some(p => path.startsWith(p));
    if (bypass) return;
    e.respondWith(
        caches.match(e.request).then(cached => {
            if (cached) return cached;
            return fetch(e.request).then(res => {
                if (!res || res.status !== 200 || res.type !== 'basic') return res;
                const clone = res.clone();
                caches.open(CACHE).then(c => c.put(e.request, clone));
                return res;
            }).catch(() => caches.match('/'));
        })
    );
});
