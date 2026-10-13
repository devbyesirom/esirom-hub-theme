/*
 * Agency Hub service worker
 *
 * Strategy:
 *  - Navigations (HTML pages): network-first, falling back to the cached copy
 *    and finally the offline page. This guarantees a new theme deploy is
 *    picked up on the next load instead of serving stale HTML from cache.
 *  - Same-origin static assets (theme CSS/JS/images): stale-while-revalidate.
 *  - API requests and cross-origin requests are never intercepted.
 *
 * The cache name is derived from the `v` query parameter the theme appends
 * when registering (`sw.js?v=<theme version>`), so every release gets a clean
 * cache and old ones are removed on activate.
 */

const SW_VERSION = (() => {
  try {
    return new URL(self.location.href).searchParams.get('v') || 'dev';
  } catch (e) {
    return 'dev';
  }
})();
const CACHE_NAME = `esirom-hub-${SW_VERSION}`;
const THEME_PATH = (() => {
  // .../wp-content/themes/<theme>/sw.js -> .../wp-content/themes/<theme>
  const path = new URL(self.location.href).pathname;
  return path.substring(0, path.lastIndexOf('/'));
})();
const OFFLINE_URL = `${THEME_PATH}/offline.html`;
const ICON_URL = `${THEME_PATH}/img/agencyhub-icon.png`;

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll([OFFLINE_URL, ICON_URL]))
      .catch(() => { /* offline page is best-effort */ })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((names) => Promise.all(
      names.filter((name) => name.startsWith('esirom-hub-') && name !== CACHE_NAME)
        .map((name) => caches.delete(name))
    )).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;          // CDNs, fonts, backend API on another host
  if (url.pathname.includes('/api/')) return;               // never cache API responses
  if (url.pathname.startsWith('/wp-admin') || url.pathname.startsWith('/wp-login')) return;

  if (request.mode === 'navigate') {
    event.respondWith(networkFirst(request));
    return;
  }

  if (url.pathname.startsWith(THEME_PATH + '/')) {
    event.respondWith(staleWhileRevalidate(request));
  }
});

async function networkFirst(request) {
  const cache = await caches.open(CACHE_NAME);
  try {
    const response = await fetch(request);
    if (response && response.ok) {
      cache.put(request, response.clone()).catch(() => {});
    }
    return response;
  } catch (err) {
    const cached = await cache.match(request);
    if (cached) return cached;
    const offline = await cache.match(OFFLINE_URL);
    return offline || new Response('You are offline.', { status: 503, headers: { 'Content-Type': 'text/plain' } });
  }
}

async function staleWhileRevalidate(request) {
  const cache = await caches.open(CACHE_NAME);
  const cached = await cache.match(request);
  const network = fetch(request).then((response) => {
    if (response && response.ok) {
      cache.put(request, response.clone()).catch(() => {});
    }
    return response;
  }).catch(() => cached);
  return cached || network;
}

// Push notifications (only used if the backend sends them)
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'New update available',
    icon: ICON_URL,
    badge: ICON_URL,
    vibrate: [100, 50, 100],
    data: { dateOfArrival: Date.now() }
  };
  event.waitUntil(self.registration.showNotification('Agency Hub', options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(self.clients.openWindow('/dashboard/'));
});
