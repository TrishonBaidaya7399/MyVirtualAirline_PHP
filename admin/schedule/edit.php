<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(4)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

Api::__constructStatic();
$id = cleanString($_GET['id']);
$status = null;
$errorMessage = null;
$flightNumber = null;
$depIcao = null;
$depCity = null;
$arrIcao = null;
$arrCity = null;
$depTime = null;
$arrTime = null;
$duration = null;
$day = null;
$aircraft = null;
$operator = null;
$route = null;
$comments = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = cleanString(trim($_POST['id']));
    $flightNumber = cleanString(trim($_POST['flight_number']));
    $depIcao = cleanString(trim($_POST['dep_icao']));
    $depCity = cleanString(trim($_POST['dep_city']));
    $arrIcao = cleanString(trim($_POST['arr_icao']));
    $arrCity = cleanString(trim($_POST['arr_city']));
    $depTime = cleanString(trim($_POST['dep_time']));
    $arrTime = cleanString(trim($_POST['arr_time']));
    $duration = cleanString(trim($_POST['duration']));
    $day = isset($_POST['day']) ? implode(' ', $_POST['day']) : "";
    $aircraft = cleanString(trim($_POST['aircraft']));
    $operator = cleanString(trim($_POST['operator']));
    $route = cleanString(trim($_POST['route']));
    $comments = cleanString(trim($_POST['comments']));

    $data = [
        'Id' => $id,
        'FlightNumber' => $flightNumber,
        'DepIcao' => $depIcao,
        'ArrIcao' => $arrIcao,
        'DepCity' => $depCity,
        'ArrCity' => $arrCity,
        'DepTime' => $depTime,
        'ArrTime' => $arrTime,
        'Duration' => $duration,
        'Day' => $day,
        'Aircraft' => $aircraft,
        'Route' => $route,
        'Operator' => $operator,
        'Comments' => $comments
    ];

    $res = Api::sendSync('PUT', 'v1/operations/schedule/flight', $data);
    switch ($res->getStatusCode()) {
        case 200:
            $status = "updated";
            break;
        case 400:
            $status = "error";
            $errorMessage = json_decode($res->getBody());
            break;
        default:
            $status = "error";
            break;
    }
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $flight = null;
    $res = Api::sendAsync('GET', 'v1/operations/schedule/flight/' . $id, null);
    if ($res->getStatusCode() == 200) {
        $flight = json_decode($res->getBody());
        $id = $flight->id;
        $flightNumber = $flight->flightNumber;
        $depIcao = $flight->depIcao;
        $depCity = $flight->depCity;
        $arrIcao = $flight->arrIcao;
        $arrCity = $flight->arrCity;
        $depTime = $flight->depTime;
        $arrTime = $flight->arrTime;
        $duration = $flight->duration;
        $day = $flight->day;
        $aircraft = $flight->aircraft;
        $operator = $flight->operator;
        $route = $flight->route;
        $comments = $flight->comments;
    } else {
        header('Location: ' . website_base_url . 'schedule');
        die();
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
                            Edit Route
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Schedule
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Route Details</div>
            <div class="card-body">
                <?php if ($status == "updated") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Route has been successfully updated.
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
                <?php if ($status == "error") { ?>
                    <div class="alert alert-danger alert-dismissible fade show">An error has occurred updating the
                        flight.<br />
                        <ul>
                            <?php if (!empty($errorMessage->errors)) {
                                echo '<br />';
                                foreach ($errorMessage->errors as $err) {
                                    echo '<li>' . $err[0] . '</li>';
                                }
                            } else {

                                if (!empty($errorMessage->message)) echo '<li>' . $errorMessage->message . '</li>';
                            }
                            ?>
                        </ul>
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
                <form method="post" class="form" enctype="multipart/form-data">
                    <div class="row gx-3 g-5 mb-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="mb-1">Flight Number*</label>
                                <input name="flight_number" type="text" id="flight_number"
                                    value="<?php echo $flightNumber; ?>" class="form-control" required>
                                <input name="id" type="hidden" id="id" value="<?php echo $id; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Departure ICAO*</label>
                                <input name="dep_icao" type="text" id="dep_icao" value="<?php echo $depIcao; ?>"
                                    maxlength="4" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Departure City*</label>
                                <input name="dep_city" type="text" id="dep_city" value="<?php echo $depCity; ?>"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Departure Time (UTC)*</label>
                                <input name="dep_time" type="text" id="dep_time3" maxlength="8"
                                    value="<?php echo $depTime; ?>" class="form-control" required>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Example:
                                    01:00:00 (max 24 hours)</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Arrival ICAO*</label>
                                <input name="arr_icao" type="text" id="arr_icao" value="<?php echo $arrIcao; ?>"
                                    maxlength="4" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Arrival City*</label>
                                <input name="arr_city" type="text" id="arr_city" value="<?php echo $arrCity; ?>"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Arrival Time (UTC)*</label>
                                <input name="arr_time" type="text" id="arr_time" maxlength="8"
                                    value="<?php echo $arrTime; ?>" class="form-control" required>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Example:
                                    01:00:00 (max 24 hours)</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Flight Duration*</label>
                                <input name="duration" type="text" id="duration" maxlength="8"
                                    value="<?php echo $duration; ?>" class="form-control" required>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Example:
                                    01:00:00 (max 24 hours)</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="Mo">
                                        <label class="mb-1">Mo</label>
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="Tu">
                                        <label class="mb-1">Tu</label>
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="We">
                                        <label class="mb-1">We</label>
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="Th">
                                        <label class="mb-1">Th</label>
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="Fr">
                                        <label class="mb-1">Fr</label>
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="Sa">
                                        <label class="mb-1">Sa</label>
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label>
                                        <input type="checkbox" name="day[]" value="Su">
                                        <label class="mb-1">Su</label>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Aircraft</label>
                                <textarea name="aircraft" type="text" id="aircraft"
                                    class="form-control"><?php echo $aircraft; ?></textarea>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Example:
                                    B738:NG1234;B738:NG5678;A320:NG9101</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Operator</label>
                                <input name="operator" type="text" id="operator" value="<?php echo $operator; ?>"
                                    class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Route*</label>
                                <textarea name="route" cols="30" rows="5" id="route" class="form-control"
                                    required><?php echo $route; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Comments</label>
                                <textarea name="comments" cols="30" rows="5" id="comments"
                                    class="form-control"><?php echo $comments; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <div class="">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
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
<script type="text/javascript">
    var days = ('<?php echo $day; ?>').split(' ');
    $(document).ready(function() {
        $.each(days, function(index, value) {
            $(":checkbox[value=" + value + "]").prop("checked", "true");
        });
    });
</script>
<?php include '../includes/footer.php'; ?>