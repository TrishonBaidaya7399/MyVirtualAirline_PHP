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
$id = cleanString($_GET['id']);
$download = null;
$res = Api::sendAsync('GET', 'v1/download/' . $id, null);
if ($res->getStatusCode() == 200) {
    $download = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/fleet');
    die();
}
$status = null;
$category = $download->type;
$description = $download->description;
$fileName = $download->fileName;

$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = cleanString($_POST['type']);
    $description = cleanString($_POST['description']);
    $fileName = cleanString($_POST['file_name']);

    if (empty($fileName) || empty($description) || empty($category)) {
        $status = "required_fields";
    }

    if (empty($status)) {
        $data = [
            'id' => $id,
            'fileName' => $fileName,
            'type' => $category,
            'description' => $description
        ];

        $res = Api::sendSync('PUT', 'v1/download', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
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
                            Edit Download
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Downloads
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Download Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Download successfully updated.
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
                                    echo 'An error occurred when updating the download. Please try again later.';
                                }
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
                        <div class=" text-right">
                            <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
                                Changes</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    $(document).ready(function() {
        $('#type').val('<?php echo $category; ?>');
    });
</script>
<?php include '../includes/footer.php'; ?>