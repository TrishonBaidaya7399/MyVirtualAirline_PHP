<?php

use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
session_start();
validateSession();

?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css"
    href="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.css" />
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Pilot Roster</h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped roster" id="roster">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>&nbsp;</th>
                            <th>Pilot Id</th>
                            <th>Rank</th>
                            <th></th>
                            <th>Hours</th>
                            <th>Last<br />30 Days</th>
                            <th>Status</th>
                            <th>Joined</th>
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
    $(document).ready(function() {
        $('#roster').DataTable({
            "processing": true,
            serverSide: true,
            ajax: {
                url: "<?php echo website_base_url; ?>includes/roster_data.php",
                type: "POST",
            },
            "pageLength": 10,
            columns: [{
                    name: 'name',
                    data: "name",
                    sortable: true,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            var image =
                                '<i class="fa fa-user-circle profile-small" aria-hidden="true"></i>';
                            if (row.profileImage != null) {
                                image = '<img src="/uploads/profiles/' + row.profileImage +
                                    '" class="img-circle pilot-profile-image-small"/>';
                            }
                            return image + '&nbsp; <a href="/profile.php?id=' + row.id + '">' +
                                data + '</a>'
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: 'location',
                    data: "location",
                    sortable: false,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            return '<img src="/images/flags/' + data +
                                '.gif" width="20" height="20" title="' + data + '">'
                        } else {
                            return data;
                        }
                    }
                },
                {
                    name: 'callsign',
                    data: 'callsign',
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
                    name: 'rankImage',
                    data: "rankImage",
                    sortable: false,
                    render: function(data, type, row, meta) {
                        if (type == 'display') {
                            if (data != null) {
                                return '<img src="/uploads/ranks/' + data +
                                    '" width="80" title="' + row.rankName +
                                    '">'
                            } else {
                                return "";
                            }
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
            ],
            colReorder: false,
            fnServerParams: function(data) {
                data['order'].forEach(function(items, index) {
                    data['order'][index]['name'] = data['columns'][items.column]['data'];
                });
            },
            "order": [
                [2, 'asc']
            ]
        });
    });
</script>