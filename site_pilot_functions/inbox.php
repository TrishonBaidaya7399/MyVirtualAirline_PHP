<?php
include '../lib/functions.php';
require_once __DIR__ . '/../proxy/api.php';
include '../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();
validateSession();

$id = null;
if (isset($_GET['id'])) {
    $id = cleanString($_GET['id']);
}
$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_message") {
    $res = Api::sendSync('PUT', 'v1/mailbox/mail/mark-deleted/' . $id, null);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/datatables.min.css" />

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}
.row{
    width: 100%;
}
.route-map-section {
    position: relative;
    padding: 90px 0;
    min-height: calc(100vh - 128px);
    background-image: url('../assets/images/backgrounds/world_map2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.route-map-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.route-map-section .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.offset-header {
    padding-top: 100px;
}

.route-map-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
}

.route-map-title {
    font-size: 4rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.route-map-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.route-map-header {
    padding: 30px;
    text-align: center;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.route-map-header h1 {
    font-size: 2.5rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-header hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 15px auto;
    width: 80%;
    border-radius: 2px;
}

.route-map-header p {
    font-size: 2rem;
    line-height: 1.6;
    margin-bottom: 15px;
    color: rgba(0, 0, 0, 0.9);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.route-map-header strong {
    color: rgba(255, 215, 0, 1);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.route-map-header a {
    color: rgba(255, 215, 0, 1);
    text-decoration: none;
    transition: all 0.3s ease;
}

.route-map-header a:hover {
    color: rgba(255, 255, 255, 1);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}

.flights-table-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    width: 100%;
    max-width: 100%;
}

.flights-table-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.flights-table-header {
    background: rgba(255, 255, 255, 0.9);
    color: rgba(255, 255, 255, 1);
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.flights-table-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
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
    max-height: 600px;
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
    color: rgba(255, 255, 255, 0.9);
    width: 100%;
}

.flights-table thead th {
    color: rgba(255, 255, 255, 0.9);
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
    color: rgba(255, 255, 255, 0.9);
}

.flights-table tbody tr:last-child {
    border-bottom: none !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: rgba(255, 255, 255, 0.9);
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
    color: #fff;
    font-style: italic;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    font-size: 1.2rem;
    display: none; /* Ensure it starts hidden */
}

.no-data-message svg {
    width: 100px;
    height: 100px;
    margin-bottom: 20px;
    fill: rgba(255, 215, 0, 0.8);
}

#mailbox_wrapper .row .col-sm-6 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#mailbox_wrapper .row .col-sm-5 {
    padding-left: 0 !important;
    padding-right: 0 !important;
    background: transparent !important;
    color: rgba(0, 0, 0, 0.9);
}

#mailbox_wrapper .row .col-sm-7 {
    background: transparent !important;
    padding: 0 !important;
}

#mailbox_wrapper .row .col-sm-12 {
    overflow-x: auto;
}

.btn-success {
    background: linear-gradient(45deg, rgba(0, 128, 0, 0.8), rgba(0, 100, 0, 0.9));
    border: 1px solid rgba(0, 128, 0, 0.5);
    color: #fff;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-success:hover {
    background: linear-gradient(45deg, rgba(0, 128, 0, 1), rgba(0, 100, 0, 1));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 128, 0, 0.4);
    color: #fff;
}

.modal-content {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    color: #fff;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-body {
    padding: 20px;
    text-align: center;
}

.modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(45deg, rgba(0, 123, 255, 0.8), rgba(0, 86, 179, 0.9));
    border: 1px solid rgba(0, 123, 255, 0.5);
    color: #fff;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(45deg, rgba(0, 123, 255, 1), rgba(0, 86, 179, 1));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    color: #fff;
}

.btn-secondary {
    background: rgba(108, 117, 125, 0.2);
    border: 1px solid rgba(108, 117, 125, 0.5);
    color: #fff;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: rgba(108, 117, 125, 0.4);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
    color: #fff;
}

@media (max-width: 1200px) {
    .route-map-section {
        padding: 80px 0;
    }
    .route-map-title {
        font-size: 2.5rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .global-heading .global-title {
        font-size: 35px;
    }
    .flights-table thead th,
    .flights-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
    .btn-success {
        padding: 7px 18px;
    }
    .no-data-message svg {
        width: 80px;
        height: 80px;
    }
}

@media (max-width: 992px) {
    .route-map-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 80px;
    }
    .route-map-title {
        font-size: 2.2rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .global-heading .global-title {
        font-size: 30px;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    .flights-table-wrapper {
        max-height: 500px;
    }
    .btn-success {
        padding: 6px 16px;
    }
    .no-data-message svg {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 768px) {
    .route-map-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .route-map-title {
        font-size: 2rem;
    }
    .route-map-icon {
        font-size: 1.8rem;
    }
    .global-heading .global-title {
        font-size: 25px;
    }
    .route-map-header {
        padding: 25px 20px;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    .flights-table-wrapper {
        max-height: 400px;
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
    .btn-success {
        padding: 5px 14px;
        font-size: 14px;
    }
    .no-data-message svg {
        width: 60px;
        height: 60px;
    }
}

@media (max-width: 576px) {
    .route-map-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 50px;
    }
    .route-map-title {
        font-size: 1.8rem;
    }
    .route-map-icon {
        font-size: 1.5rem;
    }
    .global-heading .global-title {
        font-size: 20px;
    }
    .route-map-header {
        padding: 20px 15px;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    .flights-table-wrapper {
        max-height: 350px;
    }
    .flights-table-header {
        padding: 12px 15px;
    }
    .flights-table-header h3 {
        font-size: 1.3rem;
    }
    .btn-success {
        padding: 4px 12px;
        font-size: 12px;
    }
    .no-data-message svg {
        width: 50px;
        height: 50px;
    }
}

@media (max-width: 480px) {
    .route-map-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 40px;
    }
    .route-map-title {
        font-size: 1.6rem;
    }
    .route-map-icon {
        font-size: 1.3rem;
    }
    .global-heading .global-title {
        font-size: 18px;
    }
    .flights-table-wrapper {
        max-height: 300px;
    }
    .no-data-message {
        padding: 40px 15px;
        font-size: 14px;
    }
    .btn-success {
        padding: 3px 10px;
        font-size: 11px;
    }
    .no-data-message svg {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 25px;
    }
}

@media print {
    .route-map-section {
        background: white;
        padding: 20px 0;
    }
    .route-map-section::before {
        display: none;
    }
    .route-map-title,
    .route-map-header h1,
    .route-map-header p {
        color: black;
        text-shadow: none;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    .flights-table thead th,
    .flights-table tbody td {
        color: white;
    }
}
</style>

<section id="content" class="section route-map-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="global-heading" style="display: flex; justify-content: space-between; align-items: center; flex-direction: row; gap: 20px; width: 100%; margin-bottom: 10px">
                    <h3 class="global-title"><i class="fa fa-inbox"></i> Inbox</h3>
                    <a href="<?php echo website_base_url; ?>site_pilot_functions/send_message.php"
                    class="js_showloader btn btn-success">Send Message</a>
                </div>
                <div class="flights-table-glass-card">
                    <div class="flights-table-wrapper">
                        <table class="table table-striped flights-table" id="mailbox">
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="no-data-message" style="display:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <p>No messages in your inbox. Click the <a href="<?php echo website_base_url; ?>site_pilot_functions/send_message.php" class="js_showloader">Send Message</a> button to start a new conversation!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Delete Message</h3>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this message thread?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a type="button" class="btn btn-primary">Confirm</a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script type="text/javascript" src="/assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#deleteModal').on('show.bs.modal', function(event) {
        var id = $(event.relatedTarget).data('id');
        $(this).find('.btn-primary').attr("href", "?function=delete_message&id=" + id);
    });
    var table = $('#mailbox').DataTable({
        ajax: {
            url: "../includes/mailbox_data.php",
            type: "GET",
            dataSrc: function(json) {
                // Check if the response is empty or invalid
                if (!json || json.length === 0) {
                    $('#mailbox').hide();
                    $('.no-data-message').show();
                    return [];
                }
                return json;
            },
            error: function(xhr, error, thrown) {
                // Handle AJAX errors if any
                if (xhr.status === 200 && !$.trim(xhr.responseText)) {
                    $('#mailbox').hide();
                    $('.no-data-message').show();
                }
            }
        },
        "pageLength": 10,
        "ordering": false,
        columns: [{
                data: "tableConversationDisplayName",
                "sortable": false,
                render: function(data, type, row, meta) {
                    if (type == 'display') {
                        return '<a style="font-weight:normal;" href="view_message.php?id=' + row
                            .id + '">' + (row.unread ?
                                '<strong>' + data + '</strong>' : data) + '</a>';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "subject",
                "sortable": false,
                render: function(data, type, row, meta) {
                    if (type == 'display') {
                        return '<a style="font-weight:normal;" href="view_message.php?id=' + row
                            .id + '">' + (row.unread ? '<strong>' + data + '</strong>' : data) +
                            '</a>';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: "updatedDateString",
                "sortable": true,
                render: function(data, type, row, meta) {
                    if (type == 'display') {
                        return '<span style="font-size:12px;">' + (row.unread ?
                            '<strong>Updated: ' + data +
                            '</strong>' :
                            'Updated: ' + data) + '</span>';
                    } else {
                        return row.updatedDate;
                    }
                }
            },
            {
                data: "id",
                sortable: false,
                render: function(data, type, row, meta) {
                    if (type == 'display') {
                        return '<a href="#" data-toggle="modal" data-target="#deleteModal" title="Delete" data-id="' +
                            data + '"><i class="fa fa-trash" title="delete"></i></a>'
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
        "language": {
            "emptyTable": "No data available in table"
        }
    });

    // Additional check after data is loaded
    table.on('draw.dt', function() {
        if (table.data().length === 0) {
            $('#mailbox').hide();
            $('.no-data-message').show();
        } else {
            $('#mailbox').show();
            $('.no-data-message').hide();
        }
    });
});
</script>