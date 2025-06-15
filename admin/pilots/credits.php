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
$pilot = json_decode($res->getBody());
$responseCode = $res->getStatusCode();
if ($responseCode != 200) {
    echo '<script>alert("Pilot does not exist.");</script>';
    echo '<script>history.back(1);</script>';
    exit;
}
$responseMessage = null;
$status = "";
$hours = null;
$xp = null;
$cash = null;
$comments = null;
if (isset($_POST['submit'])) {
    $hours = addslashes(trim($_POST['hours']));
    $xp = addslashes(trim($_POST['xp']));
    $cash = addslashes(trim($_POST['cash']));
    $comments = addslashes(trim($_POST['comments']));

    if (empty($hours) && empty($cash) && empty($xp)) {
        $status = "required_fields";
    }
    if (empty($status)) {
        $data = [
            'PilotId' => $id,
            'Hours' => intval($hours) ?? null,
            'Xp' => intval($xp) ?? null,
            'Cash' => floatval($cash) ?? null,
        ];
        $res = Api::sendSync('POST', 'v1/pilot/credits/', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                //Email pilot credit information
                $subject = "" . virtual_airline_name . " | Credit Notification";
                $message = 'Dear Pilot, <br /><br />
			A credit has been applied to your pilot account:<br /><br />
			Hours: ' . (empty($hours) ? "N/A" : $hours) . '<br />
			Cash: ' . (empty($cash) ? "N/A" : $cash) . '<br />
			XP: ' . (empty($xp) ? "N/A" : $xp) . '<br /><br />
			Reason: ' . $comments . '<br /><br />
			If you have a query about this credit please contact us at ' . airline_admin_email . '.<br /><br />
			Kind Regards,<br />
			' . virtual_airline_name . ' Team.';
                echo sendEmail($subject, $message, $pilot->email);
                $xp = "";
                $hours = "";
                $cash = "";
                $comments = "";
                break;
            case 400:
                $status = "error";
                $responseMessage = $res->getBody();
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
                            <div class="page-header-icon"><i data-feather="credit-card"></i></div>
                            Credits
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to PIlots
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Pilot Credits - <?php echo $pilot->name; ?> (<?php echo $pilot->callsign; ?>)</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Credits have been applied successfully.
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } else { ?>
                    <?php if (!empty($status)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
                            if ($status == 'error') {
                                if (!empty($responseMessage)) {
                                    echo $responseMessage . 'lkjkljl';
                                } else {
                                    echo 'An error has occured when applying credits.';
                                }
                            }
                            if ($status == 'required_fields') {
                                echo 'Please check at least one credit has been applied.';
                            }
                            ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                <?php } ?>
                <form method="post" class="form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="mb-1">Hours</label>
                        <input name="hours" type="number" id="hours" class="form-control" value="<?php echo $hours; ?>">
                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Add or subtract pilot
                            hours. You can use negative numbers
                            to remove hours. Enter whole hours only.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">XP</label>
                        <input name="xp" type="number" id="xp" class="form-control" value="<?php echo $xp; ?>">
                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Add or subtract pilot
                            XP. You can use negative numbers to
                            remove XP.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Cash</label>
                        <input name="cash" type="number" id="cash" class="form-control" value="<?php echo $cash; ?>">
                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Add or subtract cash
                            to a pilots wallets. You can use
                            negative numbers to remove cash.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Comments (emailed to pilot)</label>
                        <textarea name="comments" rows="5" id="comments"
                            class="form-control"><?php echo htmlspecialchars(strval($comments)) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <div class=" text-right">
                            <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
                                Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>