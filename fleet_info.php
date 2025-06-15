<?php

use Proxy\Api\Api;

include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
session_start();

$id = cleanString($_GET['id']);
$res = Api::sendSync('GET', 'v1/operations/aircraft/' . $id, null);
$aircraft = json_decode($res->getBody());
$responseCode = $res->getStatusCode();
if ($responseCode != 200) {
    echo '<script>alert("No aircraft exists under this id");</script>';
    echo '<script>history.back(1);</script>';
    exit;
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
        <div class="row">
            <div class="col-md-12">
                <i class="fa fa-angle-double-left" aria-hidden="true"></i> <a
                    href="<?php echo website_base_url; ?>fleet.php">Back
                    to Fleet</a>
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <h1 class="title"><?php echo $aircraft->description; ?>
                </h1>
                <div class="fleet-content"></div>
            </div>
            <div class="col-md-5 text-right">
                <img src="<?php echo website_base_url; ?>uploads/fleet/<?php echo $aircraft->imageUrl; ?> "
                    width="350" />
            </div>
        </div>
        <div class="col-md-12">
            <hr />
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Statistics & Information</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6 col-xs-6">
                            <div class="row">
                                <div class="col-md-4">
                                    ICAO
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->icao; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Name
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->description; ?></a></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Operator
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->operator == "" ? "N/A" : $aircraft->operator; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    PAX
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->pax; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Crew
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->totalCrew; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Cargo
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo getCargoDisplayValue($aircraft->cargo); ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    MTOW
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->mtow; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    MLW
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->mlw; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    MZFW
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->mzfw; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="row">
                                <div class="col-md-4">
                                    Total in Fleet
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo empty($aircraft->totalInFleet) ? "" : number_format($aircraft->totalInFleet); ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Service Ceiling
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo empty($aircraft->maxAlt) ? "" : number_format($aircraft->maxAlt); ?>ft</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Range
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo empty($aircraft->maxRange) ? "" : number_format($aircraft->maxRange); ?>nm</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Max Speed
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->speed; ?>kts</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Wingspan
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo number_format($aircraft->wingspan, 2); ?>m</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Length
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo number_format($aircraft->length, 2); ?>m</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Height
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo number_format($aircraft->height, 2); ?>m</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Engine Type
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->engineModel; ?></strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Minimum Pilot Rank
                                </div>
                                <div class="col-md-8">
                                    <strong><?php echo $aircraft->minRank->name ?? "None"; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Registrations</h3>
                    </div>
                    <div class="panel-body registrations-panel">
                        <div class="row registration-items">
                            <div class="col-md-12">
                                <?php if (empty($aircraft->registrations)) { ?>
                                    No aircraft regisrations.
                                    <hr />
                                <?php } else { ?>
                                    <?php foreach (explode(',', $aircraft->registrations) as $reg) { ?>
                                        <div class="row">
                                            <div class="col-md-12 col-xs-12">
                                                <?php echo $reg; ?>
                                            </div>
                                        </div>
                                        <hr />
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script type="text/javascript">
    var descJson =
        '<?php echo $aircraft->notes != null ? addslashes(preg_replace("/\r|\n/", "", $aircraft->notes)) : ""; ?>';
    $(document).ready(function() {
        try {
            var parser = new edjsParser({
                embed: {
                    useProvidedLength: false,
                }
            });
            var html = parser.parse(JSON.parse(descJson));
            $(".fleet-content").html(html)
            console.log(html);
        } catch (e) {
            $(".fleet-content").html(descJson);
        }
    });
</script>
<?php include 'includes/footer.php'; ?>