<?php
use Proxy\Api\Api;

include 'lib/functions.php';
include 'config.php';
session_start();
Api::__constructStatic();
$id = cleanString($_GET['id']);
$isFlightBooked = false;
$settings = null;
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
$hasPilotAlreadyBooked = false;
if (isset($_SESSION['pilotid'])) {
    if (!empty($_SESSION['pilotid'])) {
        $res = Api::sendAsync('GET', 'v1/pilot/hasactivebid/' . $_SESSION['pilotid'], null);
        if ($res->getStatusCode() == 200) {
            $response = json_decode($res->getBody());
            $hasPilotAlreadyBooked = $response;
        }
    }
}
$res = Api::sendAsync('GET', 'v1/operations/schedule/flight/' . $id, null);
if ($res->getStatusCode() == 200) {
    $flight = json_decode($res->getBody());
} else {
    echo '<script>alert("No flight exists under this id");</script>';
    echo '<script>history.back(1);</script>';
    exit;
    die();
}
$depicao = $flight->depIcao;
$arricao = $flight->arrIcao;
$mapData = $flight->airportInfo;
$path_data = null;
$status = "";
$responseMessage = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateSession();
    $ac = cleanString($_POST['selectedAircraft']);
    $reg = cleanString($_POST['selectedAircraftReg']);
    if (!$flight->hasActiveBid || pilots_can_book_same_flight) {
        if (!$hasPilotAlreadyBooked) {
            if (!empty($_SESSION['pilotid'])) {
                Api::__constructStatic();
                $data = [
                    'PilotId' => $_SESSION['pilotid'],
                    'DepartureIcao' => empty($flight->depIcao) ? "" : $flight->depIcao,
                    'ArrivalIcao' => empty($flight->arrIcao) ? "" : $flight->arrIcao,
                    'TotalPax' => 0,
                    'Route' => empty($flight->route) ? "" : $flight->route,
                    'FlightNumber' => empty($flight->flightNumber) ? "" : $flight->flightNumber,
                    'AircraftIcao' => empty($flight->aircraft) ? "" : $ac,
                    'AircraftReg' => empty($flight->aircraft) ? "" : $reg,
                    'Cargo' => 0,
                    'ScheduleId' => $id,
                ];

                $res = Api::sendSync('POST', 'v1/bid', $data);

                switch ($res->getStatusCode()) {
                    case 200:
                        $isFlightBooked = true;
                        $hasPilotAlreadyBooked = true;
                        if (enable_discord_dispatch_flight_alerts) {
                            $data = [
                                'Message' => '**' . $_SESSION['callsign'] . '** has dispatched a flight **' . (empty($flight->flightNumber) ? "" : $flight->flightNumber) . '** to **' . (empty($flight->arrIcao) ? "" : $flight->arrIcao) . '**.',
                            ];
                            $res = Api::sendSync('POST', 'v1/integrations/discord', $data);
                        }
                        break;
                    case 400:
                        $status = "error";
                        $responseMessage = $res->getBody();
                        break;
                    default:
                        break;
                }
            }

        } else {
            $hasPilotAlreadyBooked = true;
        }
    } else {
        echo '<script>alert("This flight has already been booked!");</script>';
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<style>
    /* Parallax Background */
    .parallax {
        position: relative;
        padding: 60px 0;
        min-height: calc(100vh - 128px);
        background-image: url('./assets/images/backgrounds/world_map2.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .parallax::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .parallax .container {
        position: relative;
        z-index: 2;
    }

    /* Mobile: Disable fixed background attachment */
    @media (max-width: 768px) {
        .parallax {
            background-attachment: scroll;
            padding: 60px 0;
            min-height: auto;
        }
    }

    /* Glass Backdrop for Cards */
    .glass-card {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 30px;
    }

    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .glass-card .panel-heading {
        border-radius: 10px 10px 0 0;
        background: rgba(255, 255, 255, 0.3);
        padding: 20px;
    }

    .glass-card .panel-heading .panel-title {
        color: #fff;
        font-size: 2rem;
        font-weight: 300;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .glass-card .panel-body {
        color: #fff;
        padding: 20px;
    }

    .glass-card .panel-body strong {
        color: rgba(255, 215, 0, 1);
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .glass-card .panel-body a {
        color: rgba(255, 215, 0, 1);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .glass-card .panel-body a:hover {
        color: #fff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    }

    /* Map Container */
    .map-container-glass {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 30px;
    }

    .map-container-glass:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    #map {
        height: 600px;
        border-radius: 15px;
        width: 100%;
        margin-bottom: 0 !important;
    }

    /* Alerts and Buttons */
    .alert {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        text-align: center;
        padding: 15px;
        margin-bottom: 20px;
    }

    .alert a {
        color: rgba(255, 215, 0, 1);
        text-decoration: none;
    }

    .alert a:hover {
        color: #fff;
    }

    .btn {
        background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 193, 7, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.5);
        color: #333;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: linear-gradient(45deg, rgba(255, 215, 0, 1), rgba(255, 193, 7, 1));
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        color: #222;
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .parallax {
            padding: 60px 0;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.8rem;
        }

        #map {
            height: 500px;
        }
    }

    @media (max-width: 992px) {
        .parallax {
            padding: 60px 0;
            background-attachment: scroll;
        }

        .offset-header {
            padding-top: 80px;
        }

        .glass-card {
            margin: 0 15px 30px 15px;
            border-radius: 12px;
        }

        .glass-card .panel-body {
            padding: 15px;
        }

        #map {
            height: 400px;
            border-radius: 12px;
        }
    }

    @media (max-width: 768px) {
        .parallax {
            padding: 60px 0;
        }

        .offset-header {
            padding-top: 60px;
        }

        .glass-card {
            margin: 0 10px 25px 10px;
            border-radius: 10px;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.6rem;
        }

        .glass-card .panel-body .row {
            margin-bottom: 10px;
        }

        .glass-card .panel-body .col-md-4,
        .glass-card .panel-body .col-md-8 {
            font-size: 0.9rem;
        }

        #map {
            height: 350px;
            border-radius: 10px;
        }

        .alert {
            padding: 10px;
        }

        .btn {
            padding: 8px 15px;
        }
    }

    @media (max-width: 576px) {
        .parallax {
            padding: 60px 0;
        }

        .offset-header {
            padding-top: 50px;
        }

        .glass-card {
            margin: 0 5px 20px 5px;
            border-radius: 8px;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.4rem;
        }

        .glass-card .panel-body .col-md-4,
        .glass-card .panel-body .col-md-8 {
            font-size: 1.5rem;
        }

        #map {
            height: 300px;
            border-radius: 8px;
        }

        .btn {
            padding: 6px 12px;
        }
    }

    @media (max-width: 480px) {
        .parallax {
            padding: 60px 0;
        }

        .offset-header {
            padding-top: 40px;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.2rem;
        }

        #map {
            height: 250px;
        }
    }

    @media print {
        .parallax {
            background: white;
            padding: 20px 0;
        }

        .parallax::before {
            display: none;
        }

        .glass-card {
            background: white;
            border: 1px solid #ccc;
            box-shadow: none;
        }

        .glass-card .panel-heading .panel-title,
        .glass-card .panel-body,
        .alert {
            color: black;
            text-shadow: none;
        }
    }
</style>
<?php include 'includes/header.php';?>
<section id="content" class="section parallax ">
    <div class="container">
        <?php if (isset($_SESSION['pilotid'])) {?>
        <?php if (!empty($status)) {?>
        <div class="alert alert-danger" role="alert">
            <?php if ($status == 'error') {
    if (!empty($responseMessage)) {
        echo $responseMessage;
    } else {
        echo '<p>An error occurred when dispatching the flight.</p>';
    }
}?>
        </div>
        <?php }?>
        <div class="row">
            <div class="col-12 text-center">
                <?php if ($limitDepatureLocation && $currentLocation != $flight->depIcao && !empty($currentLocation)) {?>
                <div class="alert alert-default text-center"><i class="fa fa-exclamation-triangle"
                        aria-hidden="true"></i> You can't book this flight as you are not currently located at this
                    departure airport. Head to the <a
                        href="<?php echo website_base_url; ?>site_pilot_functions/pilot_centre.php"
                        class="js_showloader btn btn-default">Dashboard</a> to take a jump seat flight.</div>
                <?php } else {?>
                <?php if ($isFlightBooked) {?>
                <div class="alert alert-success text-center"><i class="fa fa-check" aria-hidden="true"></i> You have
                    successfully booked this flight. Please visit the <a
                        href="<?php echo website_base_url; ?>site_pilot_functions/dispatch.php"
                        class="js_showloader btn btn-success">Dispatch
                        Center</a> for a flight briefing.</div>
                <?php }?>
                <?php if ($hasPilotAlreadyBooked && $_SERVER['REQUEST_METHOD'] != 'POST') {?>
                <div class="alert alert-danger text-center"><i class="fa fa-exclamation-triangle"
                        aria-hidden="true"></i> You can't book more than one flight. Please visit the <a
                        href="<?php echo website_base_url; ?>site_pilot_functions/dispatch.php"
                        class="js_showloader btn btn-danger">Dispatch
                        Center</a> for a flight briefing or to cancel your existing booking.</div>
                <?php }?>
                <?php if ((!$flight->hasActiveBid || pilots_can_book_same_flight) && !$hasPilotAlreadyBooked) {?>
                <form method="post" class="form" id="dispatchForm">
                    <button name="btnbook" type="submit" id="btnbook" class="btn btn-success"><i class="fa fa-plane"
                            aria-hidden="true"></i> Dispatch Flight</button>
                    <input name="selectedAircraft" id="selectedAircraft" type="hidden" />
                    <input name="selectedAircraftReg" id="selectedAircraftReg" type="hidden" />
                </form>
                <?php }?>
                <?php }?>
                <hr />
            </div>
        </div>
        <?php }?>
        <div class="row">
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $flight->flightNumber; ?> Flight Information</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6 col-12">
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Flight Number
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><?php echo $flight->flightNumber; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Departure ICAO
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><i class="fa fa-map-marker"></i> <a
                                            href="airport_info.php?airport=<?php echo $flight->depIcao; ?>"><?php echo $flight->depIcao; ?></a></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Departure City
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><?php echo $flight->depCity; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="md-4 col-4">
                                    Departure Time
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><i class="fa fa-clock-o"></i> <?php echo $flight->depTime; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Arrival ICAO
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><i class="fa fa-map-marker"></i> <a
                                            href="airport_info.php?airport=<?php echo $flight->arrIcao; ?>"><?php echo $flight->arrIcao; ?></a></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Arrival City
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><?php echo $flight->arrCity; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Arrival Time
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><i class="fa fa-clock-o"></i> <?php echo $flight->arrTime; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Duration Approx.
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><i class="fa fa-clock-o"></i> <?php echo $flight->duration; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Day
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><?php echo $flight->day; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Aircraft
                                </div>
                                <div class="col-md-8 col-8">
                                    <select name="aircraft" id="aircraft" class="form-control" required>
                                        <option value="" selected="true">Please select...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Aircraft Reg
                                </div>
                                <div class="col-md-8 col-8">
                                    <select name="aircraftReg" id="aircraftReg" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Operator
                                </div>
                                <div class="col-md-8 col-8">
                                    <strong><?php echo $flight->operator; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-4">
                                    Comments
                                </div>
                                <div class="col-md-8 col-8">
                                    <?php echo $flight->comments; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="map-container-glass">
                    <?php include_once 'site_widgets/map_flight.php';?>
                    <div id="map" style="height:600px;"></div>
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
                <h4 class="modal-title"><strong>Dispatch Error</strong></h4>
            </div>
            <div class="modal-body">
                <p>You must select an aircraft before dispatching the flight.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnCloseCancel" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php';?>
<script type="text/javascript">
var dataSet = <?php echo empty($flight->aircraftList) ? "null" : json_encode($flight->aircraftList); ?>;
$(document).ready(function() {

    $("#btnbook").on('click', function(e) {
        e.preventDefault();
        if ($('#aircraft').val() == '' && dataSet != null) {
            $("#errorModal").modal("show");
        } else {
            $("#dispatchForm").submit();
        }
    });

    $("#aircraftReg").prop("disabled", true);

    if (dataSet == null) {
        $("#aircraft").prop("disabled", true);
    } else {
        $.each(dataSet, function(key, value) {
            $("#aircraft").append($('<option></option>').val(value.icao).html(value.icao));
        });
    }

    $("#aircraft").on('change', function(e) {
        $('#aircraft')[0].checkValidity();
        var selected = $(this).val();
        $("#selectedAircraft").val(selected);
        $("#aircraftReg").empty().prop("disabled", true);
        $("#selectedAircraftReg").val('');
        $.each(dataSet, function(i, v) {
            if (v.icao == selected) {
                if (v.registrations.length > 0) {
                    $.each(v.registrations, function(key, value) {
                        $("#aircraftReg").append($('<option></option>').val(value).html(value));
                    });
                    $("#aircraftReg").prop("disabled", false);
                    $("#selectedAircraftReg").val($("#aircraftReg").val());
                }
                return;
            }
        });
    });

    $("#aircraftReg").on('change', function(e) {
        $("#selectedAircraftReg").val($(this).val());
    });

});
</script>