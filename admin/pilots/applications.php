<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(2)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$pendingPilots = null;
$suspendedPilots = null;
$res = Api::sendAsync('GET', 'v1/account/registration/pendingpilotlist', null);
$responseCode = $res->getStatusCode();
if ($responseCode == 200) {
    $pendingPilots = json_decode($res->getBody(), true);
    $suspendedPilots = $pendingPilots;
}

if (!empty($pendingPilots)) {
    foreach ($pendingPilots as $key => &$pilot) {

        if ($pilot['isNewApplication'] == true) {

            $pilot["type"] = "<span class='badge bg-green-soft text-green'>New Applicant</span>";
        } else {
            unset($pendingPilots[$key]);
            continue;
        }
        $pilot["joinDateOrig"] = $pilot["joinDate"];
        $pilot["location"] = '<img src="' . website_base_url . 'images/flags/' . $pilot["location"] . '.gif" width="20" height="20"/> ' . $pilot["location"];
        $pilot["hub"] = '<span class="badge bg-blue-soft text-blue">' . $pilot["hub"] . '</span>';
        $pilot["joinDate"] = (new DateTime($pilot["joinDate"]))->format('d M Y');
        $pilot["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/pilots/review.php?id=' . $pilot['pilotId'] . '" title="Review"><i data-feather="edit"></i></a>';

        $pilot["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete Application" data-id="' . $pilot['pilotId'] . '"><i data-feather="trash-2"></i></a>';
    }
}

if (!empty($suspendedPilots)) {
    foreach ($suspendedPilots as $key => &$pilot) {

        if ($pilot['isNewApplication'] == false) {

            $pilot["type"] = "<span class='badge bg-red-soft text-red'>Suspended</span>";
        } else {
            unset($suspendedPilots[$key]);
            continue;
        }
        $pilot["joinDateOrig"] = $pilot["joinDate"];
        $pilot["location"] = '<img src="' . website_base_url . 'images/flags/' . $pilot["location"] . '.gif" width="20" height="20"/> ' . $pilot["location"];
        $pilot["hub"] = '<span class="badge bg-blue-soft text-blue">' . $pilot["hub"] . '</span>';
        $pilot["joinDate"] = (new DateTime($pilot["joinDate"]))->format('d M Y');
        $pilot["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/pilots/review.php?id=' . $pilot['pilotId'] . '" title="Review"><i data-feather="edit"></i></a>';
        $pilot["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $pilot['pilotId'] . '"><i data-feather="trash-2"></i></a>';
        $pilot["callsign"] = '<a href="' . website_base_url . 'profile.php?id=' . $pilot['pilotId'] . '" target="_blank">' . $pilot['callsign'] . '</a>';
    }
}

$function = null;
$id = null;

if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if (isset($_GET['id'])) {
    $id = cleanString($_GET['id']);
}
if ($function == "delete_pilot") {
    Api::sendAsync('DELETE', 'v1/pilot/' . $id, null);
    header('Location: applications.php');
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

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="row gx-4">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header">New Applicants</div>
                    <div class="card-body">
                        <table id="applicants" class="table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Application Date</th>
                                    <th>Location</th>
                                    <th>Requested Base</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">Suspended Pilots</div>
                    <div class="card-body">
                        <table id="suspended" class="table-bordered">
                            <thead>
                                <tr>
                                    <th>Pilot Id</th>
                                    <th>Name</th>
                                    <th>Application Date</th>
                                    <th>Location</th>
                                    <th>Base</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete User</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this user?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
$('#deleteModal').on('show.bs.modal', function(event) {
    var id = $(event.relatedTarget).data('id');
    $(this).find('.btn-danger').attr("href", "?function=delete_pilot&id=" + id);
});
var dataSet = <?php echo !empty($pendingPilots) ? json_encode(array_values($pendingPilots)) : '""'; ?>;
var dataSetTwo = <?php echo $suspendedPilots != null ? json_encode(array_values($suspendedPilots)) : '""'; ?>;
$(document).ready(function() {
    $('#applicants').DataTable({
        data: dataSet,
        "pageLength": 10,
        "scrollX": true,
        columns: [{
                data: "name",
            },
            {
                data: 'joinDate',
                render: function(data, type, row, meta) {
                    if (type == 'sort') {
                        return row.joinDateOrig
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "location",
                "orderable": false
            },
            {
                data: "hub",
            },
            {
                data: "type",
                "orderable": false
            },
            {
                data: "buttons",
                "orderable": false
            }
        ],
        colReorder: false,
        "order": [
            [0, 'asc']
        ],
        "drawCallback": function(settings) {
            feather.replace();
        },
        "initComplete": function(settings, json) {
            $(this).show()
        },
    });
    $('#suspended').DataTable({
        data: dataSetTwo,
        "pageLength": 10,
        "scrollX": true,
        columns: [{
                data: "callsign",
            },
            {
                data: "name",
            },
            {
                data: 'joinDate',
                render: function(data, type, row, meta) {
                    if (type == 'sort') {
                        return row.joinDateOrig
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "location",
                "orderable": false
            },
            {
                data: "hub",
            },
            {
                data: "type",
                "orderable": false
            },
            {
                data: "buttons",
                "orderable": false
            }
        ],
        colReorder: false,
        "order": [
            [0, 'asc']
        ],
        "drawCallback": function(settings) {
            feather.replace();
        },
        "initComplete": function(settings, json) {
            $(this).show()
        },
    });
});
</script>
<?php include '../includes/footer.php'; ?>