<?php

use Proxy\Api\Api;

include 'config.php';
include 'lib/functions.php';
session_start();
Api::__constructStatic();
if (isset($_SESSION['pilotid'])) {
    $pilot = null;
    $res = Api::sendAsync('GET', 'v1/pilot/location', null);
    if ($res->getStatusCode() == 200) {
        $pilot = json_decode($res->getBody());
    }
}

?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="assets/plugins/datatables/datatables.min.css" />
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="jumbotron text-center">
            <h1>Advanced Schedule</h1>
            <hr />
            <p><i class="fa fa-search" aria-hidden="true"></i> Use the filters below to search all
                flight schedules.<br />Alternatively, you can search for flights on the <a href="route_map.php">route
                    map</a>.
            </p>
            <hr />
            <div class="row text-left">
                <div class="form-group row col-md-6">
                    <label class="col-form-label col-sm-4">Flight Number</label>
                    <div class="col-sm-8">
                        <span id="fn"><i class="fa fa-circle-o-notch fa-spin"></i> Loading
                            filter</span>
                    </div>
                </div>
                <div class="form-group row col-md-6">
                    <label class="col-form-label col-sm-4">Departure ICAO</label>
                    <div class="col-sm-8">
                        <span id="di"><i class="fa fa-circle-o-notch fa-spin"></i> Loading
                            filter</span>
                    </div>
                </div>
                <div class="form-group row col-md-6">
                    <label class="col-form-label col-sm-4">Arrival ICAO</label>
                    <div class="col-sm-8">
                        <span id="ai"><i class="fa fa-circle-o-notch fa-spin"></i> Loading
                            filter</span>
                    </div>
                </div>
                <div class="form-group row col-md-6">
                    <label class="col-form-label col-sm-4">Aircraft</label>
                    <div class="col-sm-8">
                        <span id="ac"><i class="fa fa-circle-o-notch fa-spin"></i> Loading
                            filter</span>
                    </div>
                </div>
                <div class="form-group row col-md-6">
                    <label class="col-form-label col-sm-4">Operator</label>
                    <div class="col-sm-8">
                        <span id="op"><i class="fa fa-circle-o-notch fa-spin"></i> Loading
                            filter</span>
                    </div>
                </div>
                <div class="form-group row col-md-6">
                    <div class="col-sm-12">
                        <i class="fa fa-map-marker" aria-hidden="true"></i> Virtual Location:
                        <strong><?php echo empty($pilot->location) ? "N/A" : '<a href="#"><i class="fa fa-external-link"></i> <span class="curlocation">' . $pilot->location . '</span></a>'; ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Flight Schedule</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                        </div>
                    </div>
                    <table class="table table-striped" id="flights" width="100%">
                        <thead>
                            <tr>
                                <th>Flight<br />Number</th>
                                <th>Departure<br />ICAO</th>
                                <th>Arrival<br />ICAO</th>
                                <th>Depart<br />UTC</th>
                                <th>Arrive<br />UTC</th>
                                <th>Duration</th>
                                <th>Operator</th>
                                <th>Aircraft</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script type="text/javascript" src="assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.curlocation').on('click', function(e) {
            e.preventDefault();
            $("#di").find('input:text').val($(this).html());
            $("#di").find('input:text').trigger("change");
        });
        $('#flights').DataTable({
            initComplete: function() {
                this.api().columns([7]).every(function(e) {
                    var column = this;
                    $("#ac").html('');
                    var select = $(
                            '<select class="form-control" id="aclist"><option value="">Select aircraft</option></select>'
                        )
                        .appendTo($("#ac"))
                        .on('change click', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val ? '' + val + '' : '', true, false)
                                .draw();
                        });
                    column.data().unique().sort().each(function(d, j) {

                        var nameArr = d.split(",");
                        nameArr.forEach(function(number) {
                            var optionExists = ($("#aclist option[value='" +
                                number.trim() + "']").length > 0);
                            if (!optionExists) {
                                select.append('<option value="' + number
                                    .trim() +
                                    '">' + number.trim() + '</option>');
                            }
                        });
                    });
                });
                this.api().columns([6]).every(function(e) {
                    var column = this;
                    $("#op").html('');
                    var select = $(
                            '<select class="form-control"><option value="">Select operator</option></select>'
                        )
                        .appendTo($("#op"))
                        .on('change click', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });
                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d +
                            '</option>')
                    });
                });
                this.api().columns([0]).every(function(e) {
                    var column = this;
                    $("#fn").html('');
                    var text = $(
                            '<input type="text" placeholder="" class="form-control" />')
                        .appendTo($("#fn"))
                        .on('keyup change click', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val)
                                .draw();
                        });
                });
                this.api().columns([1]).every(function(e) {
                    var column = this;
                    $("#di").html('');
                    var text = $(
                            '<input type="text" placeholder="" class="form-control" style="text-transform:uppercase"/>'
                        )
                        .appendTo($("#di"))
                        .on('keyup change click', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val)
                                .draw();
                        });
                });
                this.api().columns([2]).every(function(e) {
                    var column = this;
                    $("#ai").html('');
                    var text = $(
                            '<input type="text" placeholder="" class="form-control" style="text-transform:uppercase"/>'
                        )
                        .appendTo($("#ai"))
                        .on('keyup change click', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column
                                .search(val)
                                .draw();
                        });
                });
            },
            ajax: {
                url: "includes/schedule_data.php",
                dataSrc: ""
            },
            processing: false,
            serverSide: false,
            language: {
                loadingRecords: '<i class="fa fa-circle-o-notch fa-spin"></i> Loading schedule - please wait...'
            },
            "pageLength": 50,
            columns: [{
                    data: 'flightNumberDisplay',
                    render: function(data, type, row, meta) {
                        if (type == 'sort') {
                            return row.flightNumber
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: "depIcao"
                },
                {
                    data: "arrIcao"
                },
                {
                    data: "depTime",
                    "orderable": false
                },
                {
                    data: "arrTime",
                    "orderable": false
                },
                {
                    data: "duration"
                },
                {
                    data: "operator"
                },
                {
                    data: "aircraftTypeCommas",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [7, 'asc']
            ]
        });
    });
</script>