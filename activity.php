<?php

use Proxy\Api\Api;

$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php
include 'lib/functions.php';
include 'config.php';
session_start();
Api::__constructStatic();
$activityId = cleanString($_GET['id']);
$activity = null;
$activityHistory = null;
$res = Api::sendAsync('GET', 'v1/activity/' . $activityId, null);
if ($res->getStatusCode() == 200) {
    $activity = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url);
    die();
}
if (!empty($_SESSION['pilotid'])) {
    $res = Api::sendAsync('GET', 'v1/activity/history/' . $activityId . '/' . $_SESSION['pilotid'], null);
    if ($res->getStatusCode() == 200) {
        $activityHistory = json_decode($res->getBody());
    }
}
$historyAll = null;
$res = Api::sendAsync('GET', 'v1/activity/history/' . $activityId, null);
if ($res->getStatusCode() == 200) {
    $historyAll = json_decode($res->getBody());
}
if (isset($_POST["btnattending"])) {
    $res = Api::sendSync('POST', 'v1/activity/interest/' . $activityId . '/' . $_SESSION['pilotid'], null);
    if ($res->getStatusCode() == 200) {
        if ($res == "true") {
            $pilotShownInterested = true;
        }
    }
}
if (isset($_POST["btnremoveattend"])) {
    $res = Api::sendSync('DELETE', 'v1/activity/interest/' . $activityId . '/' . $_SESSION['pilotid'], null);
    if ($res->getStatusCode() == 200) {
        if ($res == "true") {
            $pilotShownInterested = false;
        }
    }
}
$interests = null;
$pilotShownInterested = false;
$res = Api::sendAsync('GET', 'v1/activity/interest/' . $activityId, null);
if ($res->getStatusCode() == 200) {
    $interests = json_decode($res->getBody());
}
$mapData = [];
foreach ($activity->activityLegs as $key => $leg) {
    array_push($mapData, $leg->airportInfo);
}
$path_data = null;
?>
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="<?php echo website_base_url; ?>activities.php"><i class="fa fa-angle-double-left js_showloader"
                        aria-hidden="true"></i> Back to tours / events</a>
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo htmlspecialchars($activity->title); ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="activity-content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <img src="<?php echo website_base_url; ?>uploads/activities/<?php echo $activity->banner; ?>"
                    style="float:right; margin-bottom:30px;" width="350" height="350" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-<?php echo $activity->type == "Event" ? "9" : "12" ?>">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $activity->type; ?> Detail &
                            Statistics</h3>
                    </div>
                    <div class="panel-body event-panel row-space">
                        <div class="row">
                            <div class="col-md-<?php echo $activity->type == "Event" ? "6" : "4" ?> col-xs-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Start Date:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo (new DateTime($activity->startDate))->format('d M Y H:i'); ?>
                                        (UTC)
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>End Date:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo empty($activity->endDate) ? "No end date" : (new DateTime($activity->endDate))->format('d M Y H:i') . " (UTC)"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Legs Flown In Order:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $activity->legsInOrder == true ? "Yes" : "No"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Minimum Rank:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $activity->minRank != null ? $activity->minRank->name : "Any"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Completion Award:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activity->award) ? "<a href=\"/awards.php\" target=\"_blank\">" . $activity->award->name . "</a>" : "No"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Completion Bonus XP:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo empty($activity->bonusXp) ? "NA" : $activity->bonusXp; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Total Legs:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo count($activity->activityLegs); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $activity->type == "Event" ? "6" : "4" ?> col-xs-12">
                                <?php if (!empty($_SESSION['pilotid'])) { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Progress Complete:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: <?php echo isset($activityHistory) ? $activityHistory->percentComplete : "0"; ?>%;"
                                                aria-valuenow="<?php echo isset($activityHistory) ? $activityHistory->percentComplete : "0"; ?>"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <?php echo isset($activityHistory) ? $activityHistory->percentComplete : "0"; ?>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Legs Complete:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activityHistory) ? $activityHistory->legsComplete : "0"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Last Leg Flown Date:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activityHistory->lastLegFlownDate) ? (new DateTime($activityHistory->lastLegFlownDate))->format('d M Y') : "None flown"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Date Complete:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activityHistory->dateComplete) ? (new DateTime($activityHistory->dateComplete))->format('d M Y') : "NA"; ?>
                                    </div>
                                </div>
                                <?php if ($activity->type == "Tour") { ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Your Time to Complete:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activityHistory->daysToComplete) ? ($activityHistory->daysToComplete > 0 ? $activityHistory->daysToComplete . ' days' : "NA") : "NA"; ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                            <?php if ($activity->type == "Tour") { ?>
                            <div class="col-md-4 col-xs-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Time to Complete (Average all pilots):</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $activity->averageDaysToComplete > 0 ? $activity->averageDaysToComplete . ' days' : "NA"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>1st To Complete:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activity->firstPilotToComplete) ? '<i class="fa fa-trophy gold" aria-hidden="true" title="1st place"></i> ' . $activity->firstPilotToComplete : "NA"; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Completed By:</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo isset($activity->totalPilotsComplete) ? $activity->totalPilotsComplete . ' pilot(s)' : "NA"; ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($activity->type == "Event") { ?>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Interest (<?php echo count($interests); ?>)</h3>
                    </div>
                    <div class="panel-body interest-panel">
                        <div class="row interest-users">
                            <div class="col-md-12">
                                <?php if (empty($interests)) { ?>
                                Currently no interest.
                                <hr />
                                <?php } ?>
                                <?php
                                    foreach ($interests as $interest) {
                                        if (!empty($_SESSION['pilotid'])) {
                                            if ($interest->pilotId == intval($_SESSION['pilotid'])) {
                                                $pilotShownInterested = true;
                                            }
                                        } ?>
                                <div class="row">
                                    <div class="col-md-2 col-xs-3">
                                        <?php if (!empty($interest->profileImage)) { ?>
                                        <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $interest->profileImage ?>"
                                            class="img-circle pilot-profile-image-small" />
                                        <?php } else { ?>
                                        <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-10 col-xs-9">
                                        <?php echo $interest->name; ?> -
                                        <?php echo $interest->callsign; ?>
                                    </div>
                                </div>
                                <hr />
                                <?php
                                    } ?>
                            </div>
                        </div>
                        <?php if (!empty($_SESSION['pilotid'])) { ?>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <?php if (!$pilotShownInterested) { ?>
                                <form method="post" class="form">
                                    <button name="btnattending" type="submit" id="btnattending"
                                        class="btn btn-success">Show Interest
                                    </button>
                                </form>
                                <?php } else { ?>
                                <form method="post" class="form">
                                    <button name="btnremoveattend" type="submit" id="btnremoveattend"
                                        class="btn btn-warning">Remove Interest
                                    </button>
                                </form>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $activity->type; ?> Legs</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($activity->activityLegs != null) { ?>
                                <table class="table table-striped" id="activities">
                                    <thead>
                                        <tr>
                                            <th><strong>#</strong></th>
                                            <th><strong>Depart</strong></th>
                                            <th><strong>Arrive</strong></th>
                                            <th><strong>Flight Number</strong></th>
                                            <th><strong>Aircraft</strong></th>
                                            <th class="text-center">Completed</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($activity->activityLegs as $key => $leg) {
                                                $status = "";
                                                if (isset($activityHistory)) {
                                                    if (hasPropertyValue($activityHistory->activityLegHistories, 'activityLegId', $leg->id)) {
                                                        $status = "<i class=\"fa fa-check\" title=\"Leg complete\"></i>";
                                                    }
                                                } ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?>
                                            </td>
                                            <td><?php echo empty($leg->departureIcao) ? "Any" : '<a href="airport_info.php?airport=' . $leg->departureIcao . '">' . $leg->departureIcao . '</a>'; ?>
                                            </td>
                                            <td><?php echo empty($leg->arrivalIcao) ? "Any" : '<a href="airport_info.php?airport=' . $leg->arrivalIcao . '">' . $leg->arrivalIcao . '</a>'; ?>
                                            </td>
                                            <td><?php echo empty($leg->flightNumber) ? "Any" : $leg->flightNumber; ?>
                                            </td>
                                            <td><?php echo empty($leg->aircraft) ? "Any" : $leg->aircraft; ?>
                                            <td class="text-center"><?php echo $status; ?>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?php echo website_base_url; ?>activity_leg.php?id=<?php echo $leg->id; ?>"
                                                    class="btn btn-success js_showloader">View</a>
                                            </td>
                                        </tr>
                                        <?php
                                            } ?>
                                    </tbody>
                                </table>
                                <?php
                                } else { ?>
                                No legs have been created yet for this activity.
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $activity->type; ?> History</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row activity-history">
                            <div class="col-md-12">
                                <?php if (!empty($historyAll)) { ?>
                                <table class="table table-striped" id="activities">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><strong>Callsign</strong></th>
                                            <th><strong>Name</strong></th>
                                            <th><strong>Progress</strong></th>
                                            <th><strong>Legs Complete</strong></th>
                                            <th><strong>Start Date</strong></th>
                                            <th><strong>Last Flown</strong></th>
                                            <th><strong>Completion Date</strong></th>
                                            <?php if ($activity->type == "Tour") { ?>
                                            <th><strong>Time (days)</strong></th><?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($historyAll as $history) {
                                            ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($history->pilot->profileImage)) { ?>
                                                <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $history->pilot->profileImage ?>"
                                                    class="img-circle pilot-profile-image-small" />
                                                <?php } else { ?>
                                                <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $history->pilot->callsign; ?>
                                            </td>
                                            <td><a
                                                    href="<?php echo website_base_url; ?>profile.php?id=<?php echo $history->pilotId; ?>"><?php echo explode(" ", $history->pilot->name)[0]; ?></a>
                                            </td>
                                            <td>
                                                <div class="progress"
                                                    title="<?php echo isset($history->percentComplete) ? $history->percentComplete : "0"; ?>%">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: <?php echo isset($history->percentComplete) ? $history->percentComplete : "0"; ?>%;"
                                                        aria-valuenow="<?php echo isset($history->percentComplete) ? $history->percentComplete : "0"; ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $history->legsComplete; ?>
                                            </td>
                                            <td>
                                                <?php echo (new DateTime($history->startDate))->format('d M Y'); ?>
                                            </td>
                                            <td>
                                                <?php echo (new DateTime($history->lastLegFlownDate))->format('d M Y'); ?>
                                            </td>
                                            <td><?php echo isset($history->dateComplete) ? (new DateTime($history->dateComplete))->format('d M Y') : ""; ?>
                                            </td>
                                            <?php if ($activity->type == "Tour") { ?>
                                            <td>
                                                <?php echo $history->daysToComplete > 0 ? $history->daysToComplete : ""; ?>
                                            </td>
                                            <?php
                                                    } ?>
                                        </tr>
                                        <?php
                                            } ?>
                                    </tbody>
                                </table>
                                <?php
                                } else { ?>
                                Currently no activity history.
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (count($activity->activityLegs) > 0) { ?>
        <?php include 'site_widgets/map_activity.php'; ?>
        <div id="map" style="height:600px;"></div>
        <?php } ?>
    </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script type="text/javascript">
var descJson =
    '<?php echo $activity->description != null ? addslashes(preg_replace("/\r|\n/", "", $activity->description)) : ""; ?>';
$(document).ready(function() {
    try {
        var parser = new edjsParser({
            embed: {
                useProvidedLength: false,
            }
        });
        var html = parser.parse(JSON.parse(descJson));
        $(".activity-content").html(html)
        console.log(html);
    } catch (e) {
        $(".activity-content").html(descJson);
    }
});
</script>