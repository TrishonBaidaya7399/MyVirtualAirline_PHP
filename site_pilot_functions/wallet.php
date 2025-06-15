<?php

use Proxy\Api\Api;

include '../lib/functions.php';
include '../config.php';
Api::__constructStatic();
session_start();
validateSession();

$id = $_SESSION['pilotid'];
$res = Api::sendSync('GET', 'v1/pilot/' . $id, null);
$pilot = json_decode($res->getBody());
$responseCode = $res->getStatusCode();
if ($responseCode != 200) {
    echo '<script>alert("Pilot does not exist.");</script>';
    echo '<script>history.back(1);</script>';
    exit;
}
$trans = null;
$res = Api::sendSync('GET', 'v1/pilot/wallet/transactions/' . $id, null);
if ($res->getStatusCode() == 200) {
    $trans = json_decode($res->getBody(), true);
}
if (!empty($trans)) {
    foreach ($trans as &$tran) {
        $tran["amount"] = $tran["amount"] < 0 ? '<span style="color:red;">' . number_format($tran["amount"], 2) . '</span>' : '<span style="color:green;">+' . number_format($tran["amount"], 2) . '</span>';
        $tran["dateString"] = (new DateTime($tran["date"]))->format('d-m-Y H:i');
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="../assets/plugins/datatables/datatables.min.css" />
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <p>
            <h1>Balance: $<?php echo number_format($pilot->wallet, 2) ?></h1>
            <hr />
            </p>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Transactions</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped" id="wallet">
                        <thead>
                            <tr>
                                <th><strong>Reference</strong></th>
                                <th><strong>Date</strong></th>
                                <th><strong>Description</strong></th>
                                <th><strong>Amount</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
<script type="text/javascript" src="../assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
var dataSet = <?php echo json_encode($trans); ?>;
$(document).ready(function() {
    $('#wallet').DataTable({
        data: dataSet,
        "pageLength": 10,
        columns: [{
                data: "reference",
            },
            {
                data: "dateString",
                render: function(data, type, row, meta) {
                    if (type == 'display') {
                        return data;
                    } else {
                        return row.date;
                    }
                }
            },
            {
                data: "description"
            },
            {
                data: "amount",
            }
        ],
        colReorder: false,
        "order": [
            [1, 'desc']
        ]
    });
});
</script>