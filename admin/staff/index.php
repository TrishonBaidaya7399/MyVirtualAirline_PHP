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

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_staff") {
    Api::sendAsync('DELETE', 'v1/staff/' . cleanString($_GET['id']), null);
}

$status = null;
$pilotId = null;
$role = null;
$description = null;
$email = null;
$order = null;
$permissions = null;
$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pilotId = cleanString($_POST['pilotId']);
    $role = cleanString($_POST['role_name']);
    $description = cleanString($_POST['role_desc']);
    $email = cleanString($_POST['email']);
    $order = cleanString($_POST['order']);
    if (isset($_POST['permissions'])) {
        $permissions = implode(',', $_POST['permissions']);
    }

    if (empty($pilotId) || empty($role) || empty($description)) {
        $status = "required_fields";
    }

    if (empty($status)) {
        $data = [
            'permissions' => $permissions,
            'contactEmail' => $email,
            'roleName' => $role,
            'roleDescription' => $description,
            'sortOrder' => $order,
            'staffPilotId' => $pilotId
        ];

        $res = Api::sendSync('POST', 'v1/staff', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                $role = null;
                $description = null;
                $email = null;
                $order = null;
                $permissions = null;
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

$staffs = null;
$res = Api::sendSync('GET', 'v1/staffs', null);
if ($res->getStatusCode() == 200) {
    $staffs = json_decode($res->getBody(), true);
}
if (!empty($staffs)) {
    foreach ($staffs as &$staff) {
        $staff["staffName"] = $staff["staffName"] . ' (' . $staff["staffCallsign"] . ')';
        $staff["dateAssigned"] = (new DateTime($staff["dateAssigned"]))->format('Y-m-d h:i:s');
        $staff["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/staff/edit.php?id=' . $staff["staffId"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $staff["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $staff["staffId"] . '"><i data-feather="trash-2"></i></a>';
    }
}
$staffUserList = null;
$res = Api::sendSync('GET', 'v1/staff/users', null);
if ($res->getStatusCode() == 200) {
    $staffUserList = json_decode($res->getBody(), false);
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
                            Staff
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Manage Staff</div>
            <div class="card-body">
                <table class="table-bordered" id="staff" style="display:none;">
                    <thead>
                        <tr>
                            <th>Name</strong></th>
                            <th>Role</th>
                            <th>Display Order</th>
                            <th>Date Assigned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Create Staff</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Staff successfully created.
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
                                    echo '<p>An error occurred when creating the staff member. Please try again later.</p>';
                                }
                            }
                            if ($status == 'required_fields') {
                                echo '<p>Please check all fields have been completed correctly.</p>';
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
                                <label class="mb-1">Select User*</label>
                                <select name="pilotId" id="pilotId" class="form-select" required>
                                    <option value="" selected="true">
                                        Select User
                                    </option>
                                    <?php
                                    if (!empty($staffUserList)) {
                                        foreach ($staffUserList as $user) {
                                    ?>
                                            <option value="<?php echo $user->id; ?>"
                                                <?php echo $user->id != $pilotId ? "" : 'selected="true"' ?>>
                                                <?php echo $user->name; ?> (<?php echo $user->callsign; ?>)
                                            </option>
                                    <?php
                                        }
                                    } ?>
                                </select>
                            </div>
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
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Staff</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this staff member?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    var dataSet = <?php echo json_encode($staffs); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_staff&id=" + id);
        });
        $('#staff').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "staffName",
                },
                {
                    data: "roleName",
                },
                {
                    data: "sortOrder",
                },
                {
                    data: "dateAssigned",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [2, 'asc']
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