<link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.css" />
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.js"></script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.rotatedMarker.js" type="text/javascript">
</script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/arc.js" type="text/javascript"></script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/map.helpers.js" type="text/javascript"></script>
<style>
#map {
    height: 600px;
    margin-bottom: 20px;
}
</style>
<script type="text/javascript">
var pathData = <?php echo $path_data == null ? "null" : json_encode($path_data); ?>;
var mapData = <?php echo json_encode($mapData); ?>;

$(document).ready(function() {
    var map = L.map('map', {
        'center': [0, 0],
        'zoom': 2,
        'worldCopyJump': true,
        'attributionControl': false
    });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: ''
    }).addTo(map);
    var flight = L.featureGroup();
    //departure label
    marker = new L.marker([
        mapData[0].lat,
        mapData[0].lng
    ], {
        icon: L.divIcon({
            className: 'iconicon',
            html: '<div class="label_content"><span>' + mapData[0].icao + '</span></div>',
            iconAnchor: [20, 30]
        })
    }).addTo(flight);
    if (mapData[2] != null) {
        //alternate label
        marker = new L.marker([
            mapData[2].lat,
            mapData[2].lng
        ], {
            icon: L.divIcon({
                className: 'iconicon',
                html: '<div class="label_content"><span>' + mapData[2].icao + '</span></div>',
                iconAnchor: [20, 30]
            })
        }).addTo(flight);
    }
    if (mapData.length > 1) {
        if (mapData[0].icao != mapData[1].icao) {
            //arrival label
            marker = new L.marker([
                mapData[1].lat,
                mapData[1].lng
            ], {
                icon: L.divIcon({
                    className: 'iconicon',
                    html: '<div><div class="label_content"><span>' + mapData[1].icao +
                        '</span></div></div>',
                    iconAnchor: [20, 30]
                })
            }).addTo(flight);
            var start = {
                x: mapData[0].lng,
                y: mapData[0].lat
            };
            var end = {
                x: mapData[1].lng,
                y: mapData[1].lat
            };
            var generator = new arc.GreatCircle(start, end);
            var line = generator.Arc(100, {
                offset: 10
            });
            var latlngs = [];
            for (var key in line.geometries[0].coords) {
                latlngs.push([line.geometries[0].coords[key][1], line.geometries[0].coords[key][0]]);
            }
            var polyline = L.polyline(latlngs, {
                color: 'red',
                weight: 2,
                dashArray: [5, 5]
            }).addTo(flight);
        }
    } else {
        map.setView([mapData[0].lat, mapData[0].lng], 5);
    }
    if (pathData != null) {
        //path data here
        const radius = -0.30;
        const SmoothPoly = L.Polyline.extend({
            _updatePath: function() {
                const path = roundPathCorners(this._parts, radius)
                this._renderer._setPath(this, path);
            }
        });
        oPath = JSON.parse(pathData);
        var latlngs = [];
        for (var key in oPath) {
            latlngs.push([oPath[key]['Latitude'], oPath[key]['Longitude']]);
        }

        var polyline = new SmoothPoly(latlngs, {
            color: '#163B4B',
            weight: 3,
        }).addTo(flight);
    }
    flight.addTo(map);
    map.fitBounds(flight.getBounds());
});
</script>