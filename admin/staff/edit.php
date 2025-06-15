<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!$_SESSION['owner'] == 1) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$id = cleanString($_GET['id']);
$staff = null;
$res = Api::sendAsync('GET', 'v1/staff/' . $id, null);
if ($res->getStatusCode() == 200) {
    $staff = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/staff');
    die();
}

$status = null;
$staffName = $staff->staffName;
$role = $staff->roleName;
$description = $staff->roleDescription;
$email = $staff->contactEmail;
$order = $staff->sortOrder;
$permissions = $staff->permissions;
$responseMessage = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = cleanString($_POST['role_name']);
    $description = cleanString($_POST['role_desc']);
    $email = cleanString($_POST['email']);
    $order = cleanString($_POST['order']);
    if (isset($_POST['permissions'])) {
        $permissions = implode(',', $_POST['permissions']);
    }

    if (empty($role) || empty($description)) {
        $status = "required_fields";
    }

    if (empty($status)) {
        $data = [
            'staffId' => $id,
            'permissions' => $permissions,
            'contactEmail' => $email,
            'roleName' => $role,
            'roleDescription' => $description,
            'sortOrder' => $order
        ];

        $res = Api::sendSync('PUT', 'v1/staff', $data);

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

$staffRoles = null;
$res = Api::sendSync('GET', 'v1/staff/roles', null);
if ($res->getStatusCode() == 200) {
    $staffRoles = json_decode($res->getBody(), false);
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
                            <div class="page-header-icon"><i data-feather="shield"></i></div>
                            Edit Staff
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Staff Details - <?php echo $staff->staffName; ?></div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Staff successfully updated.
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
                                    echo 'An error occurred when creating the staff member. Please try again later.';
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
                    <div class="row gx-3 g-5 mb-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="mb-1">Role Name*</label>
                                <input name="role_name" type="text" id="role_name" class="form-control" max-length="4"
                                    placeholder="eg: Fleet Manager" value="<?php echo strval($role) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Role Description*</label>
                                <textarea name="role_desc" cols="30" rows="5" id="role_desc" class="form-control"
                                    placeholder="eg: Responsible for managing fleet operations."
                                    required><?php echo strval($description) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Contact Email</label>
                                <input name="email" type="email" id="email" class="form-control"
                                    value="<?php echo strval($email) ?>">
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Leave blank
                                    to
                                    hide on staff page</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Display Order*</label>
                                <input name="order" type="number" id="order" class="form-control" placeholder="eg: 10"
                                    value="<?php echo strval($order) ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="mb-1">Role Permissions*</label><br />
                                <?php
                                $currentPermissions = empty($permissions) ? null : explode(",", $permissions);
                                foreach ($staffRoles as $role) {
                                ?>
                                    <div class="form-check form-check-solid">
                                        <input class="form-check-input" id="permissions<?php echo $role->roleId; ?>"
                                            name="permissions[]" type="checkbox" value="<?php echo $role->roleId; ?>"
                                            <?php echo empty($currentPermissions) ? "" : (in_array($role->roleId, $currentPermissions) ? "checked" : ""); ?>>
                                        <label class="form-check-label"
                                            for="permissions<?php echo $role->roleId; ?>"><?php echo $role->role; ?></label>
                                    </div>
                                <?php
                                } ?>
                            </div>
                            <div class="mb-3">
                                <div class=" text-right">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary">Save
                                        Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>