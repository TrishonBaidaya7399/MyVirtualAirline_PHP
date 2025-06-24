<?php

use Proxy\Api\Api;

include '../lib/functions.php';
include '../config.php';
Api::__constructStatic();
session_start();
validateSession();
$id = $_SESSION['pilotid'];
$logbook = null;
$res = Api::sendSync('GET', 'v1/pilot/logbook/' . $id, null);
if ($res->getStatusCode() == 200) {
    $logbook = json_decode($res->getBody(), true);
}
if (!empty($logbook)) {
    foreach ($logbook as &$item) {
        $item["date"] = (new DateTime($item["date"]))->format('Y-m-d');
        $item["flightNumber"] = '<a href="' . website_base_url . 'pirep_info.php?id=' . $item['id'] . '" target="_blank">' . $item['flightNumber'] . '</a>';
        $item["depIcao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $item['depIcao'] . '" target="_blank">' . $item['depIcao'] . '</a>';
        $item["arrIcao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $item['arrIcao'] . '" target="_blank">' . $item['arrIcao'] . '</a>';
        $item["aircraft"] = '<span title="' . $item['aircraft'] . '">' . limit($item['aircraft'], 15, "...") . '</span>';
        $item["landingRate"] = $item['landingRate'] < 0 ? number_format($item['landingRate']) . 'fpm' : "NA";
        switch ($item["approvedStatus"]) {
            case 0:
                $item["approvedStatusDescription"] = "<span style='color:orange;'>Pending Approval</span>";
                break;
            case 1:
                $item["approvedStatusDescription"] = "<span style='color:green;'>Approved</span>";
                break;
            case 2:
                $item["approvedStatusDescription"] = "<span style='color:red;'>Denied</span>";
                break;
        }
    }
}
$activities = null;
$res = Api::sendSync('GET', 'v1/activity/history/logbook/' . $id, null);
if ($res->getStatusCode() == 200) {
    $activities = json_decode($res->getBody(), true);
}
if (!empty($activities)) {
    foreach ($activities as &$activity) {
        $activity["activity"]["title"] = '<a href="/activity.php?id=' . $activity["activityId"] . '" class="js_showloader">' . limit($activity["activity"]["title"], 25, "...") . '</a>';
        $activity["startDate"] = (new DateTime($activity["startDate"]))->format('d M Y');
        $activity["dateComplete"] = isset($activity["dateComplete"]) ? (new DateTime($activity["dateComplete"]))->format('d M Y') : "";
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="../assets/plugins/datatables/datatables.min.css" />

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.route-map-section {
    position: relative;
    padding: 90px 0;
    min-height: calc(100vh - 128px);
    background-image: url('../assets/images/backgrounds/world_map2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.route-map-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.route-map-section .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.route-map-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
}

.route-map-title {
    font-size: 4rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.route-map-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.route-map-header {
    padding: 30px;
    text-align: center;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.route-map-header h1 {
    font-size: 2.5rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-header hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 15px auto;
    width: 80%;
    border-radius: 2px;
}

.route-map-header p {
    font-size: 2rem;
    line-height: 1.6;
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.route-map-header strong {
    color: rgba(255, 215, 0, 1);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.route-map-header a {
    color: rgba(255, 215, 0, 1);
    text-decoration: none;
    transition: all 0.3s ease;
}

.route-map-header a:hover {
    color: rgba(255, 255, 255, 1);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.flights-table-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    width: 100%;
    max-width: 100%;
}

.flights-table-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.flights-table-header {
    background: rgba(255, 255, 255, 0.9);
    color: rgba(255, 255, 255, 1);
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.flights-table-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    gap: 10px;
}

.flights-table-header .fa-plane {
    color: rgba(255, 215, 0, 1);
    font-size: 1.5rem;
}

.flights-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

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

.flights-table {
    margin: 0;
    background: transparent;
    color: rgba(255, 255, 255, 0.9);
    width: 100%;
}

.flights-table thead th {
    color: rgba(255, 255, 255, 0.9);
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
    color: rgba(255, 255, 255, 0.9);
}

.flights-table tbody tr:last-child {
    border-bottom: none !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: rgba(255, 255, 255, 0.9);
    padding: 10px;
    margin: 0;
}

.dataTables_wrapper .dataTables_length {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_filter {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_info {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_paginate {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_filter input {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 6px;
    padding: 6px 10px;
    color: rgba(0, 0, 0, 0.9);
}

.paginate_button {
    color: #333 !important;
    border-radius: 4px !important;
    margin: 0 2px !important;
}

.paginate_button:hover {
    background: rgba(255, 215, 0, 1) !important;
    color: #222 !important;
}

.paginate_button.current {
    background: rgba(255, 193, 7, 1) !important;
    color: #222 !important;
}

.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #333;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
}

#logbook_wrapper .row .col-sm-6,
#activityHistory_wrapper .row .col-sm-6 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#logbook_wrapper .row .col-sm-5,
#activityHistory_wrapper .row .col-sm-5 {
    padding-left: 0 !important;
    padding-right: 0 !important;
    background: transparent !important;
    color: rgba(0, 0, 0, 0.9);
}

#logbook_wrapper .row .col-sm-7,
#activityHistory_wrapper .row .col-sm-7 {
    background: transparent !important;
    padding: 0 !important;
}

#logbook_wrapper .row .col-sm-12,
#activityHistory_wrapper .row .col-sm-12 {
    overflow-x: auto;
}

.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}

@media (max-width: 1200px) {
    .route-map-section {
        padding: 80px 0;
    }
    .route-map-title {
        font-size: 2.5rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .route-map-header h1 {
        font-size: 2.2rem;
    }
    .flights-table thead th,
    .flights-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .route-map-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .route-map-title {
        font-size: 2.2rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .route-map-header h1 {
        font-size: 2rem;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    .flights-table-wrapper {
        max-height: 500px;
    }
}

@media (max-width: 768px) {
    .route-map-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .route-map-title {
        font-size: 2rem;
    }
    .route-map-icon {
        font-size: 1.8rem;
    }
    .route-map-header {
        padding: 25px 20px;
    }
    .route-map-header h1 {
        font-size: 1.8rem;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 10px 25px 10px;
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
    .route-map-section {
        padding: 80px 0;
    }
    .route-map-title {
        font-size: 1.8rem;
    }
    .route-map-icon {
        font-size: 1.5rem;
    }
    .route-map-header {
        padding: 20px 15px;
    }
    .route-map-header h1 {
        font-size: 1.6rem;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 5px 20px 5px;
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
    .route-map-section {
        padding: 80px 0;
    }
    .route-map-title {
        font-size: 1.6rem;
    }
    .route-map-icon {
        font-size: 1.3rem;
    }
    .flights-table-wrapper {
        max-height: 300px;
    }
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
}

@media print {
    .route-map-section {
        background: white;
        padding: 20px 0;
    }
    .route-map-section::before {
        display: none;
    }
    .route-map-title,
    .route-map-header h1,
    .route-map-header p {
        color: black;
        text-shadow: none;
    }
    .route-map-glass-card,
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

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
    }
}
</style>

<section id="content" class="section route-map-section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="route-map-glass-card">
                    <div class="route-map-header">
                        <div class="route-map-title-wrapper">
                            <h3 class="route-map-title">Logbook & History</h3>
                            <i class="fa fa-book route-map-icon" aria-hidden="true"></i>
                        </div>
                        <hr />
                        <p>View your flight and tour/event history in the tables below. You can also view your flight history
                            interactively on
                            the <a href="logbook_map.php">logbook map</a>.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title"><i class="fa fa-plane" style="color: rgba(255, 193, 7, 1); rotate: -30deg;"></i> Logbook</h3>
                </div>
                <div class="flights-table-glass-card">
                    <div class="flights-table-wrapper">
                        <?php if ($logbook != null) { ?>
                            <table class="table table-striped flights-table" id="logbook">
                                <thead>
                                    <tr>
                                        <th><strong>Date</strong></th>
                                        <th><strong>Flight No.</strong></th>
                                        <th><strong>Depart</strong></th>
                                        <th><strong>Arrive</strong></th>
                                        <th><strong>Duration</strong></th>
                                        <th><strong>Aircraft</strong></th>
                                        <th><strong>Landing Rate</strong></th>
                                        <th><strong>Status</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="no-data-message">You haven't flown any flights yet.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title"><i class="fa fa-calendar" style="color: rgba(255, 193, 7, 1)"></i> Tours / Events History</h3>
                </div>
                <div class="flights-table-glass-card">
                    <div class="flights-table-wrapper">
                        <?php if ($activities != null) { ?>
                            <table class="table table-striped flights-table" id="activityHistory">
                                <thead>
                                    <tr>
                                        <th><strong>Title</strong></th>
                                        <th><strong>Type</strong></th>
                                        <th><strong>Start Date</strong></th>
                                        <th><strong>Progress %</strong></th>
                                        <th><strong>Legs Complete</strong></th>
                                        <th><strong>Date Complete</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="no-data-message">You haven't flown any tours or events.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
<script type="text/javascript" src="../assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($logbook); ?>;
    var activity = <?php echo json_encode($activities); ?>;
    $(document).ready(function() {
        $('#logbook').DataTable({
            data: dataSet,
            "pageLength": 10,
            columns: [{
                    data: "date",
                },
                {
                    data: "flightNumber",
                },
                {
                    data: "depIcao"
                },
                {
                    data: "arrIcao",
                },
                {
                    data: "duration"
                },
                {
                    data: "aircraft"
                },
                {
                    data: "landingRate",
                },
                {
                    data: "approvedStatusDescription",
                }
            ],
            colReorder: false,
            "order": [
                [0, 'desc']
            ]
        });
        $('#activityHistory').DataTable({
            data: activity,
            "pageLength": 10,
            columns: [{
                    data: "activity.title",
                },
                {
                    data: "activity.type",
                },
                {
                    data: "startDate",
                },
                {
                    data: "percentComplete",
                },
                {
                    data: "legsComplete",
                },
                {
                    data: "dateComplete",
                }
            ],
            colReorder: false,
            "order": [
                [0, 'desc'],
                [1, 'desc'],
                [2, 'desc'],
                [3, 'desc'],
                [4, 'desc'],
                [5, 'desc'],
            ]
        });
    });
</script>