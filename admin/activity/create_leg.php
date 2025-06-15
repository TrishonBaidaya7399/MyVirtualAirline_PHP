<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(11)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$activityId = cleanString($_GET['id']);
$res = Api::sendAsync('GET', 'v1/activity/' . $activityId, null);
if ($res->getStatusCode() != 200) {
    header('Location: ' . website_base_url . 'admin/activity/');
    die();
}
$fleet = null;
$res = Api::sendAsync('GET', 'v1/operations/fleet/icaolist', null);
if ($res->getStatusCode() == 200) {
    $fleet = json_decode($res->getBody());
}
$status = null;
$aircraft = null;
$description = null;
$flightNumber = null;
$durHour = null;
$durMin = null;
$departureIcao = null;
$arrivalIcao = null;
$route = null;
$aircraftReg = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activityId = cleanString($_POST['id']);
    $description = $_POST['editorContent'];
    $flightNumber = strtoupper(cleanString($_POST['flightNumber']));
    $departureIcao = strtoupper(cleanString($_POST['departureIcao']));
    $arrivalIcao = strtoupper(cleanString($_POST['arrivalIcao']));
    $route = strtoupper(cleanString($_POST['route']));
    $durHour = cleanString($_POST['hour']);
    $durMin = cleanString($_POST['min']);
    $aircraft = cleanString($_POST['aircraft']);
    $aircraftReg = strtoupper(cleanString($_POST['aircraftReg']));

    if (!empty($durHour)) {
        if (empty($durMin)) {
            $durMin = "00";
        }
    }
    if (!empty($durMin)) {
        if (empty($durHour)) {
            $durHour = "00";
        }
    }

    $editorValid = json_decode($description)->blocks != null;

    if (empty($description) || (empty($departureIcao) && empty($arrivalIcao)) || !$editorValid) {
        $status = "required_fields";
    }

    if (empty($status) && $activityId != null) {
        $data = [
            'ActivityId' => $activityId,
            'FlightNumber' => $flightNumber,
            'DepartureIcao' => $departureIcao,
            'ArrivalIcao' => $arrivalIcao,
            'Route' => $route,
            'Duration' => !empty($durHour) ? $durHour . ':' . $durMin . ':' . '00' : null,
            'Description' => $description,
            'Aircraft' => $aircraft,
            'AircraftReg' => $aircraftReg,
        ];

        $res = Api::sendSync('POST', 'v1/activity/leg', $data);

        switch ($res->getStatusCode()) {
            case 200:
                header("Location: edit.php?id=" . $activityId);
                die();
            case 400:
                $status = "error";
                break;
            default:
                $status = "error";
                break;
        }
    }
}
?>
<?php include '../includes/nav.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="grid"></i></div>
                            Create Activity Leg
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Tours/Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php if (!empty($status)) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            if ($status == 'error') {
                echo 'An error occurred when creating the activity leg. Please try again later.';
            }
            if ($status == 'required_fields') {
                echo 'Please check all fields have been completed correctly. You must also have at least an arrival or a departure airport set.';
            }
            ?>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Leg Details</div>
            <div class="card-body">
                <form method="post" class="form" id="editorForm" enctype="multipart/form-data">
                    <div class="row gx-3 g-5 mb-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="mb-1">Description*</label>
                                <div id="editorJs" class="form-control"></div>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="mb-1">Departure Icao</label>
                                        <input maxlength="4" name="departureIcao" type="text" id="departureIcao"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars(strval($departureIcao)) ?>"
                                            style="text-transform:uppercase">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1">Arrival Icao</label>
                                        <input maxlength="4" name="arrivalIcao" type="text" id="arrivalIcao"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars(strval($arrivalIcao)) ?>"
                                            style="text-transform:uppercase">
                                    </div>

                                </div>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> You can leave
                                    arrival or departure blank for fly-in/out
                                    events.</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Route</label>
                                <textarea name="route" cols="30" rows="5" id="route" class="form-control"
                                    style="text-transform:uppercase"><?php echo htmlspecialchars(strval($route)) ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="mb-1">Approx. Duration</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="mb-1">Hours</label>
                                        <select name="hour" id="hour" class="form-select">
                                            <option value="" selected="true"></option>
                                            <option value="00">00</option>
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
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1">Minutes</label>
                                        <select name="min" id="min" class="form-select">
                                            <option value="" selected="true"></option>
                                            <option value="00">00</option>
                                            <option value="05">05</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="55">55</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-1">Flight Number</label>
                                <input name="flightNumber" type="text" id="flightNumber" class="form-control"
                                    value="<?php echo htmlspecialchars(strval($flightNumber)) ?>"
                                    style="text-transform:uppercase">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Aircraft Type</label>
                                <select name="aircraft" id="aircraft" class="form-select">
                                    <option value="" selected="true"></option>
                                    <?php
                                    foreach ($fleet as $fleetIcao) {
                                    ?>
                                        <option value="<?php echo $fleetIcao; ?>"
                                            <?php echo empty($aircraft == $fleetIcao) ? "" : 'selected="true"' ?>>
                                            <?php echo $fleetIcao; ?>
                                        </option>
                                    <?php
                                    } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Aircraft Reg</label>
                                <input name="aircraftReg" type="text" id="aircraftReg" class="form-control"
                                    value="<?php echo htmlspecialchars(strval($aircraftReg)) ?>"
                                    style="text-transform:uppercase">
                            </div>
                            <div class="mb-3">
                                <div class=" text-right">
                                    <input name="id" type="hidden" id="id" value="<?php echo $activityId; ?>">
                                    <input name="editorContent" type="hidden" id="editorContent" />
                                    <button id="submitButton" type="submit" class="btn btn-primary">Save
                                        Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.7"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.8"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.6"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@2.5.0"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script src="<?php echo website_base_url; ?>admin/js/editor.js"></script>
<script type="text/javascript">
    contentJson = '<?php echo $description != null ? addslashes($description) : ""; ?>';
</script>