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
var map;
$(document).ready(function() {
    var intervalId = null;
    map = L.map('map', {
        'center': [0, 0],
        'zoom': 2,
        'worldCopyJump': true,
        'attributionControl': false,
        'scrollWheelZoom': false
    });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: ''
    }).addTo(map);
    intervalId = setInterval(function() {
        loadMarkers();
    }, 60000);
    loadMarkers();

    function loadMarkers() {
        var curPlanes = map.getFeatureGroupById(2)
        if (curPlanes != null) {
            map.removeLayer(curPlanes);
            console.log("removing layers")
        }
        $.ajax({
            url: '<?php echo website_base_url; ?>includes/map_data.php',
            data: "",
            cache: false,
            dataType: 'json',
            success: function(data) {
                var planes = L.featureGroup().addTo(map);
                planes.id = 2;
                planes.clearLayers();
                for (var key in data) {
                    var popup = L.popup({
                            offset: [0, -20]
                        })
                        .setContent(
                            '<p>Flight No: <strong style="color:#17baef; font-size:18px">' +
                            data[key]['flightNumber'] +
                            '</strong><br /><strong style="color:#17baef; font-size:18px">' +
                            data[key]['depIcao'] +
                            '</strong> <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> <strong style="color:#17baef; font-size:18px">' +
                            data[key]['arrIcao'] +
                            '</strong><br /><i class="fa fa-user" aria-hidden="true"></i> ' + data[
                                key]['nameShort'] +
                            ' (ID: <strong>' + data[key]['callsign'] + '</strong>)<br />' +
                            'Altitude: <strong>' +
                            data[
                                key]['statAltitude'] + 'ft</strong><br />' + 'Heading: <strong>' +
                            data[
                                key]
                            ['statHdg'] +
                            '&deg;</strong><br />' + 'Ground Speed: <strong>' + data[key][
                                'statSpeed'
                            ] +
                            'kt</strong><br />' +
                            '<i class="fa fa-plane" aria-hidden="true"></i> ' + data[key][
                                'aircraftTypeId'
                            ].substring(
                                0, 25) +
                            '<br />ETA: <strong>' + data[key]['eta'] + '</strong> DTG: <strong>' +
                            data[key][
                                'dtg'
                            ] +
                            'nm</strong><br />Progress: <div class="progress"><div class="progress-bar" role="progressbar" style="width: ' +
                            Math.round(data[key]['perc_complete'], 0) + '%;" aria-valuenow="' + Math
                            .round(
                                data[key]['perc_complete'], 0) +
                            '" aria-valuemin="0" aria-valuemax="100"></div></div><span style="color:#17baef; font-size:18px"><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i> ' +
                            data[key]['statStage'] + '</span></p>');

                    marker = new L.marker([data[key]['presLat'], data[key]['presLong']], {
                            rotationAngle: data[key]['statHdg'] - 45,
                            icon: L.divIcon({
                                className: 'plane-icon',
                                html: '<i class="fa fa-plane"></i>',
                                iconAnchor: [5, 23]
                            })
                        })
                        .on('click', markerClick);
                    marker.data = data[key];
                    marker.acarsId = data[key]['actmpId'];
                    marker.bindPopup(popup);
                    marker.getPopup().on('remove', markerPopupClose);
                    marker.addTo(planes);
                }
                if (data.length > 1) {
                    map.fitBounds(planes.getBounds());
                } else {
                    map.setView([data[0]['presLat'], data[0]['presLong']], 5);
                }
            }
        });
    }

    function markerPopupClose(e) {
        clearFlightData();
        intervalId = setInterval(loadMarkers, 60000);
    }

    function clearFlightData() {
        var flight = map.getFeatureGroupById(1)
        if (flight != null) {
            map.removeLayer(flight);
        }
    }

    function markerClick(e) {
        clearInterval(intervalId);
        clearFlightData()
        var flight = L.featureGroup();
        flight.id = 1;
        var data = this.data;
        //departure label
        marker = new L.marker([
            data['startLat'],
            data['startLong']
        ], {
            icon: L.divIcon({
                className: 'iconicon',
                html: '<div class="label_content"><span>' + data[
                    'depIcao'] + '</span></div>',
                iconAnchor: [20, 30]
            })
        }).addTo(flight);
        if (data['depIcao'] != data['arrIcao']) {
            //arrival label
            marker = new L.marker([
                data['arrLat'],
                data['arrLong']
            ], {
                icon: L.divIcon({
                    className: 'iconicon',
                    html: '<div><div class="label_content"><span>' + data[
                        'arrIcao'] + '</span></div></div>',
                    iconAnchor: [20, 30]
                })
            }).addTo(flight);
            var start = {
                x: data['startLong'],
                y: data['startLat']
            };
            var end = {
                x: data['arrLong'],
                y: data['arrLat']
            };
            var generator = new arc.GreatCircle(start, end);
            var line = generator.Arc(100, {
                offset: 10
            });
            var latlngs = [];
            for (var key in line.geometries[0].coords) {
                latlngs.push([line.geometries[0].coords[key][1], line.geometries[0].coords[key][0]])
            }
            map.fitBounds(latlngs);
            var polyline = L.polyline(latlngs, {
                color: 'red',
                weight: 2,
                dashArray: [5, 5]
            }).addTo(flight);
        }
        $.ajax({
            url: '<?php echo website_base_url; ?>includes/path_data.php',
            data: {
                id: data['actmpId']
            },
            context: this,
            cache: false,
            type: 'post',
            success: function(path_data) {
                const radius = -0.30
                const SmoothPoly = L.Polyline.extend({
                    _updatePath: function() {
                        const path = roundPathCorners(this._parts, radius)
                        this._renderer._setPath(this, path);
                    }
                })
                var marker = map.getMarkerById(data['actmpId']);
                var pos = marker.getLatLng();
                oPath = JSON.parse(path_data);
                var latlngs = [];
                for (var key in oPath) {
                    latlngs.push([oPath[key]['Latitude'], oPath[key]['Longitude']])
                }
                if (latlngs.length) {
                    marker.setLatLng(latlngs[latlngs.length - 1])
                }
                var polyline = new SmoothPoly(latlngs, {
                    color: '#163B4B',
                    weight: 3,
                }).addTo(flight);
                flight.addTo(map)
            }
        });
    }
});
</script>