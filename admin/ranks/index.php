<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/images.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(2)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_rank") {
    Api::sendAsync('DELETE', 'v1/rank/' . cleanString($_GET['id']), null);
}

$status = null;
$name = null;
$minHour = null;
$minXp = null;
$pay = null;
$rank_image_name = null;
$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanString($_POST['name']);
    $minHour = cleanString($_POST['minhour']);
    $minXp = cleanString($_POST['minxp']);
    $pay = cleanString($_POST['pay']);
    $rank_image_name = "";

    if (empty($name) || $minHour < 0 || $minXp < 0 || $pay < 0) {
        $status = "required_fields";
    }

    $image_url = "";
    $valid_file = true;
    if ($_FILES['photo']['name']) {
        if (!$_FILES['photo']['error']) {
            $new_file_name = strtolower($_FILES['photo']['tmp_name']);
            if ($_FILES['photo']['size'] > (2024000)) { //can't be larger than 2 MB
                $valid_file = false;
                $status = "rank_image_size";
            }
            if ($valid_file) {
                $image_url = store_uploaded_image('photo', 80, null, 'ranks');
            }
        } else {
            $status = "rank_image_error";
        }
    }

    if (empty($status) && $valid_file) {
        $data = [
            'Name' => $name,
            'Hours' => $minHour,
            'Xp' => $minXp,
            'ImageUrl' => $image_url,
            'Pay' => $pay
        ];

        $res = Api::sendSync('POST', 'v1/rank', $data);

        switch ($res->getStatusCode()) {
            case 200:
                break;
            case 400:
                $status = "error";
                $responseMessage = $res->getBody();
                break;
            default:
                $status = "error";
                break;
        }
    }
}

$ranks = null;
$res = Api::sendSync('GET', 'v1/ranks', null);
if ($res->getStatusCode() == 200) {
    $ranks = json_decode($res->getBody(), true);
}
if (!empty($ranks)) {
    foreach ($ranks as &$rank) {
        $rank["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/ranks/edit.php?id=' . $rank["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $rank["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $rank["id"] . '"><i data-feather="trash-2"></i></a>';
        $rank["image"] = '<img src="' . website_base_url . 'uploads/ranks/' . $rank["imageUrl"] . '" width="80"/>';
        $rank["pay"] = $rank["pay"] ?? 'Not Set';
    }
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
                            <div class="page-header-icon"><i data-feather="star"></i></div>
                            Ranks
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Manage Ranks</div>
            <div class="card-body">
                <table class="table-bordered" id="ranks" style="display:none;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Min Hours</th>
                            <th>Min XP</th>
                            <th>Pay Per Hour</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Create Rank</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Rank has been successfully created.
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } else { ?>
                    <?php if (!empty($status)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
                            if ($status == 'error') {
                                if (!empty($responseMessage)) {
                                    echo $responseMessage;
                                } else {
                                    echo 'An error occurred when creating the rank. Please try again later.';
                                }
                            }
                            if ($status == 'required_fields') {
                                echo 'Please check all fields have been completed correctly.';
                            }
                            if ($status == 'rank_image_error') {
                                echo 'There was an error uploading your image. Please check it is less than 2Mb.';
                            }
                            ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                <?php } ?>
                <form method="post" class="form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="mb-1">Rank Name*</label>
                        <input name="name" type="text" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Min Hours*</label>
                        <input name="minhour" type="number" id="minhour" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Min XP*</label>
                        <input name="minxp" type="number" id="minxp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Pay Per Hour*</label>
                        <input name="pay" type="number" id="pay" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Rank Image*</label>
                        <input type="file" name="photo" class="form-control" required />
                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Maximum file size
                            2MB. Recommended width: 80px.</div>
                    </div>
                    <div class="mb-3">
                        <div class=" text-right">
                            <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
                                Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Rank</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this rank?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($ranks); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_rank&id=" + id);
        });
        $('#ranks').DataTable({
            data: dataSet,
            "pageLength": 10,
            columns: [{
                    data: "name",
                },
                {
                    data: "hours",
                },
                {
                    data: "xp",
                },
                {
                    data: "pay",
                },
                {
                    data: "image",
                    "orderable": false
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: true,
            "order": [
                [1, 'asc']
            ],
            "drawCallback": function(settings) {
                feather.replace();
            },
            "initComplete": function(settings, json) {
                $(this).show()
            },
        });
        var easyMDE = new EasyMDE({
            element: document.getElementById('description'),
            toolbar: ['bold', 'italic', 'heading', '|', 'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'image',
                '|', 'preview', 'guide'
            ]
        });
    });
</script>
<?php include '../includes/footer.php'; ?>