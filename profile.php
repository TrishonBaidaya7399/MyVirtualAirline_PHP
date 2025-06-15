<?php

use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
include 'lib/functions.php';
include 'config.php';

Api::__constructStatic();
session_start();

$id = cleanString($_GET['id']);

$pilot = null;
$res = Api::sendAsync('GET', 'v1/pilot/' . $id, null);
if ($res->getStatusCode() == 200) {
    $pilot = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url);
    die();
}

$stats = null;
$res = Api::sendAsync('GET', 'v1/pilot/stats/' . $id, null);
if ($res->getStatusCode() == 200) {
    $stats = json_decode($res->getBody());
}
$logbook = null;
$res = Api::sendAsync('GET', 'v1/pilot/logbook20/' . $id, null);
if ($res->getStatusCode() == 200) {
    $logbook = json_decode($res->getBody(), false);
}
$awards = null;
$res = Api::sendAsync('GET', 'v1/award/assigned10/pilot/' . $id, null);
if ($res->getStatusCode() == 200) {
    $awards = json_decode($res->getBody(), false);
}
$bestLanding = null;
$res = Api::sendAsync('GET', 'v1/operations/pirep/pilot/best-landing/' . $id, null);
if ($res->getStatusCode() == 200) {
    $bestLanding = json_decode($res->getBody(), false);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row row-space">
            <div class="jumbotron">
                <div class="row">
                    <div class="col-md-4 text-right">
                        <div class="row">
                            <?php if ($pilot->profileImage != "") { ?>
                                <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $pilot->profileImage ?>"
                                    width="200" class="img-circle pilot-profile-image" />
                            <?php } else { ?>
                                <i class="fa fa-user-circle profile" aria-hidden="true"></i>
                            <?php } ?>
                        </div>
                        <?php if (isset($_SESSION['pilotid'])) { ?>
                            <div class="row profile-container">
                                <?php if ($pilot->facebookLink != "") { ?><a href="<?php echo $pilot->facebookLink ?>"
                                        target="_blank" rel="nofollow"><i class="fa fa-facebook-square"
                                            aria-hidden="true"></i></a><?php } ?>
                                <?php if ($pilot->youtubeLink != "") { ?><a href="<?php echo $pilot->youtubeLink ?>"
                                        target="_blank" rel="nofollow"><i class="fa fa-brands fa-youtube"
                                            aria-hidden="true"></i></a><?php } ?>
                                <?php if ($pilot->twitterLink != "") { ?><a href="<?php echo $pilot->twitterLink ?>"
                                        target="_blank" rel="nofollow"><i class="fa fa-twitter-square"
                                            aria-hidden="true"></i></a><?php } ?>
                                <!-- This is actually used for Discord now -->
                                <?php if ($pilot->skypeLink != "") { ?><i class="fa fa-brands fa-discord"
                                        aria-hidden="true"></i><?php } ?><?php echo $pilot->skypeLink ?>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <?php if ($pilot->vatsimId != "") { ?>
                                <a href="https://map.vatsim.net/?user=<?php echo $pilot->vatsimId; ?>" target="_blank"><img
                                        src="<?php echo website_base_url; ?>images/vatsim.gif" target="_blank"
                                        alt="Vatsim Account ID <?php echo $pilot->vatsimId; ?>" /></a>
                            <?php } ?>
                            <?php if ($pilot->ivaoId != "") { ?>
                                <a href="https://www.ivao.aero/Login.aspx?r=Member.aspx?Id=<?php echo $pilot->ivaoId; ?>"
                                    target="_blank"><img src="<?php echo website_base_url; ?>images/ivao.png"
                                        target="_blank" alt="IVAO Account ID <?php echo $pilot->ivaoId; ?>" /></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-7">
                        <h1><?php echo explode(" ", $pilot->name)[0]; ?>
                        </h1>
                        <p>Pilot ID: <strong><?php echo $pilot->callsign; ?></strong>
                            | <?php if (!empty($pilot->rank->imageUrl)) { ?><img
                                    src="<?php echo website_base_url; ?>uploads/ranks/<?php echo $pilot->rank->imageUrl; ?>"
                                    width="80" /> <strong><?php echo $pilot->rank->name; ?></strong><?php } else { ?>No
                                Rank<?php } ?>
                        </p>
                        <p>
                            Base: <strong><?php echo $pilot->hub; ?></strong>
                        </p>
                        <p>
                            <img src="<?php echo website_base_url; ?>images/flags/<?php echo $pilot->location; ?>.gif"
                                alt="<?php echo $pilot->location; ?>" width="20" height="20">
                            <?php echo $pilot->location; ?>
                        </p>
                        <p>Wallet: <strong>$<?php echo number_format($pilot->wallet, 2); ?></strong> | XP:
                            <strong><?php echo number_format($pilot->xp); ?></strong>
                        </p>
                        <p>
                            <strong><i class="fa fa-trophy gold" aria-hidden="true"></i></strong> Best Landing:

                            <?php if (!empty($bestLanding)) {
                                echo '<strong>' . $bestLanding->landingRate . 'fpm</strong> on ' . (new DateTime($bestLanding->date))->format('d M Y') . ' at <i class="fa fa-map-marker" aria-hidden="true"></i> <a href="' . website_base_url . 'pirep_info.php?id=' . $bestLanding->id . '">' . $bestLanding->arrivalIcao . '</a>';
                            } else {
                                echo '<strong>NA</strong>';
                            } ?>
                        </p>
                        <p><i><?php echo $pilot->background == "" ? "" : $pilot->background; ?></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($awards != null) { ?>
            <div class="row">
                <div class="col-md-12">
                    <h4>Awards <i class="fa fa-angle-double-right" aria-hidden="true"></i> <a
                            href="<?php echo website_base_url; ?>pilot_awards.php?id=<?php echo $pilot->id; ?>">view
                            more</a></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php foreach ($awards as $key => $award) { ?>
                        <img src="<?php echo website_base_url; ?>uploads/awards/<?php echo $award->imageUrl; ?>"
                            style="margin-right:5px;" width="60"
                            title="<?php echo $award->awardName; ?> awarded on <?php echo (new DateTime($award->dateAwarded))->format('d M Y'); ?>" />
                    <?php }; ?>
                </div>
            </div>
            <hr />
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default row-space">
                    <div class="panel-heading">
                        <h3 class="panel-title">Pilot Statistics</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <h4>30 Day Statistics</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Hours</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo $stats->monthHours == null ? "No Flights" : displayCleanHours($stats->monthHours); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Flights</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo number_format($stats->monthFlights); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Miles</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo number_format($stats->monthMiles); ?>nm
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Fuel Used</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo getFuelDisplayValue($stats->monthFuel); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Passengers</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo number_format($stats->monthPax); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Cargo</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo getCargoDisplayValue($stats->monthCargo, 2); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <h4>All-time Statistics</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Hours</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo $pilot->totalHours == null ? "No Flights" : displayCleanHours($pilot->totalHours); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Flights</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo number_format($stats->totalFlights); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Miles</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo number_format($stats->totalMiles); ?>nm
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Fuel Used</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo getFuelDisplayValue($stats->totalFuel); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Passengers</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo number_format($stats->totalPax); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Cargo</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo getCargoDisplayValue($stats->totalCargo, 2); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <h4>Average Performance</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Average Landing Rate</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <?php echo $stats->averageLandingRate == 0 ? "No data" : number_format($stats->averageLandingRate) . "fpm"; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <strong>Average Flight Performance Score</strong>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <br /><?php echo number_format($stats->averageFlightRating, 2); ?>%
                                </div>
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
                        <h3 class="panel-title">Latest Flights</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><strong>Flight Number</strong></th>
                                    <th><strong>Date</strong></th>
                                    <th><strong>From</strong></th>
                                    <th><strong>To</strong></th>
                                    <th><strong>Duration</strong></th>
                                    <th><strong>Aircraft</strong></th>
                                    <th class="text-center"><strong>ACARS</strong></th>
                                    <th class="text-center"><strong>Landing Rate</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logbook != null) { ?>
                                    <?php foreach ($logbook as $key => $flight) { ?>
                                        <tr>
                                            <td><i class="fa fa-plane" aria-hidden="true"></i> <a
                                                    href="<?php echo website_base_url; ?>pirep_info.php?id=<?php echo $flight->id; ?>"
                                                    class="js_showloader"><?php echo $flight->flightNumber; ?></a>
                                            </td>
                                            <td><?php echo (new DateTime($flight->date))->format('d M Y'); ?>
                                            </td>
                                            <td><i class="fa fa-map-marker"></i> <a
                                                    href="airport_info.php?airport=<?php echo $flight->depIcao; ?>"
                                                    class="js_showloader"><?php echo $flight->depIcao; ?></a>
                                            </td>
                                            <td><i class="fa fa-map-marker"></i> <a
                                                    href="airport_info.php?airport=<?php echo $flight->arrIcao; ?>"
                                                    class="js_showloader"><?php echo $flight->arrIcao; ?></a>
                                            </td>
                                            <td><?php echo $flight->duration; ?>
                                            </td>
                                            <td><span
                                                    title="<?php echo $flight->aircraft; ?>"><?php echo limit($flight->aircraft, 15); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php echo empty($flight->acarsFlight) ? "<i class=\"fa fa-times\" title=\"Not Acars recorded flight\"></i>" : "<i class=\"fa fa-check\" title=\"Acars recorded flight\"></i>"; ?>
                                            </td>
                                            <td><?php echo $flight->landingRate < 0 ? number_format($flight->landingRate) . "fpm" : "N/A"; ?>
                                            </td>
                                        </tr>
                                    <?php }; ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8">This pilot hasn't made any flights yet.</td>
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