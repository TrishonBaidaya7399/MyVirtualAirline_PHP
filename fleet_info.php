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
<style>
    /* Parallax Background */
.parallax{
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 200px);
    background-image: url('./assets/images/backgrounds/fleet_bg.jpg');
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
    background: rgba(0, 0, 0, 0.2);
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
        }
    }
    /* Glass Backdrop for Cards */
    .glass_back_btn {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 7px;
        color: #fff !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 2;
        position: relative;
        width: fit-content;
        height:fit-content;
        padding: 8px 12px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 800px){
        .glass_back_btn {
        margin-top: 0px;
        margin-bottom: 0px;
        }
    }
        .glass_back_btn a{
        color: #fff !important;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 2;
        position: relative;
        margin-bottom: 20px;
    }

    .glass-card .panel-heading{
        border-radius: 10px 10px 0 0 ;
        background: rgba(255, 255, 255, 0.3)
    }
    .glass-card .panel-heading,
    .glass-card .panel-title {
        color: #fff;
font-size: 20px !important;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
width: 100%;

    }

    .glass-card .panel-body {
        color: #fff;
        padding-inline: 15px;
        padding-bottom: 0 !important;
        padding-top: 0 !important;
    }

    .glass-card strong {
        color: #e0e0e0;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .title {
            font-size: 2rem;
            text-align: center;
        }

        .col-md-7,
        .col-md-5,
        .col-md-9,
        .col-md-3 {
            padding-left: 0px !important;
            padding-right: 0px !important;
        }

        img {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            display: block;
        }

        .row-space .row {
            margin-bottom: 10px;
        }

        .row-space .col-md-4 {
            font-size: 0.9rem;
        }

        .row-space .col-md-8 {
            font-size: 0.9rem;
        }

        .registrations-panel .row {
            margin-bottom: 5px;
        }
    }

    /* Ensure text readability */
    #content {
        padding-top: 60px;
        padding-bottom: 60px;
        position: relative;
        z-index: 2;
    }

    .title {
        color: #fff;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }

    a {
        color: #00b4d8;
        text-decoration: none;
    }

    a:hover {
        color: #0077b6;
        text-decoration: underline;
    }

    hr {
        border-color: rgba(255, 255, 255, 0.2);
    }
</style>
<section id="content" class="cp section offset-header parallax">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center glass_back_btn">
                <i class="fa fa-angle-double-left" aria-hidden="true" style="color: #fff;"></i> <a
                    href="<?php echo website_base_url; ?>fleet.php">Back
                    to Fleet</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <h1 class="title"><?php echo $aircraft->description; ?></h1>
                <div class="fleet-content glass-card" style="padding: 20px;"></div>
            </div>
            <div class="col-md-5 text-center">
                <img src="<?php echo website_base_url; ?>uploads/fleet/<?php echo $aircraft->imageUrl; ?>" style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);" />
            </div>
        </div>
        <div class="col-md-12">
            <hr />
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Statistics & Information</h3>
                    </div>
                    <div class="panel-body row-space" style="max-height: 350px; overflow-y: auto">
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
                                    <strong><?php echo $aircraft->description; ?></strong>
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
                <div class="panel panel-default glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Registrations</h3>
                    </div>
                    <div class="panel-body registrations-panel">
                        <div class="row registration-items" style="max-height: 338px; overflow-y: auto">
                            <div class="col-md-12">
                                <?php if (empty($aircraft->registrations)) { ?>
                                    No aircraft registrations.
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
            $(".fleet-content").html(html);
            console.log(html);
        } catch (e) {
            $(".fleet-content").html(descJson);
        }
    });
</script>
<?php include 'includes/footer.php'; ?>