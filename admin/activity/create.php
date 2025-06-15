<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../lib/images.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(11)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
$awards = null;
$res = Api::sendAsync('GET', 'v1/awards/getawardsdropdownlist', null);
if ($res->getStatusCode() == 200) {
    $awards = json_decode($res->getBody());
}
$ranks = null;
$res = Api::sendSync('GET', 'v1/ranks', null);
if ($res->getStatusCode() == 200) {
    $ranks = json_decode($res->getBody(), false);
}
$status = null;
$awardId = null;
$title = null;
$description = null;
$type = null;
$startDate = null;
$endDate = null;
$bonusXp = null;
$active = true;
$rankId = null;
$legsInOrder = true;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = cleanString($_POST['title']);
    $description = $_POST['editorContent'];
    $type = cleanString($_POST['type']);
    $awardId = empty(cleanString($_POST['award'])) ? null : cleanString($_POST['award']);
    $bonusXp = cleanString($_POST['bonusxp']);
    $startDate = cleanString($_POST['startDate']);
    $endDate = cleanString($_POST['endDate']);
    $startHour = cleanString($_POST['startHour']);
    $startMin = cleanString($_POST['startMin']);
    $endHour = cleanString($_POST['endHour']);
    $endMin = cleanString($_POST['endMin']);
    $active = false;
    $legsInOrder = false;
    $rankId = cleanString($_POST['rank']);
    $banner_image_name = "";

    if (isset($_POST['active'])) {
        $active = boolval(cleanString($_POST['active']));
    }

    if (isset($_POST['legsinorder'])) {
        $legsInOrder = boolval(cleanString($_POST['legsinorder']));
    }

    $editorValid = json_decode($description)->blocks != null;

    if (empty($title) || empty($description) || empty($startDate) || !$editorValid) {
        $status = "required_fields";
    }

    $image_url = "";
    $valid_file = true;
    if ($_FILES['photo']['name']) {
        if (!$_FILES['photo']['error']) {
            $new_file_name = strtolower($_FILES['photo']['tmp_name']);
            if ($_FILES['photo']['size'] > (2024000)) { //can't be larger than 2 MB
                $valid_file = false;
                $status = "banner_size";
            }
            if ($valid_file) {
                $image_url = store_uploaded_image('photo', 350, null, 'activities');
            }
        } else {
            $status = "banner_error";
        }
    }

    if (empty($status) && $valid_file) {
        $data = [
            'Title' => $title,
            'Description' => $description,
            'Type' => $type,
            'Banner' => $image_url,
            'AwardId' => $awardId,
            'BonusXp' => empty($bonusXp) ? null : $bonusXp,
            'StartDate' => $startDate . 'T' . $startHour . ':' . $startMin,
            'EndDate' => empty($endDate) ? null : $endDate . 'T' . $endHour . ':' . $endMin,
            'Active' => $active,
            'LegsInOrder' => $legsInOrder,
            'MinRankId' => empty($rankId) ? null : intval($rankId),
        ];

        $res = Api::sendSync('POST', 'v1/activity', $data);

        switch ($res->getStatusCode()) {
            case 200:
                header("Location: create_leg.php?id=" . $res->getBody());
                die();
            case 400:
                $status = "error";
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
                            Create Activity
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Tours/Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php if (!empty($status)) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            if ($status == 'error') {
                echo 'An error occurred when creating the activity. Please try again later.';
            }
            if ($status == 'required_fields') {
                echo 'Please check all fields have been completed correctly.';
            }
            if ($status == 'banner_error') {
                echo 'There was an error uploading your image. Please check it is less than 2Mb.';
            }
            ?>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Activity Details</div>
            <div class="card-body">
                <form method="post" class="form" id="editorForm" enctype="multipart/form-data">
                    <div class="row gx-3 g-5 mb-3">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="mb-1">Title*</label>
                                <input name="title" type="text" id="title" class="form-control"
                                    value="<?php echo htmlspecialchars(strval($title)) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Description*</label>
                                <div id="editorJs" class="form-control"></div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Type *</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="Tour" <?php if ($type == "Tour") { ?>selected="true" <?php } ?>>
                                        Tour</option>
                                    <option value="Event" <?php if ($type == "Event") { ?>selected="true" <?php } ?>>
                                        Event</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Completion Award</label>
                                <select name="award" id="award" class="form-select">
                                    <option value="" selected="true"></option>
                                    <?php
                                    foreach ($awards as $awardItem) {
                                    ?>
                                        <option value="<?php echo $awardItem->id; ?>"
                                            <?php echo empty($awardId == $awardItem->id) ? "" : 'selected="true"' ?>>
                                            <?php echo $awardItem->name; ?>
                                        </option>
                                    <?php
                                    } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Completion Bonus XP</label>
                                <input name="bonusxp" type="number" id="bonusxp" class="form-control"
                                    value="<?php echo htmlspecialchars(strval($bonusXp)) ?>">
                            </div>
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
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="mb-1">Start Date/Time *</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="mb-1">Date</label>
                                        <input name="startDate" type="date" id="startDate"
                                            value="<?php echo $startDate ?>" maxlength="10" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-1">Hours</label>
                                        <select name="startHour" id="startHour" class="form-select">
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12" selected="true">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                            <option value="00">00</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-1">Minutes</label>
                                        <select name="startMin" id="startMin" class="form-select">
                                            <option value="00" selected="true">00</option>
                                            <option value="05">05</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="55">55</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Time is UTC
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">End Date/Time</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="mb-1">Date</label>
                                        <input name="endDate" type="date" id="endDate" value="<?php echo $endDate ?>"
                                            maxlength="10" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-1">Hours</label>
                                        <select name="endHour" id="endHour" class="form-select">
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12" selected="true">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                            <option value="00">00</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="mb-1">Minutes</label>
                                        <select name="endMin" id="endMin" class="form-select">
                                            <option value="00" selected="true">00</option>
                                            <option value="05">05</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="55">55</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Time is UTC
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Banner</label>
                                <input type="file" name="photo" class="form-control" />
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Maximum file
                                    size 2MB</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Active</label>
                                <input type="checkbox" name="active" value="1"
                                    <?php if ($active == true) { ?>checked<?php } ?>>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Legs flown in order?</label>
                                <input type="checkbox" name="legsinorder" value="1"
                                    <?php if ($legsInOrder == true) { ?>checked<?php } ?>>
                            </div>
                            <div class="mb-3">
                                <div class=" text-right">
                                    <input name="editorContent" type="hidden" id="editorContent" />
                                    <button id="submitButton" type="submit" class="btn btn-primary">Save
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
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.7"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.8"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.6"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@2.5.0"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script src="<?php echo website_base_url; ?>admin/js/editor.js"></script>
<script type="text/javascript">
    contentJson = '<?php echo $description != null ? addslashes($description) : ""; ?>';
</script>