<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/emailer.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(7)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$id = cleanString($_GET['id']);
$status = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flightNumber = cleanString($_POST['flight_number']);
    $departureIcao = strtoupper(cleanString($_POST['depicao']));
    $arrivalIcao = strtoupper(cleanString($_POST['arricao']));
    $alternateIcao = strtoupper(cleanString($_POST['alticao']));
    $route = strtoupper(cleanString($_POST['route']));
    $aircraft = cleanString($_POST['aircraft']);
    $comments = cleanString($_POST['comments']);
    $adminComments = cleanString($_POST['adminComments']);
    $approvedStatus = cleanString($_POST['status']);
    $currentApprovalStatus = cleanString($_POST['currentStatus']);
    $pilotId = cleanString($_POST['pilotId']);
    if (empty($flightNumber) || (empty($departureIcao) || empty($arrivalIcao) || empty($aircraft))) {
        $status = "required_fields";
    }
    if (empty($status)) {
        $data = [
            'Id' => $id,
            'FlightNumber' => $flightNumber,
            'DepartureIcao' => $departureIcao,
            'ArrivalIcao' => $arrivalIcao,
            'AlternateIcao' => $alternateIcao,
            'Route' => $route,
            'Comments' => $comments,
            'AdminComments' => $adminComments,
            'Aircraft' => $aircraft,
            'ApprovedStatus' => $approvedStatus != $currentApprovalStatus ? $approvedStatus : $currentApprovalStatus,
        ];
        $res = Api::sendSync('PUT', 'v1/operations/pirep', $data);
        switch ($res->getStatusCode()) {
            case 200:
                $status = "updated";
                if ($approvedStatus != $currentApprovalStatus) {
                    $pilot = null;
                    $res = Api::sendSync('GET', 'v1/pilot/' . $pilotId, null);
                    $pilot = json_decode($res->getBody());
                    if (!empty($pilot)) {
                        $pirepStatus = "";
                        switch ($approvedStatus) {
                            case 0:
                                $pirepStatus = "<span style='color:orange;'>Pending Approval</span>";
                                break;
                            case 1:
                                $pirepStatus = "<span style='color:green;'>Approved</span>";
                                break;
                            case 2:
                                $pirepStatus = "<span style='color:red;'>Denied</span>";
                                break;
                        }
                        //send email notification
                        $subject = virtual_airline_name . " | Your PIREP has been reviewed";
                        $message = "Dear Pilot, <br /><br />Your flight from <strong>" . $departureIcao . "</strong> to <strong>" . $arrivalIcao . "</strong> has been reviewed.<br /><br />Reviewed Status: " . $pirepStatus . "<br /><br />Admin Comments: " . $adminComments
                            . "<br /><br /><a href='" . website_base_url . "pirep_info.php?id=" . $id . "'>> View Full Flight Report</a><br /><br />Kind Regards,<br />
					" . virtual_airline_name . " Team.";
                        echo sendEmail($subject, $message, $pilot->email);
                    }
                }
                break;
            case 400:
                $status = "error";
                break;
            default:
                $status = "error";
                break;
        }
    }
}
$pirep = null;
$res = Api::sendAsync('GET', 'v1/operations/pirep/' . $id, null);
if ($res->getStatusCode() == 200) {
    $pirep = json_decode($res->getBody());
    $pirepStatus = "";
    switch ($pirep->approvedStatus) {
        case 0:
            $pirepStatus = "<span class='badge bg-yellow-soft text-yellow'>Pending Approval</span>";
            break;
        case 1:
            $pirepStatus = "<span class='badge bg-green-soft text-green'>Approved</span>";
            break;
        case 2:
            $pirepStatus = "<span class='badge bg-red-soft text-red'>Denied</span>";
            break;
    }
} else {
    header('Location: ' . website_base_url . 'admin/index.php');
    die();
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
                            Edit Flight Report - <?php echo $pirep->flightNumber; ?>
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to PIREP List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="row">
            <?php if ($status == "updated") { ?>
            <div class="alert alert-success alert-dismissible fade show">Pilot report has been updated.
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <?php if ($status == "error") { ?>
            <div class="alert alert-danger alert-dismissible fade show">An error has occurred updating the PIREP.
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <?php if ($status == "required_fields") { ?>
            <div class="alert alert-danger alert-dismissible fade show">Please check all required fields have been
                completed.
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Flight Details</div>
                    <div class="card-body">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-12">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="text-dark">Status:</td>
                                            <td><?php echo $pirepStatus; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Diverted::</td>
                                            <td><?php echo $pirep->arrivedAlternate ? "Yes" : "No"; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Pilot Id:</td>
                                            <td><?php echo $pirep->callsign; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Date Filled:</td>
                                            <td><?php echo (new DateTime($pirep->dateFilled))->format('d M Y H:m'); ?>
                                                UTC</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Date Flown:</td>
                                            <td><?php echo (new DateTime($pirep->date))->format('d M Y H:m'); ?>
                                                UTC</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Distance:</td>
                                            <td><?php echo $pirep->distance; ?>nm</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Fuel Consumed:</td>
                                            <td><?php echo is_null($pirep->fuel) ? getFuelDisplayValue(0) : getFuelDisplayValue($pirep->fuel); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">PAX:</td>
                                            <td><?php echo $pirep->pax; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Cargo:</td>
                                            <td><?php echo getCargoDisplayValue($pirep->cargo); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Departure Time:</td>
                                            <td><?php echo $pirep->departureTime; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Arrival Time:</td>
                                            <td><?php echo $pirep->arrivalTime; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Block Time:</td>
                                            <td><?php echo $pirep->duration; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Type:</td>
                                            <td><span
                                                    class='badge bg-blue-soft text-blue'><?php echo ucfirst($pirep->flightTypeDescription); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Landing Rate:</td>
                                            <td><?php echo empty($pirep->landingRate) ? 'NA' : $pirep->landingRate . 'fpm'; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Score:</td>
                                            <td><?php echo empty($pirep->score) ? 'NA' : $pirep->score; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">ACARS Recorded:</td>
                                            <td><?php echo empty($pirep->perfData) ? 'No' : 'Yes'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Landing Rate:</td>
                                            <td><?php echo empty($pirep->landingRate) ? 'NA' : $pirep->landingRate . 'fpm'; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">Edit Flight</div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="mb-1">Flight Number*:</label>
                                    <input name="flight_number" type="text" id="flight_number" class="form-control"
                                        value="<?php echo $pirep->flightNumber; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Departure ICAO*:</label>
                                    <input name="depicao" type="text" id="depicao" maxlength="4" class="form-control"
                                        value="<?php echo $pirep->departureIcao; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Arrival ICAO*:</label>
                                    <input name="arricao" type="text" id="arricao" maxlength="4" class="form-control"
                                        value="<?php echo $pirep->arrivalIcao; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Alternate ICAO:</label>
                                    <input name="alticao" type="text" id="alticao" maxlength="4" class="form-control"
                                        value="<?php echo $pirep->alternateIcao; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Route:</label>
                                    <textarea name="route" cols="50" rows="5" id="route" class="form-control"
                                        required><?php echo $pirep->route; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Aircraft*:</label>
                                    <input name="aircraft" type="text" id="aircraft" maxlength="25" class="form-control"
                                        value="<?php echo $pirep->aircraft; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Comments:</label>
                                    <textarea name="comments" rows="4" id="comments"
                                        class="form-control"><?php echo $pirep->comments; ?></textarea>
                                    <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Comments
                                        appear publically.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Review:</label>
                                    <input class="form-check-input" type="radio" name="status" id="status0" value="0"
                                        <?php echo $pirep->approvedStatus == 0 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status1">
                                        Pending
                                    </label>
                                    <input class="form-check-input" type="radio" name="status" id="status1" value="1"
                                        <?php echo $pirep->approvedStatus == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status1">
                                        Approved
                                    </label>
                                    <input class="form-check-input" type="radio" name="status" id="status2" value="2"
                                        <?php echo $pirep->approvedStatus == 2 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status2">
                                        Denied
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Review Comments:</label>
                                    <textarea name="adminComments" rows="4" id="adminComments"
                                        class="form-control"><?php echo $pirep->adminComments; ?></textarea>
                                    <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Comments
                                        will be emailed to the pilot and only
                                        visible to
                                        them on the report.</div>
                                </div>
                                <div class="mb-3">
                                    <div>
                                        <input type="hidden" name="currentStatus"
                                            value="<?php echo $pirep->approvedStatus; ?>" />
                                        <input type="hidden" name="pilotId" value="<?php echo $pirep->pilotId; ?>" />
                                        <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
                                            Changes</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>