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
$id = cleanString($_GET['id']);

$award = null;
$res = Api::sendAsync('GET', 'v1/award/' . $id, null);
if ($res->getStatusCode() == 200) {
    $award = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/awards');
    die();
}

$status = null;
$responseMessage = null;
$name = $award->name;
$description = $award->description;
$showOnWebsite = $award->showOnWebsite;
$active = $award->active;
$image_url = $award->imageUrl;

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
    if (empty($name) || empty($description) || empty($image_url)) {
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
        }
    }

    if (empty($status) && $valid_file) {
        $data = [
            'id' => $id,
            'name' => $name,
            'imageUrl' => $image_url,
            'description' => $description,
            'showOnWebsite' => boolval($showOnWebsite),
            'active' => boolval($active)
        ];

        $res = Api::sendSync('PUT', 'v1/award', $data);

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
                            <div class="page-header-icon"><i data-feather="award"></i></div>
                            Edit Award
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Awards
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Award Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                <div class="alert alert-success alert-dismissible fade show">Award successfully updated.
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
                                    echo 'An error occurred when updating the award. Please try again later.';
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
                    <div class="row gx-3 g-5 mb-3">
                        <div class="col-lg-6">
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
                        </div>
                        <div class="col-lg-6">
                            <?php if (!empty($image_url)) { ?>
                            <div class="mb-3">
                                <label class="mb-1">Current Image</label>
                                <br />
                                <img src="<?php echo website_base_url ?>uploads/awards/<?php echo $image_url; ?>"
                                    width="200" />
                            </div>
                            <?php } ?>
                            <div class="mb-3">
                                <label class="mb-1">New Image*</label>
                                <input type="file" name="photo" class="form-control" />
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Maximum file
                                    size 2MB
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class=" text-right">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
                                        Changes</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>