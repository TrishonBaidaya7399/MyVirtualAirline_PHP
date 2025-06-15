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

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_activity") {
    Api::sendAsync('DELETE', 'v1/activity/' . cleanString($_GET['id']), null);
}

$activities = null;
$res = Api::sendSync('GET', 'v1/activities', null);
if ($res->getStatusCode() == 200) {
    $activities = json_decode($res->getBody(), true);
}
if (!empty($activities)) {
    foreach ($activities as &$activity) {

        $activity["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'activity.php?id=' . $activity["id"] . '" title="View" target="_blank"><i data-feather="eye"></i></a>';
        $activity["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/activity/edit.php?id=' . $activity["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $activity["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" data-bs-toggle="modal" data-bs-target="#deleteModal" href="#" data-id="' . $activity["id"] . '" title="Delete"><i data-feather="trash-2"></i></a>';
        $activity["active"] = $activity["active"] == "true" ? "Yes" : "No";

        if ($activity['type'] == "Tour") {
            $activity['type'] = "<span class='badge bg-orange-soft text-orange'>Tour</span>";
        } else {
            $activity['type'] = "<span class='badge bg-purple-soft text-purple'>Event</span>";
        }

        if (!empty($activity["endDate"])) {
            $today = date("Y-m-d h:i:s");
            $expires = (new DateTime($activity["endDate"]))->format('Y-m-d h:i:s');
            $activity["endDateDisplay"] = $expires < $today ? "Ended" : $expires;
        } else {
            $activity["endDateDisplay"] = "N/A";
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
                            Manage Tours/Events
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="create.php">
                            <i class="me-1" data-feather="plus"></i>
                            Create New
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card">
            <div class="card-body">
                <table class="table-bordered" id="activities" style="display:none;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Active</th>
                            <th>End Date</th>
                            <th>Pilots Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Tour/Event</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this tour/event?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($activities); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_activity&id=" + id);
        });
        $('#activities').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "title",
                },
                {
                    data: "type",
                },
                {
                    data: "active",
                },
                {
                    data: "endDateDisplay",
                },
                {
                    data: "totalPilotsComplete",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [0, 'desc'],
                [1, 'desc'],
                [2, 'desc']
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