<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/images.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(5)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_award") {
    Api::sendAsync('DELETE', 'v1/award/' . cleanString($_GET['id']), null);
}

$status = null;
$name = null;
$description = null;
$showOnWebsite = 0;
$active = 0;
$image_url = "";

$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanString($_POST['name']);
    $description = cleanString($_POST['description']);
    $showOnWebsite = false;
    if (isset($_POST['showonweb'])) {
        $showOnWebsite = cleanString($_POST['showonweb']);
    }
    $active = false;
    if (isset($_POST['active'])) {
        $active = cleanString($_POST['active']);
    }

    if (empty($name) || empty($description)) {
        $status = "required_fields";
    }

    $valid_file = true;
    if ($_FILES['photo']['name']) {
        if (!$_FILES['photo']['error']) {
            $new_file_name = strtolower($_FILES['photo']['tmp_name']);
            if ($_FILES['photo']['size'] > (2024000)) { //can't be larger than 2 MB
                $valid_file = false;
                $status = "award_image_size";
            }
            if ($valid_file) {
                $image_url = store_uploaded_image('photo', 200, null, 'awards');
            }
        } else {
            $status = "award_image_error";
            $valid_file = false;
        }
    }

    if (empty($status) && $valid_file) {
        $data = [
            'name' => $name,
            'imageUrl' => $image_url,
            'description' => $description,
            'showOnWebsite' => boolval($showOnWebsite),
            'active' => boolval($active)
        ];

        $res = Api::sendSync('POST', 'v1/award', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                $name = null;
                $description = null;
                $showOnWebsite = 0;
                $active = 0;
                $image_url = "";
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

$awards = null;
$res = Api::sendSync('GET', 'v1/awards/unfiltered', null);
if ($res->getStatusCode() == 200) {
    $awards = json_decode($res->getBody(), true);
}
if (!empty($awards)) {
    foreach ($awards as &$award) {
        $award["imageUrl"] = '<img src="' . website_base_url . 'uploads/awards/' . $award["imageUrl"] . '" width="35" />';
        $award["date"] = (new DateTime($award["date"]))->format('Y-m-d h:i:s');
        $award["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/awards/edit.php?id=' . $award["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $award["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/awards/assignaward.php?id=' . $award["id"] . '" title="Give Award"><i data-feather="award"></i></a>';
        $award["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $award["id"] . '"><i data-feather="trash-2"></i></a>';
        $award["showOnWebsite"] = $award["showOnWebsite"] ? "Yes" : "No";
        $award["active"] = $award["active"] ? "Yes" : "No";
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
                            <div class="page-header-icon"><i data-feather="award"></i></div>
                            Awards
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Manage Awards</div>
            <div class="card-body">
                <table class="table-bordered" id="awards" style="display:none;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Award</strong></th>
                            <th>Visible</th>
                            <th>Active</th>
                            <th>Created</th>
                            <th>Assigned Pilots</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Create Award</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Award successfully created.
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
                                    echo 'An error occurred when creating the award. Please try again later.';
                                }
                            }
                            if ($status == 'award_image_error') {
                                echo 'There was an error uploading your image. Please check it is less than 2Mb.';
                            }
                            if ($status == 'required_fields') {
                                echo 'Please check all fields have been completed correctly.';
                            }
                            ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                <?php } ?>
                <form method="post" class="form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="mb-1">Name*</label>
                        <input name="name" type="text" id="name" class="form-control"
                            value="<?php echo htmlspecialchars(strval($name)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Description*</label>
                        <textarea name="description" cols="30" rows="5" id="description" class="form-control"
                            required><?php echo htmlspecialchars(strval($description)) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Show on Website</label>
                        <input type="checkbox" name="showonweb" value="true"
                            <?php echo $showOnWebsite == true ? "checked" : ""; ?>>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Active</label>
                        <input type="checkbox" name="active" value="true"
                            <?php echo $active == true ? "checked" : ""; ?>>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Image*</label>
                        <input type="file" name="photo" class="form-control" required />
                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Maximum file size 2MB
                        </div>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Award</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this award?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($awards); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_award&id=" + id);
        });
        $('#awards').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "imageUrl",
                    "orderable": false
                },
                {
                    data: "name",
                },
                {
                    data: "showOnWebsite",
                },
                {
                    data: "active",
                },
                {
                    data: "date",
                },
                {
                    data: "assignedPilotCount",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [3, 'desc']
            ],
            "drawCallback": function(settings) {
                feather.replace();
            },
            "initComplete": function(settings, json) {
                $(this).show()
            },
        });
    });
</script>
<?php include '../includes/footer.php'; ?>