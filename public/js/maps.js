// Maps for LightSense. Uses Leaflet (open source) with OpenStreetMap tiles.
(function () {
    if (typeof L === 'undefined') return;

    var TILES = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
    var ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

    function baseMap(el, center, zoom) {
        var map = L.map(el, { scrollWheelZoom: false }).setView(center, zoom);
        L.tileLayer(TILES, { maxZoom: 19, attribution: ATTRIBUTION }).addTo(map);
        return map;
    }

    // 1. Read-only maps: show where a light is
    document.querySelectorAll('.js-device-map').forEach(function (el) {
        var pos = [parseFloat(el.dataset.lat), parseFloat(el.dataset.lng)];
        var map = baseMap(el, pos, 18);
        var label = document.createElement('div');
        label.textContent = el.dataset.label;
        L.marker(pos).addTo(map).bindPopup(label).openPopup();
    });

    // 2. Pickers: click the map, drag the pin, search a place, or use my location
    document.querySelectorAll('.js-map-picker').forEach(function (box) {
        var mapEl = box.querySelector('[data-map]');
        var latIn = box.querySelector('input[name="latitude"]');
        var lngIn = box.querySelector('input[name="longitude"]');
        var status = box.querySelector('[data-status]');
        var search = box.querySelector('[data-search]');
        var marker = null;

        var hasPos = latIn.value !== '' && lngIn.value !== '';
        var map = baseMap(mapEl, hasPos ? [+latIn.value, +lngIn.value] : [20, 0], hasPos ? 17 : 2);
        box._map = map;

        function setPos(lat, lng, zoom) {
            latIn.value = lat.toFixed(7);
            lngIn.value = lng.toFixed(7);
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    var p = marker.getLatLng().wrap();
                    setPos(p.lat, p.lng);
                    status.textContent = 'Pin moved. Click Save when you are done.';
                });
            } else {
                marker.setLatLng([lat, lng]);
            }
            if (zoom) map.setView([lat, lng], zoom);
        }

        if (hasPos) setPos(+latIn.value, +lngIn.value);

        map.on('click', function (e) {
            var p = e.latlng.wrap();
            setPos(p.lat, p.lng);
            status.textContent = 'Position set. Drag the pin to adjust, then click Save.';
        });

        function find() {
            var q = search.value.trim();
            if (!q) return;
            status.textContent = 'Searching...';
            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.length) {
                        status.textContent = 'No place found. Try another name, or click the map.';
                        return;
                    }
                    setPos(parseFloat(data[0].lat), parseFloat(data[0].lon), 17);
                    status.textContent = 'Found: ' + data[0].display_name + '. Drag the pin to be exact, then click Save.';
                })
                .catch(function () {
                    status.textContent = 'Search failed. Check your internet, or click the map instead.';
                });
        }

        box.querySelector('[data-find]').addEventListener('click', find);
        search.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); find(); } // do not submit the form
        });

        box.querySelector('[data-locate]').addEventListener('click', function () {
            if (!navigator.geolocation) {
                status.textContent = 'Your browser cannot share your location. Search or click the map instead.';
                return;
            }
            status.textContent = 'Finding your location...';
            navigator.geolocation.getCurrentPosition(function (p) {
                setPos(p.coords.latitude, p.coords.longitude, 18);
                status.textContent = 'Using your current location. Drag the pin to adjust, then click Save.';
            }, function () {
                status.textContent = 'Could not get your location. Allow location access, or search instead.';
            });
        });

        box.querySelector('[data-clear]').addEventListener('click', function () {
            if (marker) { marker.remove(); marker = null; }
            latIn.value = '';
            lngIn.value = '';
            status.textContent = 'No position set. Click the map to choose one.';
        });
    });

    // Maps inside a closed <details> have no size yet. Fix them when it opens.
    document.querySelectorAll('details').forEach(function (d) {
        d.addEventListener('toggle', function () {
            d.querySelectorAll('.js-map-picker').forEach(function (p) {
                if (p._map) p._map.invalidateSize();
            });
        });
    });
})();
