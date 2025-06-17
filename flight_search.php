
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
<style>

.schedule-section {
    position: relative;
    padding: 80px 0;
    min-height: calc(100vh - 128px);
    background-image: url('./assets/images/backgrounds/leaderboard_sky_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.schedule-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.schedule-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}


.schedule-glass-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
}

.schedule-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}


.schedule-header {
    padding: 30px;
    text-align: center;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.schedule-header h1 {
    font-size: 4rem;
    font-weight: 300;
    margin-bottom: 10px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.schedule-header hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 15px auto;
    width: 80%;
    border-radius: 2px;
}

.schedule-header p {
    font-size: 1.5rem;
    line-height: 1.6;
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.schedule-header strong {
    color: rgba(255, 215, 0, 1);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.schedule-header a {
    color: rgba(255, 215, 0, 1);
    text-decoration: none;
    transition: all 0.3s ease;
}

.schedule-header a:hover {
    color: rgba(255, 255, 255, 1);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}


.form-group {
    margin-bottom: 15px;
}

.form-control {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 8px 12px;
    color: #333;
    font-weight: 600;
    height: 40px !important;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    background: rgba(255, 255, 255, 1);
    border-color: rgba(255, 215, 0, 0.8);
    box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
}

.form-control::placeholder {
    color: #666;
}

.form-control.uppercase {
    text-transform: uppercase;
}

.col-form-label {
    color: #ffffff;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}


.flights-table-glass-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.flights-table-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}


.flights-table-header {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.flights-table-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.flights-table-header .fa-plane {
    color: rgba(255, 215, 0, 1);
    font-size: 1.5rem;
}


.flights-table-wrapper {
    overflow-x: auto;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}


.flights-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.flights-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.flights-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.flights-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}


.flights-table {
    margin: 0;
    background: transparent;
    color: #fff;
    width: 100%;
}

.flights-table thead th {
    color: #fff !important;
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
}

.flights-table tbody tr {
    background: rgba(255, 255, 255, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
    transition: all 0.3s ease;
}

.flights-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95) !important;
    transform: scale(1.01);
}

.flights-table tbody td {
    padding: 15px 12px;
    border: none !important;
    vertical-align: middle;
    font-size: 14px;
    color: #333;
}

.flights-table tbody tr:last-child {
    border-bottom: none !important;
}


.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: #fff;
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
    color: #333;
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


#flights_wrapper .row .col-sm-6 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#flights_wrapper .row .col-sm-5 {
    padding-left: 0 !important;
    padding-right: 0 !important;
    background: transparent !important;
    color: #fff !important;
}

#flights_wrapper .row .col-sm-7 {
    background: transparent !important;
    padding: 0 !important;
}

#flights_wrapper .row .col-sm-12 {
    overflow-x: auto;
}


@media (max-width: 1200px) {
    .schedule-section {
        padding: 80px 0;
    }
    
    .schedule-header h1 {
        font-size: 2.2rem;
    }
    
    .flights-table thead th,
    .flights-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .schedule-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 80px;
    }
    
    .schedule-header h1 {
        font-size: 2rem;
    }
    
    .schedule-glass-card,
    .flights-table-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    
}

@media (max-width: 768px) {
    .schedule-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 80px;
    }
    
    .schedule-header {
        padding: 25px 20px;
    }
    
    .schedule-header h1 {
        font-size: 1.8rem;
    }
    
    .schedule-header p {
        font-size: 1.3rem;
    }
    
    .form-group {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .col-form-label {
        margin-bottom: 5px;
    }
    
    .schedule-glass-card,
    .flights-table-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    
    
    .flights-table {
        font-size: 12px;
    }
    
    .flights-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .flights-table tbody td {
        padding: 12px 8px;
        font-size: 12px;
        white-space: nowrap;
    }
    
    .flights-table-header {
        padding: 15px 20px;
    }
    
    .flights-table-header h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .schedule-section {
        padding: 80px 0;
    }
    
    .offset-header {
        padding-top: 50px;
    }
    
    .schedule-header {
        padding: 20px 15px;
    }
    
    .schedule-header h1 {
        font-size: 1.6rem;
    }
    
    .schedule-header p {
        font-size: 1.2rem;
    }
    
    .schedule-glass-card,
    .flights-table-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    
    
    .form-control {
        height: 36px !important;
    }
    
    .flights-table-header {
        padding: 12px 15px;
    }
    
    .flights-table-header h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .schedule-section {
        padding: 80px 0;
    }
    
    .offset-header {
        padding-top: 40px;
    }
    
    .schedule-header h1 {
        font-size: 1.4rem;
    }

    
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
}


@media print {
    .schedule-section {
        background: white;
        padding: 80px 0;
    }
    
    .schedule-section::before {
        display: none;
    }
    
    .schedule-header h1,
    .schedule-header p {
        color: black;
        text-shadow: none;
    }
    
    .schedule-glass-card,
    .flights-table-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    
    .flights-table thead th,
    .flights-table tbody td {
        color: black;
    }
}
</style>
<section id="content" class="section schedule-section offset-header">
    <div class="container">
        <!-- Header Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="schedule-glass-card">
                    <div class="schedule-header">
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
                </div>
            </div>
        </div>
        <!-- Flights Table Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="flights-table-glass-card">
                    <div class="flights-table-header">
                        <h3><i class="fa fa-plane"></i> Flight Schedule</h3>
                    </div>
                    <div class="flights-table-wrapper">
                        <table class="table table-striped flights-table" id="flights" width="100%">
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
                            '<input type="text" placeholder="" class="form-control uppercase"/>'
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
                            '<input type="text" placeholder="" class="form-control uppercase"/>'
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
