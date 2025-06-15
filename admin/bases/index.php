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

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_base") {
    Api::sendAsync('DELETE', 'v1/operations/base/' . cleanString($_GET['id']), null);
}

$status = null;
$name = null;
$icao = null;
$desc = null;
$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanString($_POST['name']);
    $icao = cleanString($_POST['icao']);
    $desc = $_POST['editorContent'];

    $editorValid = json_decode($desc)->blocks != null;

    if (empty($name) || empty($icao) || !$editorValid) {
        $status = "required_fields";
    }

    $image_url = "";
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
            'Hub' => $name,
            'Icao' => $icao,
            'Description' => $desc,
            'ImageUrl' => $image_url,
        ];

        $res = Api::sendSync('POST', 'v1/operations/base', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $name = null;
                $icao = null;
                $desc = null;
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

$bases = null;
$res = Api::sendSync('GET', 'v1/operations/bases', null);
if ($res->getStatusCode() == 200) {
    $bases = json_decode($res->getBody(), true);
}
if (!empty($bases)) {
    foreach ($bases as &$base) {

        $base["icao"] = '<a href="' . website_base_url . 'airport_info.php?airport=' . $base["icao"] . '" target="_blank">' . $base["icao"] . '</a>';
        $base["buttons"] = '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'base_info.php?id=' . $base["id"] . '" title="View" target="_blank"><i data-feather="eye"></i></a>';
        $base["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="' . website_base_url . 'admin/bases/edit.php?id=' . $base["id"] . '" title="Edit"><i data-feather="edit"></i></a>';
        $base["buttons"] .= '<a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete" data-id="' . $base["id"] . '"><i data-feather="trash-2"></i></a>';
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
                            Bases
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Manage Bases</div>
            <div class="card-body">
                <table class="table-bordered" id="routes" style="display:none;">
                    <thead>
                        <tr>
                            <th>Name</strong></th>
                            <th>ICAO</th>
                            <th>Hours 30 Days</th>
                            <th>Flights 30 Days</th>
                            <th>Pilots</th>
                            <th>Scheduled Flights</th>
                            <th>Destinations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Create Base</div>
            <div class="card-body">
                <?php
                if ($status == 'error') {
                    if (!empty($responseMessage)) {
                        echo $responseMessage;
                    } else {
                        echo 'An error occurred when creating the base. Please try again later.';
                    }
                }
                if ($status == 'required_fields') {
                    echo 'Please check all fields have been completed correctly.';
                }
                if ($status == 'base_image_error') {
                    echo 'There was an error uploading your image. Please check it is less than 2Mb.';
                }
                ?>
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
                    <div class="mb-3">
                        <label class="mb-1">Base Image</label>
                        <input type="file" name="photo" class="form-control" />
                        <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Maximum file size
                            2MB. Recommended 400px wide.</div>
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
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Base</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this Base?</div>
                <div class="modal-footer"><button class="btn btn-light" type="button"
                        data-bs-dismiss="modal">Close</button><a href="#" class="btn btn-danger">Confirm</a>
                </div>
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
<script type="text/javascript">
    contentJson = '<?php echo $desc != null ? addslashes(preg_replace("/\r|\n/", "", $desc)) : ""; ?>';
    var dataSet = <?php echo json_encode($bases); ?>;
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            $(this).find('.btn-danger').attr("href", "?function=delete_base&id=" + id);
        });
        $('#routes').show()
        $('#routes').DataTable({
            data: dataSet,
            "pageLength": 25,
            columns: [{
                    data: "hub",
                },
                {
                    data: "icao",
                },
                {
                    data: "thirtyDayHours",
                },
                {
                    data: "thirtyDayFlights",
                },
                {
                    data: "totalPilots",
                },
                {
                    data: "totalScheduledFlights",
                },
                {
                    data: "totalDestinations",
                },
                {
                    data: "buttons",
                    "orderable": false
                }
            ],
            colReorder: false,
            "order": [
                [1, 'asc']
            ],
            "drawCallback": function(settings) {
                feather.replace();
            },
            "initComplete": function(settings, json) {

            },
        });
    });
</script>
<?php include '../includes/footer.php'; ?>