const map = L.map('events-map').setView([-14.2, -51.9], 4);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

const cache = JSON.parse(localStorage.getItem('geoCache') || '{}');
const bounds = [];
const eventIcon = L.divIcon({
    className: '',
    iconSize: [28, 38],
    iconAnchor: [14, 38],
    popupAnchor: [0, -34],
    html: '<svg width="28" height="38" viewBox="0 0 28 38"><path d="M14 0C6.3 0 0 6.2 0 13.9 0 24 14 38 14 38s14-14 14-24.1C28 6.2 21.7 0 14 0z" fill="#8B84D7" stroke="#fff" stroke-width="1.5"/><circle cx="14" cy="14" r="5" fill="#fff"/></svg>'
});

function showUser(me, zoom) {
    map.setView(me, zoom);
    L.circleMarker(me, { radius: 8, color: '#fff', weight: 2, fillColor: '#0d6efd', fillOpacity: 1 }).addTo(map).bindPopup('Você está aqui');
}

// Sem permissão de GPS (ou sem HTTPS), usa localização aproximada por IP.
async function locateByIp() {
    try {
        const j = await (await fetch('https://ipapi.co/json/')).json();
        if (j.latitude && j.longitude) showUser([j.latitude, j.longitude], 9);
    } catch (e) {}
}

if (!isFiltering) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            p => showUser([p.coords.latitude, p.coords.longitude], 11),
            locateByIp,
            { timeout: 8000 }
        );
    } else {
        locateByIp();
    }
}
const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function geocode(q) {
    if (!q) return null;
    if (q in cache) return cache[q];
    const r = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q));
    const j = await r.json();
    cache[q] = j.length ? [parseFloat(j[0].lat), parseFloat(j[0].lon)] : null;
    localStorage.setItem('geoCache', JSON.stringify(cache));
    await new Promise(res => setTimeout(res, 1100)); // limite de 1 req/s do Nominatim
    return cache[q];
}

(async () => {
    for (const ev of mapEvents) {
        let pos = await geocode(ev.address);
        if (!pos) pos = await geocode(ev.city);
        if (!pos) continue;
        L.marker(pos, { icon: eventIcon }).addTo(map).bindPopup('<a href="' + esc(ev.url) + '">' + esc(ev.name) + '</a>');
        bounds.push(pos);
        if (isFiltering) map.fitBounds(bounds, { padding: [30, 30], maxZoom: 14 });
    }
})();