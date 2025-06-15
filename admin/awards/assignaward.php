<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/emailer.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(5)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_pilot_award") {
    $res = Api::sendAsync('POST', 'v1/award/removeassign/' . cleanString($_GET['assignedAwardId']), null);
}
$id = cleanString($_GET['id']);
$award = null;
$res = Api::sendAsync('GET', 'v1/award/' . $id, null);
if ($res->getStatusCode() == 200) {
    $award = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/awards');
    die();
}
$status = null;
$pilotId = null;
$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pilotId = cleanString($_POST['pilot']);


    if (empty($pilotId) || empty($id)) {
        $status = "required_fields";
    }

    if (empty($status)) {
        $data = [
            'pilotId' => $pilotId,
            'awardId' => $award->id
        ];

        $res = Api::sendSync('POST', 'v1/award/assign', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                $pilot = null;
                $res = Api::sendSync('GET', 'v1/pilot/' . $pilotId, null);
                if ($res->getStatusCode() == 200) {
                    $pilot = json_decode($res->getBody(), false);
                }
                if (!empty($pilot)) {
                    $subject = "" . virtual_airline_name . " | You have received a pilot award";
                    $to = $pilot->email;
                    $message = "
	Dear " . $pilot->name . ",<br /><br /> Congratulations, you have recieved a new award!<br /><br />

	You have been awarded with: <strong>" . $award->name . "</strong>
	<br /><br />
	Kind Regards,<br />
	" . virtual_airline_name . " Team.";

                    echo sendEmail($subject, $message, $to);
                    if (enable_discord_award_pilot_alerts) {
                        $data = [
                            'Message' => '**' . $pilot->callsign . '** has just been awarded the **' . $award->name . '** award.'
                        ];
                        $res = Api::sendSync('POST', 'v1/integrations/discord', $data);
                    }
                }
                $pilotId = null;
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
$awards = null;
$res = Api::sendSync('GET', 'v1/award/assigned/award/' . $id, null);
if ($res->getStatusCode() == 200) {
    $awards = json_decode($res->getBody(), true);
}
if (!empty($awards)) {
    foreach ($awards as &$awardItem) {
        $awardItem["imageUrl"] = '<img src="' . website_base_url . 'uploads/awards/' . $awardItem["imageUrl"] . '" width="35" />';
        $awardItem["dateAwarded"] = (new DateTime($awardItem["dateAwarded"]))->format('Y-m-d h:i:s');
        $awardItem["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $awardItem["assignedAwardId"] . '"><i data-feather="trash-2"></i></a>';
        $awardItem["pilotName"] = $awardItem["pilotName"] . ' (' . $awardItem["callsign"] . ')';
    }
}
$pilotUserList = null;
$res = Api::sendSync('GET', 'v1/pilots', null);
if ($res->getStatusCode() == 200) {
    $pilotUserList = json_decode($res->getBody(), false);
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
                            <div class="page-header-icon"><i data-feather="award"></i></div>
                            Assign Award - <?php echo $award->name; ?>
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Awards
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="row gx-5">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">Pilots Assigned Award</div>
                    <div class="card-body">
                        <table class="table-bordered" id="awards">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</strong></th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Assign Award</div>
                    <div class="card-body">
                        <?php if ($status == "success") { ?>
                        <div class="alert alert-success alert-dismissible fade show">Pilot successfully awarded.
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php } else { ?>
                        <?php if (!empty($status)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
                                    if ($status == 'error') {
                                        if (!empty($responseMessage)) {
                                            echo $responseMessage;
                                        } else {
                                            echo 'An error occurred when assigning the award. Please try again later.';
                                        }
                                    }
                                    if ($status == 'required_fields') {
                                        echo 'Please check all fields have been completed correctly.';
                                    }
                                    ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        <form method="post" class="form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="mb-1">Pilot</label>
                                <select name="pilot" id="pilot" class="form-select" required>
                                    <option value="" selected="true">
                                        Select Pilot
                                    </option>
                                    <?php
                                    if (!empty($pilotUserList)) {
                                        foreach ($pilotUserList as $user) {
                                    ?>
                                    <option value="<?php echo $user->id; ?>"
                                        <?php echo $user->id != $pilotId ? "" : 'selected="true"' ?>>
                                        <?php echo $user->name; ?> (<?php echo $user->callsign; ?>)
                                    </option>
                                    <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class=" text-right">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary">Assign
                                        Award</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Unassign Pilot Award</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to remove this award from the pilot?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
var dataSet = <?php echo json_encode($awards); ?>;
$(document).ready(function() {
    $('#deleteModal').on('show.bs.modal', function(event) {
        var id = $(event.relatedTarget).data('id');
        var awardId = <?php echo $id; ?>;
        $(this).find('.btn-danger').attr("href", "?function=delete_pilot_award&id=" + awardId +
            "&assignedAwardId=" + id);
    });
    $('#awards').DataTable({
        data: dataSet,
        "pageLength": 25,
        columns: [{
                data: "imageUrl",
                "orderable": false
            },
            {
                data: "pilotName",
            },
            {
                data: "dateAwarded",
            },
            {
                data: "buttons",
                "orderable": false
            }
        ],
        colReorder: false,
        "order": [
            [3, 'desc']
        ],
        "drawCallback": function(settings) {
            feather.replace();
        }
    });
});
</script>
<?php include '../includes/footer.php'; ?>