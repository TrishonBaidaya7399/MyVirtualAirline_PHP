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

$function = null;
$id = null;

if (isset($_GET['function'])) {
    $function =  cleanString($_GET['function']);
}

if (isset($_GET['id'])) {
    $id = cleanString($_GET['id']);
}

if ($function == "delete_pilot") {
    Api::sendAsync('DELETE', 'v1/pilot/' . cleanString($_GET['id']), null);
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
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            Manage Pilots
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
                <table id="pilots" class="table-bordered" style="display:none;">
                    <thead>
                        <tr>
                            <th>Callsign</th>
                            <th>Name</th>
                            <th>Rank</th>
                            <th>Join Date</th>
                            <th>Hours</th>
                            <th>Last 30 Days</th>
                            <th>Virtual Location</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Pilot</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this pilot?</div>
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
            $(this).find('.btn-danger').attr("href", "?function=delete_pilot&id=" + id);
        });
        $('#pilots').show()
        $('#pilots').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo website_base_url; ?>includes/roster_data.php",
                type: "POST",
            },
            "pageLength": 10,
            "scrollX": true,
            columns: [{
                    name: 'callsign',
                    data: 'callsign',
                    sortable: true,
                },
                {
                    name: 'name',
                    data: "name",
                    sortable: true,
                },
                {
                    name: 'rankName',
                    data: "rankName",
                    sortable: false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    name: 'joinDate',
                    data: 'joinDate',
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            return row.joinDateString
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: "totalHours",
                    data: 'totalHours',
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'sort') {
                            return row.hours
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: 'thirtyDayHours',
                    sortable: false,
                    render: function(data, type, row, meta) {
                        if (type == 'sort') {
                            return row.thirtyDaySeconds
                        } else {
                            return '<span' + (data == "00:00" ? ' style="color:red;">' :
                                '>') + data + '</span>';
                        }
                    }
                },
                {
                    data: "currentLocation",
                    sortable: false,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            return '<span class="badge bg-blue-soft text-blue">' + data + '</span>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: "status",
                    data: "status",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            if (data == 0) {
                                return '<span class="badge bg-yellow-soft text-yellow">On Leave</span>';
                            } else {
                                return '<span class="badge bg-green-soft text-green">Active</span>';
                            }
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: "id",
                    sortable: false,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            buttons = "";
                            buttons = buttons +
                                '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="/profile.php?id=' +
                                data +
                                '" title="Profile" target="_blank"><i data-feather="eye"></i></a>';
                            buttons = buttons +
                                '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="edit.php?id=' +
                                data + '" title="Edit"><i data-feather="edit"></i></a>';
                            buttons = buttons +
                                '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="credits.php?id=' +
                                data + '" title="Credits"><i data-feather="credit-card"></i></a>';
                            if (row.owner != 1) {
                                buttons = buttons +
                                    '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' +
                                    data + '"><i data-feather="trash-2"></i></a>';
                            }

                            return buttons;
                        }
                    }
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

            },
        });
    });
</script>
<?php include '../includes/footer.php'; ?>