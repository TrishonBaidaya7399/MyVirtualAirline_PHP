<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/images.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(8)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$baseId = cleanString($_GET['id']);
$base = null;
$res = Api::sendAsync('GET', 'v1/operations/base/' . $baseId, null);
if ($res->getStatusCode() == 200) {
    $base = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/bases');
    die();
}

$status = null;
$name = $base->b->hub;
$icao = $base->b->icao;
$desc = $base->b->description;
$image_url = $base->b->imageUrl;
$responseMessage = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanString($_POST['name']);
    $icao = cleanString($_POST['icao']);
    $desc = $_POST['editorContent'];

    if (isset($_POST['deleteimage'])) {
        $image_url = "";
    }

    $editorValid = json_decode($desc)->blocks != null;

    if (empty($name) || empty($icao) || !$editorValid) {
        $status = "required_fields";
    }

    $valid_file = true;
    if ($_FILES['photo']['name']) {
        if (!$_FILES['photo']['error']) {
            $new_file_name = strtolower($_FILES['photo']['tmp_name']);
            if ($_FILES['photo']['size'] > (2024000)) { //can't be larger than 2 MB
                $valid_file = false;
                $status = "base_image_size";
            }
            if ($valid_file) {
                $image_url = store_uploaded_image('photo', 350, null, 'bases');
            }
        } else {
            $status = "base_image_error";
        }
    }

    if (empty($status) && $valid_file) {
        $data = [
            'Id' => $baseId,
            'Hub' => $name,
            'Icao' => $icao,
            'Description' => $desc,
            'ImageUrl' => $image_url
        ];

        $res = Api::sendSync('PUT', 'v1/operations/base', $data);

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
                            <div class="page-header-icon"><i data-feather="grid"></i></div>
                            Edit Base
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Bases
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Base Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Base has been updated.
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
                                    echo 'An error occurred when updating the base. Please try again later.';
                                }
                            }
                            if ($status == 'required_fields') {
                                echo 'Please check all fields have been completed correctly.';
                            }
                            if ($status == 'base_image_error') {
                                echo 'There was an error uploading your image. Please check it is less than 2Mb.';
                            }
                            ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                <?php } ?>

                <form method="post" class="form" id="editorForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="mb-1">Base Name*</label>
                        <input name="name" type="text" id="name" class="form-control"
                            value="<?php echo htmlspecialchars(strval($name)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">ICAO*</label>
                        <input name="icao" type="text" id="icao" class="form-control" max-length="4"
                            value="<?php echo htmlspecialchars(strval($icao)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Description</label>
                        <div id="editorJs" class="form-control"></div>
                    </div>
                    <?php if (!empty($image_url)) { ?>
                        <div class="mb-3">
                            <label class="mb-1">Current Banner</label>
                            <br />
                            <img src="<?php echo website_base_url ?>uploads/bases/<?php echo $image_url; ?>" width="400"
                                height="400" />
                        </div>
                        <div class="mb-3">
                            <label class="mb-1">Delete current banner?</label>
                            <input type="checkbox" name="deleteimage" value="1">
                        </div>
                    <?php } ?>
                    <div class="mb-3">
                        <label class="mb-1">Base Image</label>
                        <input type="file" name="photo" class="form-control" />
                        <p class="help-block">Maximum file size 2MB. Recommended 400px wide.</p>
                    </div>
                    <div class="mb-3">
                        <div class=" text-right">
                            <input name="editorContent" type="hidden" id="editorContent" />
                            <button id="submitButton" type="submit" class="btn btn-primary">Save
                                Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.7"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.8"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.6"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@2.5.0"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script src="<?php echo website_base_url; ?>admin/js/editor.js"></script>
<?php include '../includes/footer.php'; ?>
<script type="text/javascript">
    contentJson = '<?php echo $desc != null ? addslashes(preg_replace("/\r|\n/", "", $desc)) : ""; ?>';
</script>