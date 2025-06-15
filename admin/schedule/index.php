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

$flights = null;
$res = Api::sendSync('GET', 'v1/operations/schedule', null);
if ($res->getStatusCode() == 200) {
    $flights = json_decode($res->getBody(), true);
}

if (!empty($flights)) {
    foreach ($flights as &$flight) {
        $flight["depIcao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $flight["depIcao"] . '" target="_blank">' . $flight["depIcao"] . '</a>';
        $flight["arrIcao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $flight["arrIcao"] . '" target="_blank">' . $flight["arrIcao"] . '</a>';
        $flight["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'flight_info.php?id=' . $flight["id"] . '" title="View" target="_blank"><i data-feather="eye"></i></a>';
        $flight["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/schedule/edit.php?id=' . $flight["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $flight["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $flight["id"] . '"><i data-feather="trash-2"></i></a>';
    }
}

$id = null;
if (isset($_GET['id'])) {
    $id = cleanString($_GET['id']);
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}

if ($function == "delete_route") {
    Api::sendSync('DELETE', 'v1/operations/schedule/flight/' . $id, null);
    header('Location: index.php');
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
                            Schedule
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
                <table class="table-bordered" id="routes" style="display:none;">
                    <thead>
                        <tr>
                            <th>Flight Number</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Depart</th>
                            <th>Arrive</th>
                            <th>Duration</th>
                            <th>Operator</th>
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
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Route</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this route?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($flights); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_route&id=" + id);
        });
        $('#routes').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: 'flightNumber'
                },
                {
                    data: "depIcao"
                },
                {
                    data: "arrIcao"
                },
                {
                    data: "depTime"
                },
                {
                    data: "arrTime"
                },
                {
                    data: "duration"
                },
                {
                    data: "operator"
                },
                {
                    data: "aircraftTypeCommas"
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [0, 'desc'],
                [5, 'desc']
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