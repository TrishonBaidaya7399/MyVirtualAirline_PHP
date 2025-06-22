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
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.hero-section {
    position: relative;
    min-height: calc(100vh - 128px);
    padding: 80px 0;
    background-image: url('./assets/images/backgrounds/leaderboard_sky_bg.jpg');
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

.roster-glass-card {
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

.roster-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.roster-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.roster-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.roster-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.roster-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.roster-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}

.roster-table {
    width: 100%;
    margin: 0;
    background: transparent;
    color: rgba(0, 0, 0, 0.9);
}

.roster-table thead th {
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

.roster-table tbody tr {
    background: rgba(255, 255, 255, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
    transition: all 0.3s ease;
}

.roster-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95) !important;
    transform: scale(1.01);
}

.roster-table tbody td {
    padding: 15px 12px;
    border: none !important;
    vertical-align: middle;
    font-size: 14px;
    color: rgba(0, 0, 0, 0.9);
}

.roster-table tbody td:first-child .profile-small {
    font-size: 20px;
    color: #333;
}

.roster-table tbody td img.pilot-profile-image-small {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    vertical-align: middle;
    margin-right: 5px;
}

.roster-table tbody td img[src*="/images/flags/"] {
    vertical-align: middle;
    margin-right: 5px;
}

.roster-table tbody td img[src*="/uploads/ranks/"] {
    vertical-align: middle;
    max-width: 80px;
    max-height: 80px;
}

.roster-table tbody .bg-yellow-soft {
    background-color: rgba(255, 193, 7, 0.2) !important;
    color: #fff;
}

.roster-table tbody .bg-green-soft {
    background-color: rgba(40, 167, 69, 0.2) !important;
    color: #fff;
}

.roster-table tbody tr:last-child {
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
    .roster-table thead th,
    .roster-table tbody td {
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
    .roster-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    .roster-table-wrapper {
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
    .roster-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    .roster-table-wrapper {
        max-height: 400px;
    }
    .roster-table {
        font-size: 12px;
    }
    .roster-table thead th {
        padding: 12px 8px;
        font-size: 11px;
    }
    .roster-table tbody td {
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
    .roster-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    .roster-table-wrapper {
        max-height: 350px;
    }
    .roster-table thead th,
    .roster-table tbody td {
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
    .roster-table-wrapper {
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
            <h3 class="global-title">Pilot Roster</h3>
        </div>
        <div class="roster-glass-card">
            <div class="roster-table-wrapper">
                <table class="table table-striped roster roster-table" id="roster">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th> </th>
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
                            return image + '  <a href="/profile.php?id=' + row.id + '">' +
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