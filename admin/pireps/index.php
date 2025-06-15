<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(7)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$id = null;
if (isset($_GET['id'])) {
    $id = cleanString($_GET['id']);
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_pirep") {
    $res = Api::sendSync('DELETE', 'v1/operations/pirep/' . $id, null);
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
                            Manage Pilot Reports
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
                <table class="table-bordered" id="pireps" style="display:none;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Flight Number</th>
                            <th>Pilot ID</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Duration</th>
                            <th>Status</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete PIREP</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this PIREP?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_pirep&id=" + id);
        });
        $('#pireps').show()
        $('#pireps').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "<?php echo website_base_url; ?>includes/pireps_data.php",
                type: "POST",
            },
            "pageLength": 10,
            columns: [{
                    name: 'date',
                    data: 'date',
                    title: "Date",
                    sortable: true,
                },
                {
                    name: 'flightNumber',
                    data: 'flightNumber',
                    title: "Flight Number",
                    sortable: true,
                },
                {
                    name: 'callsign',
                    data: 'callsign',
                    title: "Pilot Id",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            return '<a href="/profile.php?id=' + row.pilotId +
                                '" target="_blank">' + data + '</a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: 'departureIcao',
                    data: 'departureIcao',
                    title: "Departure",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            return '<a href="/airport_info.php?airport=' + data +
                                '" target="_blank">' + data + '</a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: 'arrivalIcao',
                    data: 'arrivalIcao',
                    title: "Arrival",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            return '<a href="/airport_info.php?airport=' + data +
                                '" target="_blank">' + data + '</a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: 'duration',
                    data: 'duration',
                    title: "Duration",
                    sortable: false
                },
                {
                    name: 'approvedStatus',
                    data: 'approvedStatus',
                    title: "Approved Status",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            switch (data) {
                                case 0:
                                    return "<span class='badge bg-yellow-soft text-yellow'>Pending Approval</span>"
                                    break;
                                case 1:
                                    return "<span class='badge bg-green-soft text-green'>Approved</span>"
                                    break;
                                case 2:
                                    return "<span class='badge bg-red-soft text-red'>Denied</span>"
                                    break;
                            }
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: 'flightTypeDescription',
                    data: 'flightTypeDescription',
                    title: "Type",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            if (row.flightNumber != "TRX") { // hour transfer
                                switch (row.flightTypeDescription) {
                                    case "activity":
                                        return '<span class="badge bg-blue-soft text-blue">Tour/Event</span>';
                                        break;
                                    case "scheduled":
                                        return '<span class="badge bg-blue-soft text-blue">Scheduled</span>';
                                        break;
                                    default:
                                        return '<span class="badge bg-blue-soft text-blue">Charter</span>';
                                        break;
                                }
                            }
                            return '<span class="badge bg-blue-soft text-blue">Hour Transfer</span>'
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: "id",
                    data: "id",
                    title: "Actions",
                    sortable: false,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            buttons = "";
                            //exclude hour transfers
                            if (row.flightNumber != "TRX") {
                                buttons = buttons +
                                    '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="/pirep_info.php?id=' +
                                    data +
                                    '" title="View" target="_blank"><i data-feather="eye"></i></a>';
                                buttons = buttons +
                                    '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="edit.php?id=' +
                                    data + '" title="Edit"><i data-feather="edit"></i></a>';
                            }
                            buttons = buttons +
                                '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' +
                                data + '"><i data-feather="trash-2"></i></a>'
                            return buttons;
                        }
                    }
                }
            ],
            "order": [
                [0, 'desc'],
            ],
            "drawCallback": function(settings) {
                feather.replace();
            },
            "initComplete": function(settings, json) {},
        });
    });
</script>
<?php include '../includes/footer.php'; ?>