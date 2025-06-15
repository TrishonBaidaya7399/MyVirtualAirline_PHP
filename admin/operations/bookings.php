<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(10)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$bids = null;
$res = Api::sendSync('GET', 'v1/bids', null);
if ($res->getStatusCode() == 200) {
    $bids = json_decode($res->getBody(), true);
}

if (!empty($bids)) {
    foreach ($bids as &$bid) {
        $bid["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $bid["id"] . '"><i data-feather="trash-2"></i></a>';
        if ($bid['bidType'] == "activity") {
            $bid['bidType'] = "<span class='badge bg-blue-soft text-blue'>Tour/Event</span>";
            $bid["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'activity_leg.php?id=' . $bid['activityLegId'] . '" title="View" target="_blank"><i data-feather="eye"></i></a>';
        } elseif ($bid['bidType'] == "scheduled") {
            $bid['bidType'] = "<span class='badge bg-blue-soft text-blue'>Scheduled</span>";
            $bid["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'flight_info.php?id=' . $bid['scheduleId'] . '" title="View" target="_blank"><i data-feather="eye"></i></a>';
        } else {
            $bid['bidType'] = "<span class='badge bg-blue-soft text-blue'>Charter</span>";
        }
        $bid["sbDispatched"] = $bid["sbDispatched"] ? "Yes" : "No";
        $bid["status"] = $bid["status"] < 1 ? "<span class='badge bg-yellow-soft text-yellow'>Booked</span>" : "<span class='badge bg-green-soft text-green'>In Progress</span>";
        $bid["departureIcao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $bid['departureIcao'] . '" target="_blank">' . $bid['departureIcao'] . '</a>';
        $bid["arrivalIcao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $bid['arrivalIcao'] . '" target="_blank">' . $bid['arrivalIcao'] . '</a>';
        $bid["callsign"] = '<a href="' . website_base_url . 'profile.php?id=' . $bid['pilotId'] . '" target="_blank">' . $bid['callsign'] . '</a>';
        $bid["dateBooked"] = (new DateTime($bid["dateBooked"]))->format('d M Y H:i');
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
if ($function == "delete_booking") {
    $res = Api::sendSync('DELETE', 'v1/bid/delete/' . $id, null);
    header('Location: bookings.php');
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
                            Active Bookings
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card">
            <div class="card-body">
                <table class="table-bordered" id="bookings" style="display:none;">
                    <thead>
                        <tr>
                            <th>Pilot ID</th>
                            <th>Flight No.</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>PAX</th>
                            <th>Cargo</th>
                            <th>Aircraft</th>
                            <th>Date Booked</th>
                            <th>Type</th>
                            <th>Simbrief</th>
                            <th>Status</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Booking</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this booking?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($bids); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_booking&id=" + id);
        });
        $('#bookings').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "callsign",
                },
                {
                    data: "flightNumber",
                },
                {
                    data: "departureIcao",
                },
                {
                    data: "arrivalIcao"
                },
                {
                    data: "totalPax",
                },
                {
                    data: "cargo",
                    "orderable": false
                },
                {
                    data: "aircraft",
                },
                {
                    data: "dateBooked",
                },
                {
                    data: "bidType",
                },
                {
                    data: "sbDispatched",
                },
                {
                    data: "status",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [6, 'desc'],
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