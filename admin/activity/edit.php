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
$activityId = cleanString($_GET['id']);
$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_leg") {
    Api::sendAsync('DELETE', 'v1/activity/leg/' . cleanString($_GET['leg_id']), null);
}
$status = null;
$activity = null;
$res = Api::sendAsync('GET', 'v1/activity/' . $activityId, null);
if ($res->getStatusCode() == 200) {
    $activity = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'admin/activity/');
    die();
}
$ranks = null;
$res = Api::sendSync('GET', 'v1/ranks', null);
if ($res->getStatusCode() == 200) {
    $ranks = json_decode($res->getBody(), false);
}
$awards = null;
$res = Api::sendAsync('GET', 'v1/awards/getawardsdropdownlist', null);
if ($res->getStatusCode() == 200) {
    $awards = json_decode($res->getBody());
}
$title = $activity->title;
$description = $activity->description;
$type = $activity->type;
$awardId = $activity->awardId;
$bonusXp = $activity->bonusXp;
$startDateCal = new DateTime($activity->startDate);
$endDateCal = null;
if (!empty($activity->endDate)) {
    $endDateCal = new DateTime($activity->endDate);
}
$active = $activity->active;
$legsInOrder = $activity->legsInOrder;
$image_url = $activity->banner;
$legs = $activity->activityLegs;
$rankId = $activity->minRankId;
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
    $startDateCal = new DateTime($startDate . 'T' . $startHour . ':' . $startMin);
    $endDateCal = null;
    $active = false;
    $legsInOrder = false;
    $rankId = cleanString($_POST['rank']);
    if (!empty($endDate)) {
        $endDateCal = new DateTime($endDate . 'T' . $endHour . ':' . $endMin);
    }
    if (isset($_POST['deleteimage'])) {
        $image_url = "";
    }
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
    if (!empty($_FILES['photo'])) {
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
    }
    if (empty($status) && $valid_file) {
        $data = [
            'Id' => $activityId,
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
        $res = Api::sendSync('PUT', 'v1/activity', $data);
        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                break;
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
                            Edit Activity - <?php echo htmlspecialchars(strval($title)) ?>
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
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">Activity Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">Activity has been successfully editted.
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } else { ?>
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
                <?php } ?>
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
                                <label class="mb-1">Type</label>
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
                                    <option value="" <?php echo !empty($awardId) ? "" : 'selected="true"' ?>>No
                                        award given</option>
                                    <?php
                                    foreach ($awards as $awardItem) {
                                    ?>
                                        <option value="<?php echo $awardItem->id; ?>"
                                            <?php echo $awardId == $awardItem->id ? 'selected="true"' : ""; ?>>
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
                                    <option value="" <?php echo !empty($rankId) ? "" : 'selected="true"' ?>>
                                        Any</option>
                                    <?php foreach ($ranks as $rank) { ?>
                                        <option value="<?php echo $rank->id; ?>"
                                            <?php echo $rank->id != $rankId ? "" : 'selected="true"' ?>>
                                            <?php echo $rank->name; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Start Date/Time*</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="mb-1">Date</label>
                                        <input name="startDate" type="date" id="startDate"
                                            value="<?php echo $startDateCal->format('Y-m-d'); ?>" maxlength="10"
                                            class="form-control" required>
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
                                            <option value="12">12</option>
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
                                            <option value="00">00</option>
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
                                        <input name="endDate" type="date" id="endDate" <?php if ($endDateCal != null) {
                                                                                            echo 'value="' . $endDateCal->format('Y-m-d') . '"';
                                                                                        } ?> maxlength="10"
                                            class="form-control">
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
                                            <option value="12">12</option>
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
                                            <option value="00">00</option>
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
                        </div>
                        <div class="col-lg-6">
                            <?php if (!empty($image_url)) { ?>
                                <div class="mb-3">
                                    <label class="mb-1">Current Banner:</label>
                                    <br />
                                    <img src="<?php echo website_base_url ?>uploads/activities/<?php echo $image_url; ?>"
                                        width="400" height="400" />
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Delete current banner?</label>
                                    <input type="checkbox" name="deleteimage" value="1">
                                </div>
                            <?php } ?>
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
                                <input name="editorContent" type="hidden" id="editorContent" />
                                <button type="submit" id="submitButton" class="btn btn-primary">Save
                                    Changes</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-header-actions mb-4">
            <div class="card-header">
                Activity Legs
                <a href="<?php echo website_base_url; ?>admin/activity/create_leg.php?id=<?php echo $activityId; ?>"
                    class="btn btn-primary btn-sm">Create Leg</a>
            </div>
            <div class="card-body">

                <table class="table-bordered" id="legs">
                    <thead>
                        <tr>
                            <th>Leg ID</th>
                            <th>Departure ICAO</th>
                            <th>Arrival ICAO</th>
                            <th>Flight Number</th>
                            <th>Aircraft</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($legs as $key => $leg) {
                        ?>
                            <tr>
                                <td><?php echo $key + 1; ?>
                                </td>
                                <td><span class='badge bg-blue-soft text-blue'><?php echo $leg->departureIcao; ?></span>
                                </td>
                                <td><span class='badge bg-blue-soft text-blue'><?php echo $leg->arrivalIcao; ?></span>
                                </td>
                                <td><?php echo empty($leg->flightNumber) ? "N/A" : $leg->flightNumber; ?>
                                </td>
                                <td><?php echo empty($leg->aircraft) ? "N/A" : $leg->aircraft; ?>
                                </td>
                                <td>
                                    <a class="btn btn-datatable btn-icon btn-transparent-dark me-2"
                                        href="<?php echo website_base_url; ?>activity_leg.php?id=<?php echo $leg->id; ?>"><i
                                            data-feather="eye"></i></a>
                                    <a class="btn btn-datatable btn-icon btn-transparent-dark me-2"
                                        href="<?php echo website_base_url; ?>admin/activity/edit_leg.php?leg_id=<?php echo $leg->id; ?>"><i
                                            data-feather="edit"></i></a>

                                    <a class="btn btn-datatable btn-icon btn-transparent-dark me-2" href="#"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="<?php echo $leg->id; ?>" data-activityid="<?php echo $activityId; ?>"><i
                                            data-feather="trash-2"></i></a>

                                </td>
                            </tr>
                        <?php
                        } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Leg</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this leg?</div>
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
    contentJson = '<?php echo $description != null ? addslashes(preg_replace("/\r|\n/", "", $description)) : ""; ?>';
    $(document).ready(function() {
        $('#legs').DataTable({
            colReorder: false,
            ordering: false
        });

        $('#deleteModal').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            var activityId = $(event.relatedTarget).data('activityid');
            $(this).find('.btn-danger').attr("href", '?function=delete_leg&leg_id=' + id + '&id=' +
                activityId);
        });
        $("#startHour")
            .val(
                "<?php echo $startDateCal->format('H'); ?>"
            );
        $("#startMin")
            .val(
                "<?php echo $startDateCal->format('i'); ?>"
            );
        var endHour = '12';
        var endMin = '00';
        <?php if ($endDateCal != null) { ?>
            endHour =
                '<?php echo $endDateCal->format('H'); ?>';
            endMin =
                '<?php echo $endDateCal->format('i'); ?>';
        <?php } ?>
        $("#endHour")
            .val(endHour);
        $("#endMin")
            .val(endMin);
        feather.replace();
    });
</script>
<?php include '../includes/footer.php'; ?>