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
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="jumbotron text-center">
                <h1>Logbook & History</h1>
                <hr />
                <p>View your flight and tour/event history in the tables below. You can also view your flight history
                    interactively on
                    the <a href="logbook_map.php">logbook map</a>.</p>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Logbook</h3>
                </div>
                <div class="panel-body">
                    <?php if ($logbook != null) { ?>
                        <table class="table table-striped" id="logbook">
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
                        You haven't flown any flights yet.
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tours / Events History</h3>
                </div>
                <div class="panel-body">
                    <?php if ($activities != null) { ?>
                        <table class="table table-striped" id="activityHistory">
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
                        You haven't flown any tours or events.
                    <?php } ?>
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