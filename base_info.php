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
validateSession();
Api::__constructStatic();

$baseId = cleanString($_GET['id']);
$arricao = null;
$alticao = "";
$path_data = null;
$base = null;
$res = Api::sendSync('GET', 'v1/operations/base/' . $baseId, null);
if ($res->getStatusCode() == 200) {
    $base = json_decode($res->getBody(), false);
} else {
    header('Location: ' . website_base_url);
    die();
}

$mapData = [$base->b->airportInfo];
$roster = json_encode($base->r);
$roster = json_decode($roster, true);
if (!empty($roster)) {
    foreach ($roster as &$pilot) {
        $pilot["location"] = '<img src="' . website_base_url . 'images/flags/' . $pilot['location'] . '.gif" width="20" height="20" title="' . $pilot['location'] . '">';
        $pilot["rankImage"] = '<img src="' . website_base_url . 'uploads/ranks/' . $pilot['rank']['imageUrl'] . '" width="80" title="' . $pilot['rank']['name'] . '">';

        if ($pilot['profileImage'] != "") {
            $image = '<img src="' . website_base_url . 'uploads/profiles/' . $pilot['profileImage'] . '" class="img-circle pilot-profile-image-small"/>';
        } else {
            $image = '<i class="fa fa-user-circle profile-small" aria-hidden="true"></i>';
        }
        $pilot["name"] = $image . '&nbsp;<a href="' . website_base_url . 'profile.php?id=' . $pilot['id'] . '">' . $pilot['name'] . '</a></td>';
    }
}

?>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="assets/plugins/datatables/datatables.min.css" />
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="<?php echo website_base_url; ?>bases.php"><i class="fa fa-angle-double-left js_showloader"
                        aria-hidden="true"></i> Back to bases</a>
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo htmlspecialchars(strval($base->b->hub)); ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 base-content">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Base Statistics
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2 col-xs-2">
                                <p class="small text-center">Total Pilots</p>
                            </div>
                            <div class="col-md-2 col-xs-2">
                                <p class="small text-center">30 Day Flights</p>
                            </div>
                            <div class="col-md-2 col-xs-2">
                                <p class="small text-center">30 Day Hours</p>
                            </div>
                            <div class="col-md-2 col-xs-2">
                                <p class="small text-center">Sched Flights</p>
                            </div>
                            <div class="col-md-2 col-xs-2">
                                <p class="small text-center">Destinations</p>
                            </div>
                            <div class="col-md-2 col-xs-2">
                                <p class="small text-center">ICAO</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 text-center col-xs-2">
                                <strong><?php echo $base->b->totalPilots; ?></strong>
                            </div>
                            <div class="col-md-2 text-center col-xs-2">
                                <strong><?php echo $base->b->thirtyDayFlights; ?></strong>
                            </div>
                            <div class="col-md-2 text-center col-xs-2">
                                <strong><?php echo $base->b->thirtyDayHours; ?></strong>
                            </div>
                            <div class="col-md-2 text-center col-xs-2">
                                <strong><?php echo $base->b->totalScheduledFlights; ?></strong>
                            </div>
                            <div class="col-md-2 text-center col-xs-2">
                                <strong><?php echo $base->b->totalDestinations; ?></strong>
                            </div>
                            <div class="col-md-2 text-center col-xs-2">
                                <strong><?php echo $base->b->icao; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Base Roster</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped roster" id="base">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>&nbsp;</th>
                                    <th>Pilot Id</th>
                                    <th>Rank</th>
                                    <th>Last<br />30 Days</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="row">
                    <img src="<?php echo website_base_url; ?>uploads/bases/<?php echo $base->b->imageUrl; ?>"
                        style="margin-bottom:30px;" width="350" height="350" class="text-center" />
                </div>

                <div class="row">
                    <?php include_once 'site_widgets/map_flight.php'; ?>
                    <div id="map" style="height:300px;width:350px;"></div>
                </div>
            </div>
            <div class="row text-center">
                <a href="<?php echo website_base_url; ?>airport_info.php?airport=<?php echo $base->b->icao; ?>"
                    style="margin-top:30px;" class="btn btn-default">View Airport Information <i
                        class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>

    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script type="text/javascript" src="assets/plugins/datatables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script type="text/javascript">
var dataSet = <?php echo json_encode($roster); ?>;
var descJson =
    '<?php echo $base->b->description != null ? addslashes(preg_replace("/\r|\n/", "", $base->b->description)) : ""; ?>';
$(document).ready(function() {
    try {
        var parser = new edjsParser({
            embed: {
                useProvidedLength: false,
            }
        });
        var html = parser.parse(JSON.parse(descJson));
        $(".base-content").html(html)
        console.log(html);
    } catch (e) {
        $(".base-content").html(descJson);
    }
    $('#base').DataTable({
        data: dataSet,
        "pageLength": 25,
        columns: [{
                data: "name"
            },
            {
                data: "location",
                "orderable": false
            },
            {
                data: "callsign"
            },
            {
                data: "rankImage",
                "orderable": false
            },
            {
                data: "thirtyDayHours"
            }
        ],
        colReorder: false,
        "order": [
            [2, 'asc']
        ]
    });
});
</script>