<?php

use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/functions.php';
Api::__constructStatic();
session_start();
$flights = null;
$res = Api::sendAsync('GET', 'v1/map/acars', null);
if ($res->getStatusCode() == 200) {
    $flights = json_decode($res->getBody(), true);
}
if (!empty($flights)) {
    foreach ($flights as &$flight) {
        $totalDistance = round(get_distance(floatval($flight["startLong"]), floatval($flight["startLat"]), floatval($flight["arrLong"]), floatval($flight["arrLat"])));
        $distanceFlown = round(get_distance(floatval($flight["startLong"]), floatval($flight["startLat"]), floatval($flight["presLong"]), floatval($flight["presLat"])));
        $distanceRemaining = get_distance(floatval($flight["presLong"]), floatval($flight["presLat"]), floatval($flight["arrLong"]), floatval($flight["arrLat"]));
        $etaMins = 0;
        $etaHours = 0;
        if ($flight["statSpeed"] > 30) {
            $etaMins = round($distanceRemaining) / $flight["statSpeed"] * 60;
            $etaHours = intdiv(intval($etaMins), 60);
            $etaMins -= ($etaHours * 60);
        }
        $flight["total_dist"] = $totalDistance;
        $flight["dist_flown"] = $distanceFlown;
        $flight["eta"] = str_pad($etaHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad(round($etaMins), 2, '0', STR_PAD_LEFT);
        $flight["perc_complete"] = $totalDistance - $distanceFlown > 5 ? round($distanceFlown / $totalDistance * 100) : 100;
        $flight["dtg"] = round($distanceRemaining, 1) < 1 ? 0 : round($distanceRemaining, 1);
        $flight["statHdg"] = str_pad($flight["statHdg"], 3, '0', STR_PAD_LEFT);
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="assets/plugins/leaflet/leaflet.css" />
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.js"></script>
<style>
/* Route Map Section Styles with Parallax Background */
.live-flights-section {
    position: relative;
    padding: 80px 0;
    min-height: calc(100vh - 128px);
    background-image: url('./assets/images/backgrounds/world_map2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.live-flights-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.live-flights-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}

/* Route Map Title - Outside Card */
.live-flights-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
}

.live-flights-title {
    font-size: 3rem;
    font-weight: 300;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.9);
}

.live-flights-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Glassmorphism Cards */
.live-flights-glass-card {
    background: rgba(255, 255, 255, 0.50);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
}

.live-flights-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Header Content Styling */
.live-flights-header {
    padding: 30px;
    text-align: center;
    color: black;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.live-flights-header h1 {
    font-size: 2.5rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #000;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.live-flights-header hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 15px auto;
    width: 80%;
    border-radius: 2px;
}

.live-flights-header p {
    font-size: 1.5rem;
    line-height: 1.6;
    margin-bottom: 15px;
    color: rgba(0, 0, 0, 0.9);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.live-flights-header strong {
    color: rgba(255, 215, 0, 1);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Map Container */
.map-container-glass {
    background: rgba(255, 255, 255, 0.50);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
    transition: all 0.3s ease;
}

.map-container-glass:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

#map {
    height: 600px;
    border-radius: 15px;
    overflow: hidden;
}

/* Flights Table Glass Card */
.flights-table-glass-card {
    background: rgba(255, 255, 255, 0.50);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.flights-table-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Table Header */
.flights-table-header {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.flights-table-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.flights-table-header .fa-plane {
    color: rgba(255, 215, 0, 1);
    font-size: 1.5rem;
}

/* Table Wrapper */
.flights-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

/* Custom scrollbar for webkit browsers */
.flights-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.flights-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.flights-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.flights-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}

/* Table Styles */
.flights-table {
    margin: 0;
    background: transparent;
    color: rgba(0,0,0,0.7);
    width: 100%;
}

.flights-table thead th {
    color: rgba(0,0,0,0.9);
    font-weight: 600;
    border: none !important;
    padding: 15px 12px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
    text-wrap: nowrap;
}

.flights-table tbody tr {
    background: rgba(255, 255, 255, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
    transition: all 0.3s ease;
}

.flights-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95) !important;
    transform: scale(1.01);
}

.flights-table tbody td {
    padding: 15px 12px;
    border: none !important;
    vertical-align: middle;
    font-size: 14px;
    color: rgba(0,0,0,0.7);
}

.flights-table tbody tr:last-child {
    border-bottom: none !important;
}

/* Progress Bar */
.progress {
    height: 20px;
    margin: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 193, 7, 0.9));
    transition: width 0.3s ease;
}

/* No Data Message */
.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: rgba(0,0,0,0.7);
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
}

/* Cards Grid Layout */
.cards-grid {
    display: grid;
    gap: 20px;
    margin: 0 auto;
    max-width: 100%;
}

.card-item {
    width: 100%;
}

/* Responsive Grid Layout */
@media (max-width: 767px) {
    .cards-grid {
        grid-template-columns: repeat(1, 1fr);
        justify-content: center;
    }
    .card-item {
        width: 100%;
        max-width: 100%;
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .cards-grid {
        grid-template-columns: repeat(2, 1fr);
        justify-content: center;
    }
    .card-item {
        width: calc(50% - 10px);
        max-width: 280px;
    }
}

@media (min-width: 992px) {
    .cards-grid {
        grid-template-columns: repeat(3, 1fr);
        justify-content: center;
    }
    .card-item {
        width: 100%;
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .live-flights-section {
        padding: 80px 0;
    }
    .live-flights-title {
        font-size: 2.5rem;
    }
    .live-flights-icon {
        font-size: 2rem;
    }
    .live-flights-header h1 {
        font-size: 2.2rem;
    }
    .flights-table thead th,
    .flights-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .live-flights-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 80px;
    }
    .live-flights-title {
        font-size: 2.2rem;
    }
    .live-flights-icon {
        font-size: 2rem;
    }
    .live-flights-header h1 {
        font-size: 2rem;
    }
    .live-flights-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    #map {
        height: 500px;
        border-radius: 12px;
    }
    .flights-table-wrapper {
        max-height: 500px;
    }
}

@media (max-width: 768px) {
    .live-flights-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .live-flights-title {
        font-size: 2rem;
    }
    .live-flights-icon {
        font-size: 1.8rem;
    }
    .live-flights-header {
        padding: 25px 20px;
    }
    .live-flights-header h1 {
        font-size: 1.8rem;
    }
    .live-flights-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    #map {
        height: 400px;
        border-radius: 10px;
    }
    .flights-table-wrapper {
        max-height: 400px;
    }
    .flights-table {
        font-size: 12px;
    }
    .flights-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    .flights-table tbody td {
        padding: 12px 8px;
        font-size: 12px;
        white-space: nowrap;
    }
    .flights-table-header {
        padding: 15px 20px;
    }
    .flights-table-header h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .live-flights-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 50px;
    }
    .live-flights-title {
        font-size: 1.8rem;
    }
    .live-flights-icon {
        font-size: 1.5rem;
    }
    .live-flights-title-wrapper {
        gap: 10px;
    }
    .live-flights-header {
        padding: 20px 15px;
    }
    .live-flights-header h1 {
        font-size: 1.6rem;
    }
    .live-flights-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    #map {
        height: 350px;
        border-radius: 8px;
    }
    .flights-table-wrapper {
        max-height: 350px;
    }
    .flights-table-header {
        padding: 12px 15px;
    }
    .flights-table-header h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .live-flights-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 40px;
    }
    .live-flights-title {
        font-size: 1.6rem;
    }
    .live-flights-icon {
        font-size: 1.3rem;
    }
    #map {
        height: 300px;
    }
    .flights-table-wrapper {
        max-height: 300px;
    }
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
}

/* Print Styles */
@media print {
    .live-flights-section {
        background: white;
        padding: 80px 0;
    }
    .live-flights-section::before {
        display: none;
    }
    .live-flights-title,
    .live-flights-header h1,
    .live-flights-header p {
        color: black;
        text-shadow: none;
    }
    .live-flights-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    .flights-table thead th,
    .flights-table tbody td {
        color: white;
    }
}
</style>
<section id="content" class="live-flights-section ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="live-flights-glass-card">
                    <div class="live-flights-header">
                        <div class="live-flights-title-wrapper">
                            <h3 class="live-flights-title">Live Flights</h3>
                            <i class="fa fa-plane live-flights-icon" aria-hidden="true"></i>
                        </div>
                        <hr />
                        <p>There are currently <strong><?php echo count($flights); ?> active</strong> flights.</p>
                        <p>Click on a <strong>Flight Number</strong> in the <strong>Flight List</strong> below to open the flight on the map and view more detailed information about the flight.</p>
                        <p>Use the controls in the top left of the map to zoom in and out.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="map-container-glass">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="flights-table-glass-card">
                    <div class="flights-table-header">
                        <h3><i class="fa fa-plane"></i> Flight List</h3>
                    </div>
                    <div class="flights-table-wrapper">
                        <table class="table table-striped flights-table">
                            <thead>
                                <tr>
                                    <th>Pilot</th>
                                    <th>Flight Number</th>
                                    <th>Departure</th>
                                    <th>Arrival</th>
                                    <th>Aircraft</th>
                                    <th>Stage</th>
                                    <th>DTG</th>
                                    <th>ETA</th>
                                    <th style="min-width:100px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($flights)) { ?>
                                    <?php foreach ($flights as &$flight) { ?>
                                        <tr>
                                            <td><a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $flight['pilotId']; ?>"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                    <?php echo explode(" ", $flight['name'])[0]; ?>
                                                    <span class="small">(ID: <?php echo $flight['callsign']; ?>)</span></a>
                                            </td>
                                            <td><a href="" class="marker" data-id="<?php echo $flight['actmpId']; ?>"><i
                                                        class="fa fa-external-link"></i>
                                                    <?php echo $flight['flightNumber']; ?></a>
                                            </td>
                                            <td><?php echo $flight['depIcao']; ?>
                                            </td>
                                            <td><?php echo $flight['arrIcao']; ?>
                                            </td>
                                            <td><span
                                                    title="<?php echo $flight['aircraftTypeId']; ?>"><?php echo limit($flight['aircraftTypeId'], 20); ?></span>
                                            </td>
                                            <td><strong><?php echo $flight['statStage']; ?></strong>
                                            </td>
                                            <td><?php echo $flight['dtg']; ?>nm
                                            </td>
                                            <td><?php echo $flight['eta']; ?>
                                            </td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: <?php echo (round($flight['perc_complete'], 0)); ?>%"
                                                        aria-valuenow="<?php echo (round($flight['perc_complete'], 0)); ?>"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="9" class="no-data-message">There are currently no active flights.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Leaflet map
        var map = L.map('map', {
            center: [20, 0], // Default center (adjust as needed)
            zoom: 2,
            worldCopyJump: true,
            attributionControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: ''
        }).addTo(map);

        // Load flight markers (example logic, adjust based on your data)
        if (<?php echo json_encode(!empty($flights)); ?>) {
            var flightData = <?php echo json_encode($flights); ?>;
            flightData.forEach(function(flight) {
                var marker = L.marker([flight.presLat, flight.presLong], {
                    icon: L.divIcon({
                        className: 'flight-marker',
                        html: '<div class="label_content"><span>' + flight.flightNumber + '</span></div>',
                        iconAnchor: [10, 20]
                    })
                }).addTo(map);

                marker.actmpId = flight.actmpId; // Store ID for click handling
                marker.on('click', function() {
                    // Handle marker click (e.g., open popup or focus)
                    alert('Flight ' + flight.flightNumber + ' clicked!');
                });
            });
        }

        // Handle marker clicks from the table
        $('.marker').on('click', function(e) {
            e.preventDefault();
            var marker = map.getMarkerById($(this).data("id")); // Assuming getMarkerById is defined
            if (marker) {
                marker.fire('click');
                window.scrollTo(0, 0);
            }
        });
    });

    // Placeholder for getMarkerById (implement based on your needs)
    L.Map.prototype.getMarkerById = function(id) {
        var marker = null;
        this.eachLayer(function(layer) {
            if (layer instanceof L.Marker && layer.actmpId === id) {
                marker = layer;
            }
        });
        return marker;
    };
</script>