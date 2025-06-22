<?php

use Proxy\Api\Api;

include '../lib/functions.php';
include '../config.php';

Api::__constructStatic();
session_start();
validateSession();

$status = "";
$responseMessage = null;
$bid = null;
$departureIcao = "";
$arrivalIcao = "";
$route = "";
$aircraft = "";
$fuel = "";
$miles = "";
$pax = "";
$cargo = "";
$flightNumber = "";
$date = "";
$comments = "";
$res = Api::sendAsync('GET', 'v1/bid/' . $_SESSION['pilotid'], null);
if ($res->getStatusCode() == 200) {
    $bid = json_decode($res->getBody());
    if (isset($bid)) {
        $departureIcao = $bid->departureIcao;
        $arrivalIcao = $bid->arrivalIcao;
        $route = $bid->route;
        $aircraft = $bid->aircraft;
        $pax = $bid->totalPax;
        $cargo = $bid->cargo;
        $flightNumber = $bid->flightNumber;
    }
}
$limitDepatureLocation = false;
$res = Api::sendSync('GET', 'v1/airline', null);
if ($res->getStatusCode() == 200) {
    $settings = json_decode($res->getBody(), false);
    $limitDepatureLocation = $settings->limitDepartureLocation;
}
$currentLocation = "";
if ($limitDepatureLocation) {
    $res = Api::sendAsync('GET', 'v1/pilot/location', null);
    if ($res->getStatusCode() == 200) {
        $response = json_decode($res->getBody());
        $currentLocation = $response->location;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = cleanString($_POST['date']);
    $departureIcao = empty($bid->departureIcao) ? cleanString($_POST['depicao']) : $bid->departureIcao;
    $arrivalIcao = empty($bid->arrivalIcao) ? cleanString($_POST['arricao']) : $bid->arrivalIcao;
    $route = cleanString($_POST['route']);
    $aircraft = cleanString($_POST['aircraft']);
    $fuel = cleanString(preg_replace("/[^0-9,.]/", "", $_POST['fuel']));
    if (!fuel_weight_display) {
        $fuellb = ($fuel * 2.205);
    } else {
        $fuellb = $fuel;
    }
    $cargo = cleanString(preg_replace("/[^0-9,.]/", "", $_POST['cargo']));
    if (!cargo_weight_display) {
        $cargokg = $cargo;
    } else {
        $cargokg = ($cargo / 2.205);
    }
    $miles = cleanString(preg_replace("/[^0-9,.]/", "", $_POST['miles']));
    $pax = cleanString(preg_replace("/[^0-9,.]/", "", $_POST['pax']));
    $dh = cleanString($_POST['dh']);
    $dm = cleanString($_POST['dm']);
    $ah = cleanString($_POST['ah']);
    $am = cleanString($_POST['am']);
    $bh = cleanString($_POST['bh']);
    $bm = cleanString($_POST['bm']);
    $comments = cleanString($_POST['comments']);
    $flightNumber = empty($bid->flightNumber) ? cleanString($_POST['flight_number']) : $bid->flightNumber;

    if (((((empty($date)) || (empty($departureIcao)) || (strlen($departureIcao) > 4) || (empty($arrivalIcao)) || (empty($pax)) || (strlen($arrivalIcao) > 4) || (empty($route)) || (empty($flightNumber)) || (empty($aircraft) || (empty($miles))))))) {
        $status = "required_fields";
    }
    if (empty($status)) {
        $data = [
            'PilotId' => $_SESSION['pilotid'],
            'FlightNumber' => $flightNumber,
            'Date' => $date,
            'ArrivalIcao' => $arrivalIcao,
            'DepartureIcao' => $departureIcao,
            'ArrivalIcao' => $arrivalIcao,
            'DepartureTime' => $dh . ':' . $dm . ':00',
            'ArrivalTime' => $ah . ':' . $am . ':00',
            'Duration' => $bh . ':' . $bm . ':00',
            'Passengers' => $pax,
            'Distance' => $miles,
            'FuelBurnt' => intval($fuellb),
            'Cargo' => $cargokg,
            'BidId' => !empty($bid) ? $bid->id : null,
            'Callsign' => $_SESSION['callsign'],
            'Comments' => $comments,
            'Route' => $route,
            'Aircraft' => $aircraft,
        ];

        $res = Api::sendSync('POST', 'v1/operations/pirep', $data);
        switch ($res->getStatusCode()) {
            case 200:
                if (enable_discord_pirep_alerts) {
                    $data = [
                        'Message' => '**' . $_SESSION['callsign'] . '** just completed a flight to **' . strtoupper($arrivalIcao) . '**\r\nDeparted: **' . strtoupper($departureIcao) . '**\r\nDistance: **' . $miles . 'nm**\r\nAircraft: **' . strtoupper($aircraft) . '**\r\nFlight Number: **' . strtoupper($flightNumber) . '**',
                    ];
                    $res = Api::sendSync('POST', 'v1/integrations/discord', $data);
                }
                $status = "success";
                $bid = null;
                $departureIcao = "";
                $arrivalIcao = "";
                $route = "";
                $aircraft = "";
                $fuel = "";
                $miles = "";
                $pax = "";
                $cargo = "";
                $flightNumber = "";
                $date = "";
                $comments = "";
                break;
            case 400:
                $status = "error";
                $responseMessage = $res->getBody();
                break;
            default:
                $responseMessage = $res->getBody();
                $status = "error";
                break;
        }
    }
}
?>
<?php
$MetaPageTitle = "Flight Report Form";
$MetaPageDescription = "Submit your flight report with our virtual airline. Fill out the form accurately to log your flight.";
$MetaPageKeywords = "flight report, pirep, virtual airline, aviation, flight simulation";
?>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.pirep {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('../assets/images/backgrounds/air_stats_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    overflow-y: auto; /* Enable vertical scrolling */
}

.pirep::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* 50% black overlay */
    z-index: 1;
}

.pirep-header {
    padding-top: 80px; /* Ensure padding is sufficient for header */
    min-height: 100px; /* Minimum height to ensure header space */
}

.pirep .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}


/* Global Heading */
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

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
    }
}

/* Glass Card */
.glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 20px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Panel Styles */
.panel-heading {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 15px;
    border-bottom: 1px solid rgba(255, 215, 0, 1);
}

.panel-title {
    color: #fff;
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.panel-body {
    padding: 20px;
    color: #fff;
}

/* Form Layout */
.form-layout {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    padding-inline: 20px;
}

.form-layout-textareas {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
    padding-inline: 20px;
}

.form-submit-button {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
    padding-top: 20px;
    width: 100%;
    border-top: 2px solid rgba(255, 215, 0, 1);
}

.form-submit-button input {
    min-width: 50%;
}

/* Form Styles */
.form-group {
    margin-bottom: 0 !important;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.form-group label {
    width: 150px;
    color: #fff;
    font-weight: 600;
    margin-right: 10px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.form-group label span {
    color: rgba(255, 0, 0, 0.9); /* 90% red opacity for asterisk */
}

.form-group input,
.form-group select,
.form-group textarea {
    flex-grow: 1;
    background: #fff;
    color: #000;
    border: 1px solid rgba(255, 215, 0, 0.5);
    border-radius: 5px;
    padding: 8px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: rgba(255, 215, 0, 1);
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group .btn-success {
    background: rgba(21, 182, 6, 0.8);
    border: none;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.form-group .btn-success:hover {
    background: rgba(21, 182, 6, 1);
}

/* Alert Styles */
.alert {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
    width: 100%;
}

.alert-success {
    background: rgba(21, 182, 6, 0.2);
    border-color: rgba(21, 182, 6, 0.5);
}

.alert-danger {
    background: rgba(255, 0, 0, 0.2);
    border-color: rgba(255, 0, 0, 0.5);
}

/* Responsive Design */
@media (max-width: 1199px) {
    .pirep {
        padding: 80px 0;
    }
    .pirep-header {
        padding-top: 80px;
    }
    .form-group label {
        width: 120px;
        font-size: 13px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 13px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 15px;
    }
}

@media (max-width: 991px) {
    .pirep {
        background-attachment: scroll;
    }
    .form-layout {
        grid-template-columns: 1fr;
    }
    .form-layout-textareas {
        grid-template-columns: 1fr;
    }
    .form-group {
        flex-direction: column;
        align-items: stretch;
    }
    .form-group label {
        width: auto;
        margin-right: 0;
        margin-bottom: 5px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        font-size: 12px;
    }
}

@media (max-width: 767px) {
    .pirep {
        padding: 80px 0;
    }
    .pirep-header {
        padding-top: 80px;
    }
    .form-submit-button input {
        min-width: 100%;
    }
    .form-group label {
        font-size: 12px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 12px;
        padding: 6px;
    }
    .panel-body {
        padding: 10px;
    }
    .alert {
        padding: 10px;
        font-size: 12px;
    }
}

@media (max-width: 575px) {
    .pirep {
        padding: 80px 0;
    }
    .pirep-header {
        padding-top: 80px;
    }
    .form-group label {
        font-size: 11px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 11px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 8px;
    }
}

/* Print Styles */
@media print {
    .pirep {
        background: white;
        padding: 80px 0;
    }
    .pirep::before {
        display: none;
    }
    .glass-card, .panel-heading, .alert {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .panel-title, .panel-body, .form-group label, .form-group input, .form-group select, .form-group textarea, .alert {
        color: black;
        text-shadow: none;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        background: white;
        border: 1px solid #000;
    }
}
</style>

<?php include '../includes/header.php'; ?>
<section id="content" class="pirep pirep-header">
    <div class="container">
        <div class="row" style="width: 100%">
            <div class="col-12 mb-4" style="">
                <div class="global-heading">
                    <h3 class="global-title">Terms & Conditions</h3>
                </div>
                <div class="glass-card">
                    <div class="panel-body">
                        <p>By completing this manual PIREP you agree to the following:</p>
                        <ul>
                            <li>You have completed the flight within the last 24 hours</li>
                            <li>All form fields have been completed accurately</li>
                            <li>Block time is the total time spent in the aircraft from departure to arrival and is the time you will be credited for the flight.</li>
                        </ul>
                        <p>Your PIREP will be reviewed by staff and can be rejected for providing inaccurate information about your flight.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($status)) { ?>
            <div class="row">
                <?php if ($status == "error") { ?>
                    <div class="alert alert-danger" role="alert">
                        <p>
                            <?php if ($status == 'error') {
                                if (!empty($responseMessage)) {
                                    echo $responseMessage;
                                } else {
                                    echo '<i class="fa fa-warning" aria-hidden="true"></i> An error occurred when filling the manual PIREP.';
                                }
                            } ?>
                        </p>
                    </div>
                <?php } ?>
                <?php if ($status == "required_fields") { ?>
                    <div class="alert alert-danger" role="alert">
                        <p><i class="fa fa-warning" aria-hidden="true"></i> Please check all fields have been completed correctly.</p>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="row" style="width: 100% ">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title">Flight Report Form</h3>
                </div>
                <div class="glass-card">
                    <div class="panel-body">
                        <div class="row">
                            <?php if ($status == "success") { ?>
                                <div class="col-12">
                                    <div class="alert alert-success">Your pilot report has been successfully submitted.</div>
                                </div>
                            <?php } else { ?>
                                <form method="post" id="pirepform" class="form">
                                    <?php if (!empty($bid)) { ?>
                                        <div class="col-12">
                                            <p><i class="fa fa-info" aria-hidden="true"></i> This flight has been pre-populated by the dispatch center.</p>
                                        </div>
                                    <?php } ?>
                                    <div class="form-layout">
                                        <div class="form-group">
                                            <label>Flight Number<span>*</span>:</label>
                                            <input name="flight_number" type="text" id="flight_number" class="form-control" style="text-transform:uppercase" required value="<?php echo $flightNumber; ?>" <?php echo !empty($bid) && !empty($bid->flightNumber) ? "disabled" : "" ?> />
                                        </div>
                                        <div class="form-group">
                                            <label>Flight Date<span>*</span>:</label>
                                            <input name="date" type="date" id="date" value="<?php echo $date ?>" maxlength="10" class="form-control" required />
                                        </div>
                                        <div class="form-group">
                                            <label>Departure ICAO<span>*</span>:</label>
                                            <input name="depicao" type="text" id="depicao" maxlength="4" class="form-control" style="text-transform:uppercase" required value="<?php echo $departureIcao; ?>" <?php echo !empty($bid) && !empty($bid->departureIcao) ? "disabled" : "" ?> />
                                        </div>
                                        <div class="form-group">
                                            <label>Arrival ICAO<span>*</span>:</label>
                                            <input name="arricao" type="text" id="arricao" maxlength="4" class="form-control" style="text-transform:uppercase" required value="<?php echo $arrivalIcao; ?>" <?php echo !empty($bid) && !empty($bid->arrivalIcao) ? "disabled" : "" ?> />
                                        </div>
                                        <div class="form-group">
                                            <label>Aircraft<span>*</span>:</label>
                                            <input name="aircraft" type="text" id="aircraft" maxlength="25" class="form-control" style="text-transform:uppercase" required value="<?php echo $aircraft; ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Distance<span>*</span>:</label>
                                            <input name="miles" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==4) return false;" id="miles" class="form-control" required value="<?php echo $miles; ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Fuel Used<span>*</span>: (<?php echo fuel_weight_display ? "lb" : "kg" ?>)</label>
                                            <input name="fuel" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="fuel" class="form-control" required value="<?php echo $fuel; ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Passengers<span>*</span>:</label>
                                            <input name="pax" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;" id="pax" class="form-control" required value="<?php echo $pax; ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Cargo<span>*</span>: (<?php echo fuel_weight_display ? "lb" : "kg" ?>)</label>
                                            <input name="cargo" type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="cargo" class="form-control" required value="<?php echo $cargo; ?>" />
                                        </div>
                                        <div class="form-group">
                                            <label>Departure Time<span>*</span>:</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    Hours
                                                    <select name="dh" id="dh" class="form-control">
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12" selected>12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="00">00</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    Minutes
                                                    <select name="dm" id="dm" class="form-control">
                                                        <option value="00" selected>00</option>
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="24">24</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                        <option value="32">32</option>
                                                        <option value="33">33</option>
                                                        <option value="34">34</option>
                                                        <option value="35">35</option>
                                                        <option value="36">36</option>
                                                        <option value="37">37</option>
                                                        <option value="38">38</option>
                                                        <option value="39">39</option>
                                                        <option value="40">40</option>
                                                        <option value="41">41</option>
                                                        <option value="42">42</option>
                                                        <option value="43">43</option>
                                                        <option value="44">44</option>
                                                        <option value="45">45</option>
                                                        <option value="46">46</option>
                                                        <option value="47">47</option>
                                                        <option value="48">48</option>
                                                        <option value="49">49</option>
                                                        <option value="50">50</option>
                                                        <option value="51">51</option>
                                                        <option value="52">52</option>
                                                        <option value="53">53</option>
                                                        <option value="54">54</option>
                                                        <option value="55">55</option>
                                                        <option value="56">56</option>
                                                        <option value="57">57</option>
                                                        <option value="58">58</option>
                                                        <option value="59">59</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Arrival Time<span>*</span>(UTC):</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    Hours
                                                    <select name="ah" id="ah" class="form-control">
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12" selected>12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="00">00</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    Minutes
                                                    <select name="am" id="am" class="form-control">
                                                        <option value="00" selected>00</option>
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="24">24</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                        <option value="32">32</option>
                                                        <option value="33">33</option>
                                                        <option value="34">34</option>
                                                        <option value="35">35</option>
                                                        <option value="36">36</option>
                                                        <option value="37">37</option>
                                                        <option value="38">38</option>
                                                        <option value="39">39</option>
                                                        <option value="40">40</option>
                                                        <option value="41">41</option>
                                                        <option value="42">42</option>
                                                        <option value="43">43</option>
                                                        <option value="44">44</option>
                                                        <option value="45">45</option>
                                                        <option value="46">46</option>
                                                        <option value="47">47</option>
                                                        <option value="48">48</option>
                                                        <option value="49">49</option>
                                                        <option value="50">50</option>
                                                        <option value="51">51</option>
                                                        <option value="52">52</option>
                                                        <option value="53">53</option>
                                                        <option value="54">54</option>
                                                        <option value="55">55</option>
                                                        <option value="56">56</option>
                                                        <option value="57">57</option>
                                                        <option value="58">58</option>
                                                        <option value="59">59</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Block Time<span>*</span>:</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    Hours
                                                    <select name="bh" id="bh" class="form-control">
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="00">00</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    Minutes
                                                    <select name="bm" id="bm" class="form-control">
                                                        <option value="00" selected>00</option>
                                                        <option value="01">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                        <option value="16">16</option>
                                                        <option value="17">17</option>
                                                        <option value="18">18</option>
                                                        <option value="19">19</option>
                                                        <option value="20">20</option>
                                                        <option value="21">21</option>
                                                        <option value="22">22</option>
                                                        <option value="23">23</option>
                                                        <option value="24">24</option>
                                                        <option value="25">25</option>
                                                        <option value="26">26</option>
                                                        <option value="27">27</option>
                                                        <option value="28">28</option>
                                                        <option value="29">29</option>
                                                        <option value="30">30</option>
                                                        <option value="31">31</option>
                                                        <option value="32">32</option>
                                                        <option value="33">33</option>
                                                        <option value="34">34</option>
                                                        <option value="35">35</option>
                                                        <option value="36">36</option>
                                                        <option value="37">37</option>
                                                        <option value="38">38</option>
                                                        <option value="39">39</option>
                                                        <option value="40">40</option>
                                                        <option value="41">41</option>
                                                        <option value="42">42</option>
                                                        <option value="43">43</option>
                                                        <option value="44">44</option>
                                                        <option value="45">45</option>
                                                        <option value="46">46</option>
                                                        <option value="47">47</option>
                                                        <option value="48">48</option>
                                                        <option value="49">49</option>
                                                        <option value="50">50</option>
                                                        <option value="51">51</option>
                                                        <option value="52">52</option>
                                                        <option value="53">53</option>
                                                        <option value="54">54</option>
                                                        <option value="55">55</option>
                                                        <option value="56">56</option>
                                                        <option value="57">57</option>
                                                        <option value="58">58</option>
                                                        <option value="59">59</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-layout-textareas">
                                        <div class="form-group">
                                            <label>Route<span>*</span>:</label>
                                            <textarea name="route" cols="50" rows="5" id="route" class="form-control" style="text-transform:uppercase" required><?php echo $route; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Comments:</label>
                                            <textarea name="comments" rows="5" id="comments" class="form-control"><?php echo $comments; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-submit-button">
                                        <input name="btnpirep" type="submit" id="btnpirep" value="Submit Report" class="btn btn-success" />
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title"><strong>PIREP Error<strong></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCloseCancel" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        var limitDepartureLocation = <?php echo $limitDepatureLocation ? 'true' : 'false'; ?>;
        var currentLocation = '<?php echo $currentLocation ?? ''; ?>';
        $("#btnpirep").on('click', function(event) {
            if ($("#pirepform")[0].checkValidity()) {
                event.preventDefault();
                Loader.start();

                if (limitDepartureLocation && currentLocation != '') {
                    if ($("#depicao").val().toUpperCase() != currentLocation) {
                        Loader.stop();
                        $("#errorModal").modal("show");
                        $(".modal-body").html(
                            "<p>Your flight must be filled from your current location (" +
                            currentLocation + ").</p>");
                        return;
                    }
                }
                $("#pirepform").submit();
            }
        });
    });
</script>