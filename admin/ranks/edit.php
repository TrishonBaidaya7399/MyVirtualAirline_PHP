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

$rankId = cleanString($_GET['id']);
$rank = null;
$res = Api::sendAsync('GET', 'v1/rank/' . $rankId, null);
if ($res->getStatusCode() == 200) {
    $rank = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/ranks');
    die();
}

$name = $rank->name;
$minHour = $rank->hours;
$minXp = $rank->xp;
$pay = $rank->pay ?? 0;
$image_url = $rank->imageUrl;

$responseMessage = null;
$status = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanString($_POST['name']);
    $minHour = cleanString($_POST['minhour']);
    $minXp = cleanString($_POST['minxp']);
    $pay = cleanString($_POST['pay']);

    if (empty($name) || $minHour < 0 || $minXp < 0 || $pay < 0) {
        $status = "required_fields";
    }

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
            'Id' => $rankId,
            'Name' => $name,
            'Hours' => $minHour,
            'Xp' => $minXp,
            'ImageUrl' => $image_url,
            'Pay' => $pay
        ];

        $res = Api::sendSync('PUT', 'v1/rank', $data);

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
                            <div class="page-header-icon"><i data-feather="star"></i></div>
                            Edit Rank
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Ranks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Rank Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Rank has been successfully updated.
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
                                    echo 'An error occurred when updating the rank. Please try again later.';
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
                        <input name="name" type="text" id="name" class="form-control"
                            value="<?php echo htmlspecialchars(strval($name)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Min Hours*</label>
                        <input name="minhour" type="number" id="minhour" class="form-control"
                            value="<?php echo htmlspecialchars($minHour) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Min XP*</label>
                        <input name="minxp" type="number" id="minxp" class="form-control"
                            value="<?php echo htmlspecialchars($minXp) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Pay Per Hour*</label>
                        <input name="pay" type="number" id="pay" class="form-control"
                            value="<?php echo htmlspecialchars($pay) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Rank Image*</label>
                        <p><strong>Current Image:</strong></p>
                        <img src="<?php echo website_base_url; ?>uploads/ranks/<?php echo htmlspecialchars(strval($image_url)); ?>"
                            width="80" /><br /><br />
                        <input type="file" name="photo" class="form-control" />
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
</main>
<?php include '../includes/footer.php'; ?>