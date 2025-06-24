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
    background-image: url('./assets/images/backgrounds/world_map2.jpg');
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

.offset-header {
    padding-top: 100px;
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
    text-align: start;
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
    color: rgba(0, 0, 0, 0.9);
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

#latest_flights_wrapper .row .col-sm-6 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#latest_flights_wrapper .row .col-sm-5 {
    padding-left: 0 !important;
    padding-right: 0 !important;
    background: transparent !important;
    color: rgba(0, 0, 0, 0.9);
}

#latest_flights_wrapper .row .col-sm-7 {
    background: transparent !important;
    padding: 0 !important;
}

#latest_flights_wrapper .row .col-sm-12 {
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
    .offset-header {
        padding-top: 80px;
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
    .offset-header {
        padding-top: 60px;
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
    .offset-header {
        padding-top: 50px;
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
    .offset-header {
        padding-top: 40px;
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

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
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
</style>

<section id="content" class="section route-map-section">
    <div class="container">
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="route-map-glass-card">
                    <div class="route-map-header">
                        <div class="route-map-title-wrapper">
                            <h3 class="route-map-title">Pilot Profile</h3>
                            <i class="fa fa-user route-map-icon" aria-hidden="true"></i>
                        </div>
                        <hr />
                        <div class="row" style="width: 100%">
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
                                <div class="row" style="width: 100%">
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
                            <div class="col-md-1"></div>
                            <div class="col-md-7">
                                <h1 style="font-weight: 600"><?php echo explode(" ", $pilot->name)[0]; ?></h1>
                                <p style="color: #fff">Pilot ID: <strong><?php echo $pilot->callsign; ?></strong>
                                    | <?php if (!empty($pilot->rank->imageUrl)) { ?><img
                                        src="<?php echo website_base_url; ?>uploads/ranks/<?php echo $pilot->rank->imageUrl; ?>"
                                        width="80" /> <strong><?php echo $pilot->rank->name; ?></strong><?php } else { ?>No
                                    Rank<?php } ?>
                                </p>
                                <p style="color: #fff">
                                    Base: <strong><?php echo $pilot->hub; ?></strong>
                                </p>
                                <p style="color: #fff">
                                    <img src="<?php echo website_base_url; ?>images/flags/<?php echo $pilot->location; ?>.gif"
                                        alt="<?php echo $pilot->location; ?>" width="20" height="20">
                                    <?php echo $pilot->location; ?>
                                </p>
                                <p style="color: #fff">Wallet: <strong>$<?php echo number_format($pilot->wallet, 2); ?></strong> | XP:
                                    <strong><?php echo number_format($pilot->xp); ?></strong>
                                </p>
                                <p style="color: #fff">
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
            </div>
        </div>
        <?php if ($awards != null) { ?>
            <div class="row" style="width: 100%">
                <div class="col-12">
                    <div class="global-heading">
                        <h3 class="global-title"><i class="fa fa-trophy"></i> Awards <i class="fa fa-angle-double-right"
                                aria-hidden="true"></i> <a
                                href="<?php echo website_base_url; ?>pilot_awards.php?id=<?php echo $pilot->id; ?>">view
                                more</a></h3>
                    </div>
                    <div class="route-map-glass-card">
                        <div class="row" style="width: 100%">
                            <div class="col-md-12">
                                <?php foreach ($awards as $key => $award) { ?>
                                    <img src="<?php echo website_base_url; ?>uploads/awards/<?php echo $award->imageUrl; ?>"
                                        style="margin-right:5px;" width="60"
                                        title="<?php echo $award->awardName; ?> awarded on <?php echo (new DateTime($award->dateAwarded))->format('d M Y'); ?>" />
                                <?php }; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
        <?php } ?>
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title"><i class="fa fa-bar-chart"></i> Pilot Statistics</h3>
                </div>
                <div class="route-map-glass-card">
                    <div class="row" style="padding: 15px" >
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <h4 style="color: #fff; font-weight: 600; border-bottom: 1px solid #fff; padding-bottom: 5px ">30 Day Statistics</h4>
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
                                    <h4 style="color: #fff; font-weight: 600; border-bottom: 1px solid #fff; padding-bottom: 5px  ">All-time Statistics</h4>
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
                                    <h4 style="color: #fff; font-weight: 600; border-bottom: 1px solid #fff; padding-bottom: 5px  ">Average Performance</h4>
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
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title"><i class="fa fa-plane" style="transform: rotate(-30deg)"></i> Latest Flights</h3>
                </div>
                <div class="flights-table-glass-card">
                    <div class="flights-table-wrapper">
                        <table class="table table-striped flights-table" id="latest_flights">
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