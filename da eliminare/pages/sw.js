const CACHE_NAME = 'geo-compass-cache-v1';
const URLS_TO_CACHE = [
  './',
  './index.php',
  'https://upload.wikimedia.org/wikipedia/commons/4/4c/Compass_rose_pale.svg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(URLS_TO_CACHE))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
