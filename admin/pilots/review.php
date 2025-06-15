<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/emailer.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(2)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$id = cleanString($_GET['id']);

$res = Api::sendSync('GET', 'v1/pilot/' . $id, null);
if ($res->getStatusCode() == 200) {
    $pilot = json_decode($res->getBody());
}

$airlineRes = getAirlineDetails();
if ($airlineRes->getStatusCode() != 200) {
    $status = "error";
} else {
    $airlineDetails = json_decode($airlineRes->getBody());
}

$hubs = null;
$res = Api::sendSync('GET', 'v1/operations/bases', null);
if ($res->getStatusCode() == 200) {
    $hubs = json_decode($res->getBody(), false);
}

$res = Api::sendAsync('GET', 'v1/account/registration/lastcallsignassigned', null);
$callsign = json_decode($res->getBody()->getContents());
$LastAssignedId = $callsign->result;
$status = null;
if ($pilot->activated == 1) {
    $status = "pilot_activated";
}
if (empty($status)) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPilot = $pilot->isNewApplication;
        $id = cleanString($_POST['id']);
        $callsign = $newPilot == true ? strtoupper(cleanString($_POST['callsign'])) : $pilot->callsign;
        $hub = $newPilot == true ? cleanString($_POST['hub']) : $pilot->hubId;
        $admincomments = cleanString($_POST['admincomments']);

        if ($newPilot) {
            $res = Api::sendSync('GET', 'v1/account/registration/callsignvalidandfree/' . $callsign, null);
            if ($res->getStatusCode() != 200) {
                $status = "callsign_exists";
            }
        }
        if (substr($callsign, 0, strlen($airlineDetails->icao)) != $airlineDetails->icao || $callsign == $airlineDetails->icao) {
            $status = "callsign_format";
        }

        if (empty($status)) {
            $data = [
                'PilotId' => $id,
                'Callsign' => $callsign,
                'HubId' => $hub,
                'AdminComments' => $admincomments
            ];
            $res = Api::sendSync('PUT', 'v1/account/registration', $data);
            $responseCode = $res->getStatusCode();

            if ($responseCode == 200) {
                $status = "success";
                $to = $pilot->email;
                if ($newPilot == true) {
                    //New pilot welcome email customisation here
                    $subject = "" . virtual_airline_name . " | Application Accepted";
                    $message = "Dear Pilot, <br /><br />
					Your application to " . $airlineDetails->name . " has been successfull.<br /><br />
					Pilot Id: $callsign<br />
					You can now <a href='" . website_base_url . "authentication/login.php'>login at our website</a> and begin flying now! If you have any issues and would like support about getting started, please contact us at " . airline_admin_email . ".<br /><br />
					Kind Regards,<br />
					" . virtual_airline_name . " Team.";
                } else {
                    //Unsuspended pilot welcome email customisation here
                    $subject = "" . virtual_airline_name . " | Account Unsuspended";
                    $message = "Dear Pilot, <br /><br />
					Your account has been unsuspended.<br /><br />
					Pilot Id: $callsign<br /><br />
					You are now welcome to <a href='" . website_base_url . "authentication/login.php'>login to our website</a> and continue flying. If you have any issues and would like support, please contact us at " . airline_admin_email . ".<br /><br />
					Kind Regards,<br />
					" . virtual_airline_name . " Team.";
                }
                echo sendEmail($subject, $message, $to);
                header('Location: ' . website_base_url . 'admin/pilots/applications.php');
                exit();
            } else {
                $status = "error";
            }
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
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            New/Suspended Pilots
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="applications.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Pilot List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="row">
            <?php if ($status == "success") { ?>
                <div class="alert alert-success alert-dismissible fade show">Pilot has been successfully updated.
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } else { ?>
                <?php if (!empty($status)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php
                        if ($status == 'error') {
                            echo 'An error occurred updating the pilot. Please try again later.';
                        }
                        if ($status == 'callsign_exists') {
                            echo 'Callsign already in use. Please use a different one.';
                        }
                        if ($status == 'callsign_format') {
                            echo 'Callsign format incorrect. Please prefix your callsigns with your airlines ICAO.';
                        }
                        if ($status == 'pilot_activated') {
                            echo 'The pilot has already been activated.';
                        }
                        ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">New Applicant Details</div>
                    <div class="card-body">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-12">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="text-dark">Application Date:</td>
                                            <td><?php echo date_format(new DateTime($pilot->joinDate), 'Y-m-d'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-grey">Name:</td>
                                            <td><?php echo $pilot->name; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Email Address:</td>
                                            <td><a
                                                    href="mailto:<?php echo $pilot->email; ?>"><?php echo $pilot->email; ?></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Location:</td>
                                            <td><img src="<?php echo website_base_url; ?>images/flags/<?php echo $pilot->location; ?>.gif"
                                                    width="20" height="20" /> <?php echo $pilot->location; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Vatsim ID:</td>
                                            <td><?php echo $pilot->vatsimId == "" ? "Not provided" : $pilot->vatsimId; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">IVAO ID:</td>
                                            <td><?php echo $pilot->ivaoId == "" ? "Not provided" : $pilot->ivaoId; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Custom Field 1:</td>
                                            <td><?php echo $pilot->custom1 == "" ? "Not provided" : $pilot->custom1; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Custom Field 2:</td>
                                            <td><?php echo $pilot->custom2 == "" ? "Not provided" : $pilot->custom2; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Custom Field 3:</td>
                                            <td><?php echo $pilot->custom3 == "" ? "Not provided" : $pilot->custom3; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Custom Field 4:</td>
                                            <td><?php echo $pilot->custom4 == "" ? "Not provided" : $pilot->custom4; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Custom Field 5:</td>
                                            <td><?php echo $pilot->custom5 == "" ? "Not provided" : $pilot->custom5; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark">Background:</td>
                                            <td><?php echo $pilot->background == "" ? "No background information supplied with application." : htmlspecialchars(strval($pilot->background)); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Approve Applicant</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="col-md-12">
                                <?php if ($pilot->isNewApplication == 1) { ?>
                                    <div class="mb-3">
                                        <label class="mb-1">Requested Hub:</label>
                                        <select name="hub" id="hub" class="form-select">
                                            <option value="" <?php echo !empty($pilot->hubId) ? "" : 'selected="true"' ?>>
                                                Unassigned
                                            </option>
                                            <?php
                                            foreach ($hubs as $hub) {
                                            ?>
                                                <option value="<?php echo $hub->id; ?>"
                                                    <?php echo $pilot->hubId != $hub->id ? "" : 'selected="true"' ?>>
                                                    <?php echo $hub->hub; ?> (<?php echo $hub->icao; ?>)
                                                </option>
                                            <?php
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="mb-1">Set Pilot Id*:</label>
                                        <input name="callsign" type="text" id="callsign"
                                            value="<?php echo $airlineDetails->icao; ?><?php echo is_int($LastAssignedId) ? str_replace($airlineDetails->icao, "", $LastAssignedId) + 1 : ""; ?>"
                                            maxlength="10" class="form-control" style="text-transform:uppercase" required />
                                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i>
                                            Last
                                            assigned pilot id:
                                            <strong><?php echo $LastAssignedId; ?></strong>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="mb-3">
                                        <label class="mb-1">Hub:</label>
                                        <?php echo $pilot->hub; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="mb-1">Pilot Id:</label>
                                        <?php echo $pilot->callsign; ?>
                                    </div>
                                <?php } ?>
                                <div class="mb-3">
                                    <label class="mb-1">Admin Comments (for admin use
                                        only):</label>
                                    <textarea name="admincomments" rows="5" id="admincomments"
                                        class="form-control"><?php echo htmlspecialchars(strval($pilot->adminComments)); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" id="submit"
                                        class="btn btn-primary"><?php echo $pilot->isNewApplication == 1 ? 'Approve Application' : 'Unsuspend Pilot' ?></button>
                                    <input name="id" type="hidden" id="id" value="<?php echo $pilot->id; ?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>