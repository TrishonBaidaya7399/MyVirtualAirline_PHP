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
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="<?php echo website_base_url; ?>site_pilot_functions/send_message.php"
                    class="js_showloader btn btn-success">Send Message</a><br /><br />
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Inbox</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-hover" id="mailbox">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
            dataSrc: ""
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
        ]
    });
});
</script>