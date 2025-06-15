<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(6)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_download") {
    Api::sendAsync('DELETE', 'v1/download/' . cleanString($_GET['id']), null);
}

$status = null;
$category = null;
$description = null;
$fileName = null;
$link = null;
$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = cleanString($_POST['type']);
    $description = cleanString($_POST['description']);
    $fileName = cleanString($_POST['file_name']);

    if (empty($fileName) || empty($description) || empty($category)) {
        $status = "required_fields";
    }

    $valid_file = true;
    if ($_FILES['uploaded']['tmp_name']) {
        $target = '../' . file_upload_dir;
        $target = $target . basename($_FILES['uploaded']['name']);
        if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
            $link = $_FILES['uploaded']['name'];
        } else {
            $status = "award_file_error";
            $valid_file = false;
        }
    }

    if (empty($status) && $valid_file) {
        $data = [
            'fileName' => $fileName,
            'type' => $category,
            'description' => $description,
            'link' => $link
        ];

        $res = Api::sendSync('POST', 'v1/download', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                $category = null;
                $description = null;
                $fileName = null;
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

$downloads = null;
$res = Api::sendSync('GET', 'v1/downloads', null);
if ($res->getStatusCode() == 200) {
    $downloads = json_decode($res->getBody(), true);
}
if (!empty($downloads)) {
    foreach ($downloads as &$download) {
        $download["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/downloads/edit.php?id=' . $download["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $download["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $download["id"] . '"><i data-feather="trash-2"></i></a>';
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
                            <div class="page-header-icon"><i data-feather="download"></i></div>
                            Downloads
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Manage Downloads</div>
            <div class="card-body">
                <table class="table-bordered" id="downloads" style="display:none;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</strong></th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Create Download</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Download successfully created.
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
                                    echo 'An error occurred when creating the download. Please try again later.';
                                }
                            }
                            if ($status == 'download_file_error') {
                                echo 'There was an error uploading your download. Please check your hosting supports the file size you are trying to upload and configure as required.';
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
                        <input name="file_name" type="text" id="file_name" class="form-control"
                            value="<?php echo htmlspecialchars(strval($fileName)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Description*</label>
                        <textarea name="description" rows="2" id="description" class="form-control"
                            required><?php echo htmlspecialchars(strval($description)) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Category*</label>
                        <select name="type" id="type" class="form-select">
                            <option value="Fleet">Fleet</option>
                            <option value="Scenery">Scenery</option>
                            <option value="Tools">Tools</option>
                            <option value="Documentation">Documentation</option>
                            <option value="Photo">Photo</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">File*</label>
                        <input type="file" name="uploaded" class="form-control" required />
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
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Download</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this download?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($downloads); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_download&id=" + id);
        });
        $('#downloads').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "fileName",
                },
                {
                    data: "type",
                },
                {
                    data: "description",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [1, 'desc']
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