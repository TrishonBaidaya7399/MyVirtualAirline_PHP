<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/images.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(1)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$ranks = null;
$res = Api::sendSync('GET', 'v1/ranks', null);
if ($res->getStatusCode() == 200) {
    $ranks = json_decode($res->getBody(), false);
}
$status = false;
$responseMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $icao = cleanString(trim($_POST['icao']));
    $description = cleanString(trim($_POST['description']));
    $operator = cleanString(trim($_POST['operator']));
    $pax = cleanString(trim($_POST['pax']));
    $crew = cleanString(trim($_POST['crew']));
    $cargo = cleanString(trim($_POST['cargo']));
    $mtow = cleanString(trim($_POST['mtow']));
    $mlw = cleanString(trim($_POST['mlw']));
    $mzfw = cleanString(trim($_POST['mzfw']));
    $maxalt = cleanString(trim($_POST['maxalt']));
    $wingspan = cleanString(trim($_POST['wingspan']));
    $length = cleanString(trim($_POST['length']));
    $height = cleanString(trim($_POST['height']));
    $registrations = strtoupper(str_replace(" ", "", cleanString(trim($_POST['registrations']))));
    $information = $_POST['editorContent'];
    $range = cleanString(trim($_POST['range']));
    $engine = cleanString(trim($_POST['engine']));
    $speed = cleanString(trim($_POST['speed']));
    $rankId = cleanString($_POST['rank']);
    $image_url = "";

    $editorValid = json_decode($information)->blocks != null;

    if (empty($icao) || empty($description) || empty($pax) || empty($crew) || !$editorValid) {
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
                $image_url = store_uploaded_image('photo', 350, null, 'fleet');
            }
        } else {
            $status = "rank_image_error";
        }
    }

    if (empty($status) && $valid_file) {
        if (!cargo_weight_display) {
            $cargokg = $cargo;
        } else {
            $cargokg = ($cargo / 2.205);
        }
        $data = [
            'Icao' => $icao,
            'Description' => $description,
            'Pax' => intval($pax),
            'MaxRange' => $range,
            'Cargo' => intval($cargokg),
            'Length' => intval($length),
            'Mzfw' => $mzfw,
            'Mtow' => $mtow,
            'Mlw' => $mlw,
            'MaxAlt' => intval($maxalt),
            'Speed' => intval($speed),
            'ImageUrl' => $image_url,
            'Registrations' => $registrations,
            'Notes' => $information,
            'Operator' => $operator,
            'TotalCrew' => intval($crew),
            'Wingspan' => intval($wingspan),
            'EngineModel' => $engine,
            'Height' => intval($height),
            'MinRankId' => empty($rankId) ? null : intval($rankId),
        ];

        $res = Api::sendSync('POST', 'v1/operations/aircraft', $data);

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
                            <div class="page-header-icon"><i data-feather="send"></i></div>
                            Create Aircraft
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Aircraft
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Aircraft Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Aircraft has been created.
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
                                    echo 'An error occurred when creating the aircraft. Please try again later.';
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
                <form method="post" class="form" id="editorForm" enctype="multipart/form-data">
                    <div class="row gx-3 g-5 mb-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="mb-1">ICAO *</label>
                                <input name="icao" type="text" id="icao" class="form-control" maxlength="4"
                                    placeholder="eg: B738" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Name *</label>
                                <input name="description" type="text" id="description" class="form-control"
                                    placeholder="eg: Boeing 737-800" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Operator</label>
                                <input name="operator" type="text" id="operator" class="form-control"
                                    placeholder="eg: Regional Division">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">PAX *</label>
                                <input name="pax" type="number" id="pax" class="form-control" placeholder="eg: 188"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Crew *</label>
                                <input name="crew" type="number" id="crew" class="form-control" placeholder="eg: 5"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Cargo (<?php echo fuel_weight_display ? "Lb" : "Kg" ?>)
                                    *</label>
                                <input name="cargo" type="number" step="any" id="cargo" class="form-control"
                                    placeholder="eg: 8,000" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">MTOW</label>
                                <input name="mtow" type="text" id="mtow" class="form-control"
                                    placeholder="eg: 197,000kg">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">MLW</label>
                                <input name="mlw" type="text" id="mlw" class="form-control" placeholder="eg: 144,000kg">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">MZFW</label>
                                <input name="mzfw" type="text" id="mzfw" class="form-control"
                                    placeholder="eg: 67,000kg">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Service Ceiling (ft)</label>
                                <input name="maxalt" type="number" step="any" id="maxalt" class="form-control"
                                    placeholder="eg: 41,000">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Max Speed (kts)</label>
                                <input name="speed" type="number" step="any" id="speed" class="form-control"
                                    placeholder="eg: 511">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Wingspan (meters)</label>
                                <input name="wingspan" type="number" step="any" id="wingspan" class="form-control"
                                    placeholder="eg: 34.32">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Length (meters)</label>
                                <input name="length" type="number" step="any" id="length" class="form-control"
                                    placeholder="eg: 39.50">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Height (meters)</label>
                                <input name="height" type="number" step="any" id="height" class="form-control"
                                    placeholder="eg: 12.57">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Max Range (nm)</label>
                                <input name="range" type="number" step="any" id="range" class="form-control"
                                    placeholder="eg: 4,000">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Engine Type</label>
                                <input name="engine" type="text" id="engine" class="form-control"
                                    placeholder="eg: CFM56-7B27">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="mb-1">Minimum Rank</label>
                                <select name="rank" id="rank" class="form-select">
                                    <option value="" selected="true">
                                        Any</option>
                                    <?php foreach ($ranks as $rank) { ?>
                                        <option value="<?php echo $rank->id; ?>">
                                            <?php echo $rank->name; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Aircraft Registrations In Type</label>
                                <textarea name="registrations" cols="30" rows="5" id="registrations"
                                    class="form-control" placeholder="eg: N1234,N1256,N7893"></textarea>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Registrations
                                    <u>MUST</u> be seperated by a comma
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Information *</label>
                                <div id="editorJs" class="form-control"></div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Image</label>
                                <input type="file" name="photo" class="form-control" />
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Maximum file
                                    size 2MB. 350px width image recommended.
                                </div>
                            </div>
                            <div class="mb-3">
                                <input name="editorContent" type="hidden" id="editorContent" />
                                <button id="submitButton" type="submit" class="btn btn-primary">Save
                                    Changes</button>
                            </div>
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