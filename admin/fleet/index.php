<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(1)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$function = null;
$id = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if (isset($_GET['id'])) {
    $id = cleanString($_GET['id']);
}
if ($function == "delete_aircraft") {
    Api::sendAsync('DELETE', 'v1/operations/aircraft/' . $id, null);
}
$fleet = null;
$res = Api::sendSync('GET', 'v1/operations/fleet', null);
if ($res->getStatusCode() == 200) {
    $fleet = json_decode($res->getBody(), true);
}
if (!empty($fleet)) {
    foreach ($fleet as &$aircraft) {
        $aircraft["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'fleet_info.php?id=' . $aircraft["id"] . '" title="View" target="_blank"><i data-feather="eye"></i></a>';
        $aircraft["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/fleet/edit.php?id=' . $aircraft["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $aircraft["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $aircraft["id"] . '"><i data-feather="trash-2"></i></a>';
        $aircraft["cargo"] = getCargoDisplayValue($aircraft["cargo"]);
        $aircraft["maxRange"] = empty($aircraft["maxRange"]) ? "NA" : number_format($aircraft["maxRange"]) . 'nm';
        $aircraft["speed"] = empty($aircraft["speed"]) ? "NA" : number_format($aircraft["speed"]) . 'kt';
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
                            <div class="page-header-icon"><i data-feather="send"></i></div>
                            Manage Fleet
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
                <table class="table-bordered" id="fleet" style="display:none;">
                    <thead>
                        <tr>
                            <th>ICAO</th>
                            <th>Name</th>
                            <th>PAX</th>
                            <th>Crew</th>
                            <th>Cargo</th>
                            <th>Range</th>
                            <th>Max Speed</th>
                            <th>Total</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Aircraft</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this aircraft?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($fleet); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_aircraft&id=" + id);
        });
        $('#fleet').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "icao",
                },
                {
                    data: "description",
                },
                {
                    data: "pax",
                },
                {
                    data: "totalCrew",
                },
                {
                    data: "cargo",
                },
                {
                    data: "maxRange",
                },
                {
                    data: "speed",
                },
                {
                    data: "totalInFleet",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: true,
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