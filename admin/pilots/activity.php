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

$config = null;
$res = Api::sendSync('GET', 'v1/stats/pirepyears', null);
if ($res->getStatusCode() == 200) {
    $config = json_decode($res->getBody(), true);
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
                            Pilot Activity (<span class="month-name"><?php echo date('F'); ?></span>)
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
                <select id="year" class="form-select w-25 d-inline" onchange="selectionChanged();">
                    <?php
                    foreach ($config as $year) {
                    ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php
                    } ?>
                </select>
                <select id="month" class="form-select w-25 d-inline" onchange="selectionChanged();">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <table id="pilots" class="table-bordered" style="display:none;">
                    <thead>
                        <tr>
                            <th>Callsign</th>
                            <th>Name</th>
                            <th>Month Hours</th>
                            <th>Flights</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var table = null;
    var currentMonth = <?php echo date('n') ?>;
    var year = null;
    var month = null;

    function selectionChanged() {
        $(".month-name").html($("#month option:selected").text());
        table.destroy();
        loadTable();
    }

    function loadTable() {
        year = $("#year").val();
        month = $("#month").val();
        $('#pilots').show()
        table = $('#pilots').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "<?php echo website_base_url; ?>includes/pilot_month_hours.php?year=" + year +
                    "&month=" + month,
                type: "GET",
                dataSrc: ''
            },
            "pageLength": 10,
            "scrollX": true,
            columns: [{
                    name: 'callsign',
                    data: 'callsign',
                    sortable: false,
                },
                {
                    name: 'name',
                    data: "name",
                    sortable: false,
                },
                {
                    name: "monthHours",
                    data: 'monthHours',
                    sortable: false,
                },
                {
                    data: "flights",
                    sortable: false,
                },
                {
                    name: "id",
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
                            return buttons;
                        } else {
                            return data;
                        }
                    }
                }
            ],
            colReorder: false,
            "order": [
                [2, 'desc']
            ],
            "drawCallback": function(settings) {
                feather.replace();
            },
            "initComplete": function(settings, json) {

            },
        });
    }

    $(document).ready(function() {
        $("#month").val(currentMonth);
        loadTable();
    });
</script>
<?php include '../includes/footer.php'; ?>