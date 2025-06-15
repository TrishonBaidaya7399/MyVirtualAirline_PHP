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
<?php include 'includes/header.php';?>
<section id="content" class="cp section offset-header">
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
            <div class="col-md-12 text-right">
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
                <?php } else {?>

                <?php }?>
                <?php }?>
                <hr />
            </div>
        </div>
        <?php }?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $flight->flightNumber; ?>
                            Flight Information </h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6 col-xs-6">
                            <div class="row">
                                <div class="col-md-4">
                                    Flight Number
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $flight->flightNumber; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Departure ICAO
                                </div>
                                <div class="col-md-8">
                                    <strong><i class="fa fa-map-marker"></i> <a
                                            href="airport_info.php?airport=<?php echo $flight->depIcao; ?>"><?php echo $flight->depIcao; ?></a></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Departure City
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $flight->depCity; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Departure Time
                                </div>
                                <div class="col-md-8">
                                    <strong><i class="fa fa-clock-o"></i> <?php echo $flight->depTime; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Arrival ICAO
                                </div>
                                <div class="col-md-8">
                                    <strong><i class="fa fa-map-marker"></i> <a
                                            href="airport_info.php?airport=<?php echo $flight->arrIcao; ?>"><?php echo $flight->arrIcao; ?></a></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Arrival City
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $flight->arrCity; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Arrival Time
                                </div>
                                <div class="col-md-8">
                                    <strong><i class="fa fa-clock-o"></i> <?php echo $flight->arrTime; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="row">
                                <div class="col-md-4">
                                    Duration Approx.
                                </div>
                                <div class="col-md-8">
                                    <strong><i class="fa fa-clock-o"></i> <?php echo $flight->duration; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Day
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $flight->day; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Aircraft
                                </div>
                                <div class="col-md-8">
                                    <!-- <strong><?php echo $flight->aircraft; ?></strong> -->
                                    <select name="aircraft" id="aircraft" class="form-control" required>
                                        <option value="" selected="true">Please select...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Aircraft Reg
                                </div>
                                <div class="col-md-8">
                                    <!-- <strong><?php echo $flight->aircraftReg; ?></strong> -->
                                    <select name="aircraftReg" id="aircraftReg" class="form-control">

                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Operator
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $flight->operator; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Comments
                                </div>
                                <div class="col-md-8">
                                    <?php echo $flight->comments; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php include_once 'site_widgets/map_flight.php';?>
                <div id="map" style="height:600px;"></div>
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
                <h4 class="modal-title"><strong>Dispatch Error<strong></h4>
            </div>
            <div class="modal-body">
                <p>You must select an aircraft before dispatching the flight.
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
                        $("#aircraftReg").append($('<option></option>').val(value)
                            .html(
                                value));
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