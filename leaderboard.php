<?php
use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
session_start();
validateSession();

$config = null;
$res = Api::sendSync('GET', 'v1/stats/pirepyears', null);
if ($res->getStatusCode() == 200) {
    $config = json_decode($res->getBody(), true);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.hero-section {
    position: relative;
    min-height: calc( 100vh - 128px);
    padding: 80px 0;
    background-image: url('./assets/images/backgrounds/leaderboard.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2); /* 20% black overlay */
    z-index: 1;
}

.hero-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}

/* -------------------------------- Global title ------------------------------------ */
.global-heading {
    width: 100%;
    margin-bottom: 20px;
}
.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0px !important;
    text-transform: lowercase;
}
@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
    }
}
/* -------------------------------- X ------------------------------------ */

.leaderboard-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
}

.leaderboard-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.leaderboard-header {
    padding: 20px;
    text-align: center;
    border-bottom: 2px solid rgba(255, 215, 0, 1);;
}

.leaderboard-header .form-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.leaderboard-header .form-control {
    height: 40px;
    padding: 10px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-size: 14px;
    width: 120px;
}

.leaderboard-header .form-control:focus {
    outline: none;
    border-color: rgba(255, 215, 0, 0.8);
    background: rgba(255, 255, 255, 0.3);
}

.leaderboard-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.leaderboard-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.leaderboard-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.leaderboard-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.leaderboard-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}

.leaderboard-table {
    width: 100%;
    margin: 0;
    background: transparent;
    color: rgba(0, 0, 0, 0.9);
}

.leaderboard-table thead th {
    color: rgba(0, 0, 0, 1);
    font-weight: 600;
    border: none !important;
    padding: 15px 12px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
    text-wrap: nowrap;
    background: rgba(255, 255, 255, 0.9);
}

.leaderboard-table tbody tr {
    background: rgba(255, 255, 255, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
    transition: all 0.3s ease;
}

.leaderboard-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95) !important;
    transform: scale(1.01);
}

.leaderboard-table tbody td {
    padding: 15px 12px;
    border: none !important;
    vertical-align: middle;
    font-size: 14px;
    color: rgba(0, 0, 0, 0.9);
}

.leaderboard-table tbody td:first-child .golden {
    color: #F0CC00;
    font-size: 30px;
}

.leaderboard-table tbody tr:last-child {
    border-bottom: none !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: rgba(0, 0, 0, 0.9);
    padding: 10px;
    margin: 0;
}

.dataTables_wrapper .dataTables_length {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_filter {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_info {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_paginate {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_filter input {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 6px;
    padding: 6px 10px;
    color: rgba(0, 0, 0, 0.9);
}

.paginate_button {
    color: #333 !important;
    border-radius: 4px !important;
    margin: 0 2px !important;
}

.paginate_button:hover {
    background: rgba(255, 215, 0, 1) !important;
    color: #222 !important;
}

.paginate_button.current {
    background: rgba(255, 193, 7, 1) !important;
    color: #222 !important;
}

.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #333;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
}

@media (max-width: 1200px) {
    .hero-section {
        padding: 80px 0;
    }
    .leaderboard-table thead th,
    .leaderboard-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .hero-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 80px;
    }
    .leaderboard-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    .leaderboard-table-wrapper {
        max-height: 500px;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .leaderboard-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    .leaderboard-header .form-group {
        flex-direction: column;
        gap: 10px;
    }
    .leaderboard-header .form-control {
        width: 100%;
    }
    .leaderboard-table-wrapper {
        max-height: 400px;
    }
    .leaderboard-table {
        font-size: 12px;
    }
    .leaderboard-table thead th {
        padding: 12px 8px;
        font-size: 11px;
    }
    .leaderboard-table tbody td {
        padding: 12px 8px;
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .hero-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 50px;
    }
    .leaderboard-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    .leaderboard-table-wrapper {
        max-height: 350px;
    }
    .leaderboard-table thead th,
    .leaderboard-table tbody td {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 40px;
    }
    .leaderboard-table-wrapper {
        max-height: 300px;
    }
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
}
</style>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.css" />
<section id="content" class="section hero-section">
    <div class="container">
        <div class="global-heading">
            <h3 class="global-title">Leaderboard (<span class="month-name"><?php echo date('F'); ?></span>)</h3>
        </div>
        <div class="leaderboard-glass-card">
            <div class="leaderboard-header">
                <div class="form-group" style="margin-bottom: 0; display: flex; justify-content: between; align-items: center; gap: 10px">
                    <p>Filter by month & year</p>
                    <div style="display: flex; flex-direction: row; gap: 10px; flex-wrap: wrap">

                        <div class="">
                            <select style="color: #000" id="year" class="form-control" onchange="selectionChanged();">
                                <?php
                            foreach ($config as $year) {
                                ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="">
                        <select style="color: #000" id="month" class="form-control" onchange="selectionChanged();">
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
                    </div>
                </div>
                </div>
            </div>
            <div class="leaderboard-table-wrapper">
                <table class="table table-striped roster leaderboard-table" id="pom">
                    <thead>
                        <tr>
                            <th> </th>
                            <th>Callsign</th>
                            <th>Name</th>
                            <th>Month Hours</th>
                            <th>Flights</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script type="text/javascript" src="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.js"></script>
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
        table = $('#pom').DataTable({
            "processing": true,
            serverSide: false,
            ajax: {
                url: "<?php echo website_base_url; ?>includes/pilot_month_hours.php?year=" + year + "&month=" + month,
                dataSrc: '',
            },
            "pageLength": 10,
            columns: [{
                    name: 'badge',
                    data: "badge",
                    sortable: false,
                    render: function(data) {
                        return data ? '<span class="golden">' + data + '</span>' : '';
                    }
                },
                {
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
                    name: "flights",
                    data: 'flights',
                    sortable: false,
                }
            ],
            colReorder: false,
            "ordering": false,
            "order": [
                [3, 'desc']
            ]
        });
    }

    $(document).ready(function() {
        $("#month").val(currentMonth);
        loadTable();
    });
</script>