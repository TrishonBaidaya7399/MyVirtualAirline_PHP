<?php
use Proxy\Api\Api;

include '../../lib/functions.php';
include '../../config.php';
include_once 'simbrief.apiv1.php';
Api::__constructStatic();
session_start();
validateSession();
$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == 'delete') {
    $res = Api::sendSync('DELETE', 'v1/bid/' . $_SESSION['pilotid'], null);
    if ($res->getStatusCode() == 200) {
        header('Location: ' . website_base_url . 'site_pilot_functions/dispatch.php');
        exit();
    }
}
$xmlLink = $_GET['ofp_id'];
$status = null;
if (!empty($xmlLink)) {
    if (!empty($_SESSION['pilotid'])) {
        $bid = null;
        $res = Api::sendSync('GET', 'v1/bid/' . $_SESSION['pilotid'], null);
        if ($res->getStatusCode() == 200) {
            $bid = json_decode($res->getBody(), false);
            if (!empty($bid)) {
                $data = [
                    'id' => $bid->id,
                    'Route' => strval($simbrief->ofp_obj->general->route),
                    'TotalPassengers' => intval($simbrief->ofp_obj->weights->pax_count),
                    'TotalCargo' => cargo_weight_display == 1 ? intval(convertLbToKg($simbrief->ofp_obj->weights->cargo)) : intval($simbrief->ofp_obj->weights->cargo),
                    'SbDispatched' => true,
                    'Altitude' => intval($simbrief->ofp_obj->general->initial_altitude),
                    'SbOfpId' => $xmlLink,
                    'AlternateIcao' => strval($simbrief->ofp_obj->alternate->icao_code),
                    'FlightNumber' => simbrief_airline . strval($simbrief->ofp_obj->general->flight_number),
                    'DepartureIcao' => strval($simbrief->ofp_obj->origin->icao_code),
                    'ArrivalIcao' => strval($simbrief->ofp_obj->destination->icao_code),
                ];
                $res = Api::sendSync('PUT', 'v1/bid', $data);
                if ($res->getStatusCode() != 200) {
                    $status = "bid-update-failed";
                }
            }
        } else {
            // create new bid
            $data = [
                'PilotId' => $_SESSION['pilotid'],
                'DepartureIcao' => strval($simbrief->ofp_obj->origin->icao_code),
                'ArrivalIcao' => strval($simbrief->ofp_obj->destination->icao_code),
                'TotalPax' => intval($simbrief->ofp_obj->weights->pax_count),
                'Route' => strval($simbrief->ofp_obj->general->route),
                'FlightNumber' => simbrief_airline . strval($simbrief->ofp_obj->general->flight_number),
                'AircraftIcao' => strval($simbrief->ofp_obj->aircraft->icaocode),
                'AircraftReg' => strval($simbrief->ofp_obj->aircraft->reg),
                'Cargo' => cargo_weight_display == 1 ? intval(convertLbToKg($simbrief->ofp_obj->weights->cargo)) : intval($simbrief->ofp_obj->weights->cargo),
                'SbOfpId' => $xmlLink,
                'SbDispatched' => true,
                'Altitude' => intval($simbrief->ofp_obj->general->initial_altitude),
                'AlternateIcao' => strval($simbrief->ofp_obj->alternate->icao_code),
            ];
            $res = Api::sendSync('POST', 'v1/bid', $data);
            if ($res->getStatusCode() == 200) {
                $status = "created-new-bid";
            } else {
                $status = "bid-create-failed";
            }
        }
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../../includes/header.php';?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <a href="<?php echo website_base_url; ?>site_pilot_functions/simbrief/dispatch.php"><i
                        class="fa fa-angle-double-left js_showloader" aria-hidden="true"></i> Edit flight plan</a>
            </div>
            <div class="col-md-6">
                <div class="dropdown align-right"><button class="btn btn-default dropdown-toggle" type="button"
                        id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Actions <span
                            class="caret"></span></button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a class="btn"
                                href="<?php echo $simbrief->ofp_obj->files->directory . $simbrief->ofp_obj->files->pdf->link; ?>"
                                target="_blank">Download
                                PDF</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a class="btn" href="#" id="vatsim">Pre-file on Vatsim</a></li>
                        <li><a class="btn" href="#" id="ivao">Pre-file on Ivao</a></li>
                        <li><a class="btn" href="<?php echo $simbrief->ofp_obj->pilotedge_prefile; ?>"
                                target="_blank">Pre-file on Pilot Edge</a></li>
                        <li><a class="btn" href="<?php echo $simbrief->ofp_obj->poscon_prefile; ?>"
                                target="_blank">Pre-file on Poscon</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a class="btn" href="?function=delete">Delete Flight</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <hr />
        <?php if (!empty($status)) {?>
        <div class="row">
            <?php if ($status == "bid-update-failed") {?>
            <div class="alert alert-danger" role="alert">
                <p><i class="fa fa-warning" aria-hidden="true"></i> Unable to update current bid with SimBrief dispatch
                    data.</p>
            </div>
            <?php }?>
            <?php if ($status == "bid-create-failed") {?>
            <div class="alert alert-danger" role="alert">
                <p><i class="fa fa-warning" aria-hidden="true"></i> Unable to import new flight into vaBase.</p>
            </div>
            <?php }?>
        </div>
        <?php }?>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">OFP Summary</h3>
                </div>
                <div class="panel-body row-space">
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <strong>Flight No</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Aircraft</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Depart</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Arrive</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Alternate</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Initial Alt</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <?php echo simbrief_airline . $simbrief->ofp_obj->general->flight_number; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->aircraft->icaocode; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->origin->icao_code; ?>/<?php echo $simbrief->ofp_obj->origin->plan_rwy; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->destination->icao_code; ?>/<?php echo $simbrief->ofp_obj->destination->plan_rwy; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->alternate->icao_code; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->general->initial_altitude; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <strong>Depart</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Air Time</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Block Fuel</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Extra Fuel</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>ZFW</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>TOW</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <?php echo date('H:i', intval($simbrief->ofp_obj->times->est_out)); ?>
                            UTC
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo date('H:i', intval($simbrief->ofp_obj->times->est_time_enroute)); ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->fuel->plan_ramp; ?><?php echo $simbrief->ofp_obj->params->units; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->times->extrafuel_time / 60; ?>
                            min
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->weights->est_zfw; ?><?php echo $simbrief->ofp_obj->params->units; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->weights->est_tow; ?><?php echo $simbrief->ofp_obj->params->units; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <strong>Cargo</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Distance</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>AIRAC</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Format</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <strong>Units</strong>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            &nbsp;
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->weights->cargo; ?><?php echo $simbrief->ofp_obj->params->units; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->general->air_distance; ?>nm
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo $simbrief->ofp_obj->params->airac; ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo strtoupper($simbrief->ofp_obj->api_params->planformat); ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <?php echo strtoupper($simbrief->ofp_obj->params->units); ?>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            &nbsp;
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <strong>Routing</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?php echo $simbrief->ofp_obj->general->route; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <strong>DX Remarks</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?php echo $simbrief->ofp_obj->general->dx_rmk; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Briefing</h3>
                </div>
                <div class="panel-body">
                    <div class="row row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Flight Plan</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-y:scroll;max-height:400px;">
                                    <?php if ($simbrief->ofp_obj) {?>
                                    <p><?php echo $simbrief->ofp_obj->text->plan_html; ?>
                                        <?php }?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Weather</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Origin METAR</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $simbrief->ofp_obj->weather->orig_metar; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Destination METAR</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $simbrief->ofp_obj->weather->dest_metar; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Alternate METAR</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $simbrief->ofp_obj->weather->altn_metar; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Attachments</h3>
                </div>
                <div class="panel-body" style="overflow-y:scroll; max-height:800px;">

                    <?php foreach ($simbrief->ofp_obj->images->map as $image) {?>
                    <div class="row">
                        <div class="col-md-12">
                            <img src="<?php echo $simbrief->ofp_obj->images->directory . $image->link; ?>"
                                width="100%" />
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</section>
<span class="prefile">
    <?php echo $simbrief->ofp_obj->vatsim_prefile; ?>
    <?php echo $simbrief->ofp_obj->ivao_prefile; ?>
</span>
<?php include '../../includes/footer.php';?>
<script type="text/javascript">
$(document).ready(function() {
    $("form").each(function() {
        $(this).hide();
    });
    $("#vatsim").click(function() {
        $("form[method='GET']").submit();
        Loader.stop();
    });
    $("#ivao").click(function() {
        $("form[action='https://fpl.ivao.aero/flight-plans/create']").submit();
        Loader.stop();
    });
});
</script>