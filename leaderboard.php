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
    .golden {
        color: #F0CC00 !important;
        font-size: 30px !important;
    }
</style>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css"
    href="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.css" />
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Leaderboard (<span class="month-name"><?php echo date('F'); ?></span>)</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="col-md-2">
                        <select id="year" class="form-control" onchange="selectionChanged();">
                            <?php
                            foreach ($config as $year) {
                            ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="month" class="form-control" onchange="selectionChanged();">
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
                    <div class="col-md-8">
                    </div>
                </div>
                <br />
                <br />
                <table class="table table-striped roster" id="pom">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
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
                url: "<?php echo website_base_url; ?>includes/pilot_month_hours.php?year=" + year + "&month=" +
                    month,
                dataSrc: '',
            },
            "pageLength": 10,
            columns: [{
                    name: 'badge',
                    data: "badge",
                    sortable: false,
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