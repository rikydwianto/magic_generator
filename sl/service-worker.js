// service-worker.js

self.addEventListener("install", function (event) {
  event.waitUntil(
    caches.open("my-cache").then(function (cache) {
      return cache.addAll(["/", "/index.php"]).catch(function (error) {
        console.error("Failed to cache resources:", error);
      });
    })
  );
});

self.addEventListener("fetch", function (event) {
  event.respondWith(
    caches.match(event.request).then(function (response) {
      return response || fetch(event.request);
    })
  );
});

self.addEventListener("activate", function (event) {
  event.waitUntil(
    caches.keys().then(function (cacheNames) {
      return Promise.all(
        cacheNames.map(function (cacheName) {
          if (cacheName !== "my-cache") {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
