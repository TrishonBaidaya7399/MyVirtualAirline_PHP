<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

if (!userHasPermission(2)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}
Api::__constructStatic();
$id = cleanString($_GET['id']);
$status = null;
$hubs = null;
$res = Api::sendSync('GET', 'v1/operations/bases', null);
if ($res->getStatusCode() == 200) {
    $hubs = json_decode($res->getBody(), false);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = cleanString($_POST['id']);
    $name = cleanString($_POST['name']);
    $email = cleanString($_POST['pilotemail']);
    $callsign = strtoupper(cleanString($_POST['callsign']));
    $location = cleanString($_POST['location']);
    $password = cleanString($_POST['pilotpassword']);
    $background = cleanString($_POST['background']);
    $accountstatus = cleanString($_POST['status']);
    $activated = cleanString($_POST['Activated']);
    $selectedhub = cleanString($_POST['hub']);
    $vatsim_id = cleanString($_POST['vatsim_id']);
    $ivao_id = cleanString($_POST['ivao_id']);
    $admincomments = cleanString($_POST['admincomments']);
    $facebookLink = cleanString($_POST['facebooklink']);
    $youtubeLink = cleanString($_POST['youtubelink']);
    $twitterLink = cleanString($_POST['twitterlink']);
    $skypeLink = cleanString($_POST['skypelink']);
    $custom1 = cleanString($_POST['custom1']);
    $custom2 = cleanString($_POST['custom2']);
    $custom3 = cleanString($_POST['custom3']);
    $custom4 = cleanString($_POST['custom4']);
    $custom5 = cleanString($_POST['custom5']);
    $currentLocation = cleanString($_POST['currentlocation']);
    if (empty($status)) {
        $data = [
            'Id' => $id,
            'Name' => $name,
            'Email' => $email,
            'Callsign' => $callsign,
            'Location' => $location,
            'Password' => $password,
            'Background' => $background,
            'Status' => $accountstatus,
            'Activated' => $activated == 'true' ? true : false,
            'HubId' => $selectedhub == "" ? null : $selectedhub,
            'VatsimId' => $vatsim_id,
            'IvaoId' => $ivao_id,
            'AdminComments' => $admincomments,
            'FacebookLink' => $facebookLink,
            'YoutubeLink' => $youtubeLink,
            'TwitterLink' => $twitterLink,
            'SkypeLink' => $skypeLink,
            'Custom1' => $custom1,
            'Custom2' => $custom2,
            'Custom3' => $custom3,
            'Custom4' => $custom4,
            'Custom5' => $custom5,
            'CurrentLocation' => $currentLocation,
        ];
        $res = Api::sendSync('PUT', 'v1/pilot', $data);
        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                break;
            case 400:
                switch (json_decode($res->getBody())->message) {
                    case "Email":
                        $status = "email";
                        break;
                    case "Callsign":
                        $status = "callsign";
                        break;
                    default:
                        $status = "error";
                        break;
                }
                break;
            default:
                $status = "error";
        }
    }
}
$res = Api::sendSync('GET', 'v1/pilot/' . $id, null);
$pilot = json_decode($res->getBody());
$responseCode = $res->getStatusCode();
if ($responseCode != 200) {
    echo '<script>alert("Pilot does not exist.");</script>';
    echo '<script>history.back(1);</script>';
    exit;
}
$id = $pilot->id;
$name = $pilot->name;
$email = $pilot->email;
$callsign = $pilot->callsign;
$location = $pilot->location;
$background = $pilot->background;
$accountstatus = $pilot->status;
$activated = $pilot->activated == true ? 'true' : 'false';
$selectedhub = $pilot->hub;
$vatsim_id = $pilot->vatsimId;
$ivao_id = $pilot->ivaoId;
$admincomments = $pilot->adminComments;
$facebookLink = $pilot->facebookLink;
$youtubeLink = $pilot->youtubeLink;
$twitterLink = $pilot->twitterLink;
$skypeLink = $pilot->skypeLink;
$custom1 = $pilot->custom1;
$custom2 = $pilot->custom2;
$custom3 = $pilot->custom3;
$custom4 = $pilot->custom4;
$custom5 = $pilot->custom5;
$currentLocation = $pilot->currentLocation;
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
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            Edit Pilot - <?php echo $name; ?>
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Pilot List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card">
            <div class="card-header">Account Details</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                <div class="alert alert-success alert-dismissible fade show">Pilot has been successfully updated.
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php } else { ?>
                <?php if (!empty($status)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                            if ($status == 'error') {
                                echo 'An error occurred updating the pilot. Please try again later.';
                            }
                            if ($status == 'callsign') {
                                echo 'Callsign already in use. Please use a different one.';
                            }
                            if ($status == 'email') {
                                echo 'Email is invalid or is being used by another pilot.';
                            }
                            ?>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php } ?>
                <?php } ?>
                <form method="post">
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="mb-1">Name*</label>
                                <input name="name" type="text" id="name" class="form-control"
                                    value="<?php echo $name; ?>" required>
                                <input name="id" type="hidden" id="id" value="<?php echo $id; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Email Address*</label>
                                <input name="pilotemail" type="text" id="pilotemail" class="form-control"
                                    value="<?php echo $email; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Location*</label>
                                <select name="location" class="form-control">
                                    <OPTION value="<?php echo $location; ?>" selected><?php echo $location; ?>
                                    </OPTION>
                                    <?php include '../../lib/country-list.php'; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Pilot Id*</label>
                                <input name="callsign" type="text" id="callsign" class="form-control"
                                    value="<?php echo $callsign; ?>" maxlength="8" style="text-transform: uppercase;"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Current Virtual Location</label>
                                <input name="currentlocation" type="text" id="currentlocation" maxlength="4"
                                    class="form-control" placeholder="ICAO" value="<?php echo $currentLocation; ?>" />
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Leave blank
                                    to
                                    allow pilot to start from anywhere. Feature only
                                    applicable when Limit Departure Location is set to true in airline settings.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Vatsim ID</label>
                                <input name="vatsim_id" type="text" id="vatsim_id" maxlength="15" class="form-control"
                                    value="<?php echo $vatsim_id; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">IVAO ID</label>
                                <input name="ivao_id" type="text" id="ivao_id" maxlength="15" class="form-control"
                                    value="<?php echo $ivao_id; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Select Base</label>
                                <select name="hub" id="hub" class="form-select">
                                    <option value="" <?php echo !empty($pilot->hubId) ? "" : 'selected="true"' ?>>
                                        Unassigned
                                    </option>
                                    <?php
                                    foreach ($hubs as $hub) {
                                    ?>
                                    <option value="<?php echo $hub->id; ?>"
                                        <?php echo $pilot->hubId != $hub->id ? "" : 'selected="true"' ?>>
                                        <?php echo $hub->hub; ?> (<?php echo $hub->icao; ?>)
                                    </option>
                                    <?php
                                    } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">New Password</label>
                                <input name="pilotpassword" type="password" id="pilotpassword" class="form-control"
                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" maxlength="30">
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Leave
                                    password
                                    field blank to not reset the password</div>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Facebook Link</label>
                                <input name="facebooklink" type="text" id="facebooklink" class="form-control"
                                    value="<?php echo $facebookLink; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Youtube Link</label>
                                <input name="youtubelink" type="text" id="youtubelink" class="form-control"
                                    value="<?php echo $youtubeLink; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Twitter Link</label>
                                <input name="twitterlink" type="text" id="twitterlink" class="form-control"
                                    value="<?php echo $twitterLink; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Discord</label>
                                <input name="skypelink" type="text" id="skypelink" class="form-control"
                                    value="<?php echo $skypeLink; ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="mb-1">Custom Field 1</label>
                                <input name="custom1" type="text" id="custom1" class="form-control"
                                    value="<?php echo $custom1; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Custom Field 2</label>
                                <input name="custom2" type="text" id="custom2" class="form-control"
                                    value="<?php echo $custom2; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Custom Field 3</label>
                                <input name="custom3" type="text" id="custom3" class="form-control"
                                    value="<?php echo $custom3; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Custom Field 4</label>
                                <input name="custom4" type="text" id="custom4" class="form-control"
                                    value="<?php echo $custom4; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Custom Field 5</label>
                                <input name="custom5" type="text" id="custom5" class="form-control"
                                    value="<?php echo $custom5; ?>" />
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Background</label>
                                <textarea name="background" rows="5" id="background"
                                    class="form-control"><?php echo $background; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Admin Comments (for admin use only)</label>
                                <textarea name="admincomments" rows="5" id="admincomments"
                                    class="form-control"><?php echo htmlspecialchars(strval($admincomments)); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Pilot Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1" <?php echo $accountstatus == '1' ? "selected='true'" : "" ?>>
                                        Active
                                    </option>
                                    <option value="0" <?php echo $accountstatus == '0' ? "selected='true'" : "" ?>>On
                                        Leave
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="mb-1">Account Active</label>
                                <select name="Activated" id="Activated" class="form-select">
                                    <option value="true" <?php echo $activated == 'true' ? "selected='true'" : "" ?>>Yes
                                    </option>
                                    <option value="false" <?php echo $activated == 'false' ? "selected='true'" : "" ?>>
                                        No
                                    </option>
                                </select>
                                <div class="small font-italic text-muted mb-3"><i data-feather="info"></i> Use this to
                                    suspend a pilot, they will
                                    appear as pending
                                </div>
                            </div>
                            <div class="mb-3">
                                <div>
                                    <?php if ($_SESSION['owner'] != 1 && $pilot->owner == 1) { ?>
                                    <?php } else { ?>
                                    <button type="submit" id="submit" class="btn btn-primary">Save Changes</button>
                                    <?php } ?>
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