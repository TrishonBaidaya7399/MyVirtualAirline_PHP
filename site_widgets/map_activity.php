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
var mapData = <?php echo $mapData == null ? "null" : json_encode($mapData); ?>;

$(document).ready(function() {
    var map = L.map('map', {
        'center': [0, 0],
        'zoom': 2,
        'worldCopyJump': true,
        'attributionControl': false
    });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 12,
        attribution: ''
    }).addTo(map);
    var flight = L.featureGroup();
    var icaoList = [];
    var latlngs = [];
    for (var x in mapData) {
        if (mapData.hasOwnProperty(x)) {
            var leg = mapData[x]
            if (leg[1] != null) {
                var start = {
                    x: leg[0].lng,
                    y: leg[0].lat
                };
                var end = {
                    x: leg[1].lng,
                    y: leg[1].lat
                };
                var generator = new arc.GreatCircle(start, end);
                var line = generator.Arc(100, {
                    offset: 10
                });
                for (var key in line.geometries[0].coords) {
                    latlngs.push([line.geometries[0].coords[key][1], line.geometries[0].coords[key][0]]);
                }
                map.fitBounds(latlngs);
                var polyline = L.polyline(latlngs, {
                    color: 'red',
                    weight: 2,
                    dashArray: [5, 5]
                }).addTo(flight);
            }
            for (var y in leg) {
                if (!icaoList.includes(leg[y].icao)) {
                    var marker = new L.marker([
                        leg[y].lat,
                        leg[y].lng
                    ], {
                        icon: L.divIcon({
                            className: 'iconicon',
                            html: '<div class="label_content"><span>' + leg[y].icao +
                                '</span></div>',
                            iconAnchor: [20, 30]
                        })
                    }).addTo(flight);
                    map.fitBounds(flight.getBounds());
                    icaoList.push(leg[y].icao);
                }
            }
        }
    }

    flight.addTo(map);


});
</script>