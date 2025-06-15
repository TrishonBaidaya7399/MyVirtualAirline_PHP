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
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
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
                        <p> <i class="fa fa-warning" aria-hidden="true"></i> Please check all fields have been completed
                            correctly.</p>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Terms & Conditions</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>By completing this manual PIREP you agree to the following:</p>
                            <ul>
                                <li>You have completed the flight within the last 24 hours</li>
                                <li>All form fields have been completed accurately</li>
                                <li>Block time is the total time spent in the aircraft from departure to arrival and is
                                    the time you will be credited for the flight.</li>
                            </ul>
                            <p>Your PIREP will be reviewed by staff and can be rejected for providing inaccurate
                                information about your flight.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Flight Report Form</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?php if ($status == "success") { ?>
                            <div class="col-md-12">
                                <div class="alert alert-success">Your pilot report has been successfully submitted.
                                </div>
                            </div>
                        <?php } else { ?>
                            <form method="post" id="pirepform" class="form">
                                <?php if (!empty($bid)) { ?>
                                    <div class="col-md-12">
                                        <p><i class="fa fa-info" aria-hidden="true"></i> This flight has been pre-populated by
                                            the dispatch center.</p>
                                    </div>
                                <?php } ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Flight Number*:</label>
                                        <input name="flight_number" type="text" id="flight_number" class="form-control"
                                            style="text-transform:uppercase" required value="<?php echo $flightNumber; ?>"
                                            <?php echo !empty($bid) && !empty($bid->flightNumber) ? "disabled" : "" ?> />
                                    </div>
                                    <div class="form-group">
                                        <label>Flight Date*:</label>
                                        <input name="date" type="date" id="date" value="<?php echo $date ?>" maxlength="10"
                                            class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label>Departure ICAO*:</label>
                                        <input name="depicao" type="text" id="depicao" maxlength="4" class="form-control"
                                            style="text-transform:uppercase" required value="<?php echo $departureIcao; ?>"
                                            <?php echo !empty($bid) && !empty($bid->departureIcao) ? "disabled" : "" ?> />
                                    </div>
                                    <div class="form-group">
                                        <label>Arrival ICAO*:</label>
                                        <input name="arricao" type="text" id="arricao" maxlength="4" class="form-control"
                                            style="text-transform:uppercase" required value="<?php echo $arrivalIcao; ?>"
                                            <?php echo !empty($bid) && !empty($bid->arrivalIcao) ? "disabled" : "" ?> />
                                    </div>
                                    <div class="form-group">
                                        <label>Route*:</label>
                                        <textarea name="route" cols="50" rows="5" id="route" class="form-control"
                                            style="text-transform:uppercase" required><?php echo $route; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Aircraft*:</label>
                                        <input name="aircraft" type="text" id="aircraft" maxlength="25" class="form-control"
                                            style="text-transform:uppercase" required value="<?php echo $aircraft; ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>Distance*:</label>
                                        <input name="miles" type="number" pattern="/^-?\d+\.?\d*$/"
                                            onKeyPress="if(this.value.length==4) return false;" id="miles"
                                            class="form-control" required value="<?php echo $miles; ?>" />
                                        <p class="help-block">Distance is measured in nautical miles</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Fuel Used*: (<?php echo fuel_weight_display ? "lb" : "kg" ?>)</label>
                                        <input name="fuel" type="number" pattern="/^-?\d+\.?\d*$/"
                                            onKeyPress="if(this.value.length==6) return false;" id="fuel"
                                            class="form-control" required value="<?php echo $fuel; ?>" />
                                        <p class="help-block">Fuel is measured in
                                            <?php echo fuel_weight_display ? "pounds" : "kilograms" ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Passengers*:</label>
                                        <input name="pax" type="number" pattern="/^-?\d+\.?\d*$/"
                                            onKeyPress="if(this.value.length==3) return false;" id="pax"
                                            class="form-control" required value="<?php echo $pax; ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>Cargo*: (<?php echo fuel_weight_display ? "lb" : "kg" ?>)</label>
                                        <input name="cargo" type="number" pattern="/^-?\d+\.?\d*$/"
                                            onKeyPress="if(this.value.length==6) return false;" id="cargo"
                                            class="form-control" required value="<?php echo $cargo; ?>" />
                                        <p class="help-block">Cargo is measured in
                                            <?php echo cargo_weight_display ? "pounds" : "kilograms" ?>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Departure Time*:</label>
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
                                        <p class="help-block">Time is UTC</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Arrival Time*:</label>
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
                                        <p class="help-block">Time is UTC</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Block Time*:</label>
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
                                    <div class="form-group">
                                        <label>Comments:</label>
                                        <textarea name="comments" rows="5" id="comments"
                                            class="form-control"><?php echo $comments; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <input name="btnpirep" type="submit" id="btnpirep" value="Submit Report"
                                                class="btn btn-success">
                                        </div>
                                    </div>
                                </div>
                            </form><?php } ?>
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
                    <span aria-hidden="true">&times;</span>
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