const CACHE_NAME = 'kasir-v1';
const urlsToCache = ['/kasir/transaksi', '/css/app.css', '/js/app.js'];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  if (event.request.url.includes('/kasir/transaksi')) {
    // Cache transaksi page
    event.respondWith(
      caches.match(event.request).then(response => response || fetch(event.request))
    );
  }
});

// Sync offline data
self.addEventListener('sync', event => {
  if (event.tag === 'sync-offline-transactions') {
    event.waitUntil(syncOfflineData());
  }
});

async function syncOfflineData() {
  // Ambil dari IndexedDB atau localStorage, POST ke /kasir/transaksi/sync-offline
  const offlineData = await getOfflineTransactions();  // Implementasikan sendiri
  if (offlineData.length > 0) {
    fetch('/kasir/transaksi/sync-offline', {
      method: 'POST',
      body: JSON.stringify({ offline_data: JSON.stringify(offlineData) }),
      headers: { 'Content-Type': 'application/json' }
    });
  }
}