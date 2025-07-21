<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mulungushi University Satellite View</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #map {
            height: 80vh;
            width: 100%;
            border-radius: 8px;
            border: 2px solid #2c5282;
        }
        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: rgba(255,255,255,0.9);
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .house-detail {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 500px;
            display: none;
        }
        .satellite-marker {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid white;
            background-size: cover;
            background-position: center;
        }
        .area-highlight {
            stroke: true;
            color: "#FF0000",
            weight: 2,
            opacity: 1,
            fillOpacity: 0.2
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row">
        <div class="col-12 p-4">
            <h1 class="text-center text-primary mb-3">
                <i class="fas fa-satellite-dish"></i> Mulungushi University Satellite View
            </h1>

            <div class="position-relative">
                <div id="map"></div>

                <div class="map-controls">
                    <div class="btn-group-vertical">
                        <button id="toggle-satellite" class="btn btn-sm btn-dark mb-1">
                            <i class="fas fa-satellite"></i> Toggle Satellite
                        </button>
                        <button id="highlight-sabbatical" class="btn btn-sm btn-warning mb-1">
                            <i class="fas fa-map-marker-alt"></i> Sabbatical Area
                        </button>
                        <button id="highlight-road" class="btn btn-sm btn-info">
                            <i class="fas fa-road"></i> Great North Road
                        </button>
                    </div>
                </div>

                <div class="house-detail" id="house-detail">
                    <div class="d-flex justify-content-between">
                        <h5 id="house-title" class="mb-0"></h5>
                        <button class="btn-close" id="close-detail"></button>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-home"></i> <strong>Address:</strong> <span id="house-address"></span></p>
                            <p><i class="fas fa-tag"></i> <strong>Area Type:</strong> <span id="house-area"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-tint"></i> <strong>Water Usage:</strong> <span id="house-usage"></span></p>
                            <p><i class="fas fa-user"></i> <strong>Resident:</strong> <span id="house-resident"></span></p>
                        </div>
                    </div>
                    <div id="house-image-container" class="mt-2 text-center"></div>
                    <button class="btn btn-sm btn-primary w-100 mt-2" id="navigate-btn">
                        <i class="fas fa-directions"></i> Get Directions
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    // Initialize map with satellite view
    const map = L.map('map').setView([{!! $center['lat'] !!}, {!! $center['lng'] !!}], 16);

    // Satellite imagery with labels
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    });

    // Hybrid view (satellite with labels)
    const hybridLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    }).addTo(map);

    // Regular street map
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    });

    // Important locations
    const importantLocations = {!! json_encode($important_locations) !!};
    const locationsLayer = L.layerGroup();

    Object.entries(importantLocations).forEach(([name, coords]) => {
        L.marker(coords, {
            icon: L.divIcon({
                className: 'location-icon',
                html: `<div style="background-color: #2c5282; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; justify-content: center; align-items: center; font-weight: bold;">${name.charAt(0)}</div>`,
                iconSize: [24, 24]
            })
        })
        .bindPopup(`<b>${name}</b>`)
        .addTo(locationsLayer);
    });
    locationsLayer.addTo(map);

    // Area highlighting
    const sabbaticalArea = L.polygon([
        [-14.2945, 28.552], [-14.2945, 28.554],
        [-14.293, 28.554], [-14.293, 28.552]
    ], {
        color: '#d69e2e',
        weight: 2,
        fillOpacity: 0.1
    }).bindPopup("Sabbatical Area");

    const greatNorthRoad = L.polyline([
        [-14.293, 28.549], [-14.293, 28.551]
    ], {
        color: '#4a5568',
        weight: 4,
        opacity: 0.7
    }).bindPopup("Great North Road");

    // Process household data
    const mapData = {!! json_encode($mapData) !!};
    const housesLayer = L.layerGroup();

    function getUsageColor(usage) {
        if (usage <= 400) return '#38a169';
        if (usage <= 600) return '#dd6b20';
        return '#e53e3e';
    }

    mapData.forEach(house => {
        const color = getUsageColor(house.usage);
        const marker = L.circleMarker([house.lat, house.lng], {
            radius: 8,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        })
        .bindPopup(`
            <b>${house.address}</b><br>
            <small>${house.area_type}</small><br>
            <span style="color:${color}">● ${house.usage} liters</span>
        `)
        .on('click', function() {
            document.getElementById('house-title').textContent = house.address;
            document.getElementById('house-address').textContent = house.address;
            document.getElementById('house-area').textContent = house.area_type;
            document.getElementById('house-usage').innerHTML =
                `<span style="color:${color}">${house.usage} liters</span>`;
            document.getElementById('house-resident').textContent = house.label;

            const imgContainer = document.getElementById('house-image-container');
            imgContainer.innerHTML = '';
            if (house.building_image) {
                imgContainer.innerHTML = `
                    <img src="${house.building_image}"
                         class="img-fluid rounded mb-2"
                         alt="Building photo"
                         style="max-height: 150px;">
                `;
            }

            document.getElementById('navigate-btn').onclick = function() {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=${house.lat},${house.lng}`);
            };
            document.getElementById('house-detail').style.display = 'block';
        });

        housesLayer.addLayer(marker);
    });
    housesLayer.addTo(map);

    // Layer control
    const baseLayers = {
        "Satellite": satelliteLayer,
        "Hybrid": hybridLayer,
        "Street Map": streetLayer
    };

    const overlays = {
        "Important Locations": locationsLayer,
        "Households": housesLayer,
        "Sabbatical Area": sabbaticalArea,
        "Great North Road": greatNorthRoad
    };

    L.control.layers(baseLayers, overlays, {position: 'topright'}).addTo(map);

    // UI Controls
    document.getElementById('toggle-satellite').addEventListener('click', function() {
        if (map.hasLayer(hybridLayer)) {
            map.removeLayer(hybridLayer);
            satelliteLayer.addTo(map);
        } else if (map.hasLayer(satelliteLayer)) {
            map.removeLayer(satelliteLayer);
            streetLayer.addTo(map);
        } else {
            map.removeLayer(streetLayer);
            hybridLayer.addTo(map);
        }
    });

    document.getElementById('highlight-sabbatical').addEventListener('click', function() {
        if (map.hasLayer(sabbaticalArea)) {
            map.removeLayer(sabbaticalArea);
        } else {
            sabbaticalArea.addTo(map);
            map.fitBounds(sabbaticalArea.getBounds());
        }
    });

    document.getElementById('highlight-road').addEventListener('click', function() {
        if (map.hasLayer(greatNorthRoad)) {
            map.removeLayer(greatNorthRoad);
        } else {
            greatNorthRoad.addTo(map);
            map.fitBounds(greatNorthRoad.getBounds());
        }
    });

    document.getElementById('close-detail').addEventListener('click', function() {
        document.getElementById('house-detail').style.display = 'none';
    });

    // Add scale control
    L.control.scale({position: 'bottomleft'}).addTo(map);
</script>
</body>
</html>
