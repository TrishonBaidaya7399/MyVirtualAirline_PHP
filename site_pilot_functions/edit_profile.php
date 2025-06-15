<?php
use Proxy\Api\Api;

require_once '../proxy/api.php';
include '../lib/images.php';
include '../lib/functions.php';
include '../config.php';

session_start();
validateSession();

$id = $_SESSION['pilotid'];
Api::__constructStatic();

$status = null;
$errormessage = null;
$res = Api::sendSync('GET', 'v1/pilot/' . $id, null);
$pilot = json_decode($res->getBody());
$name = $pilot->name;
$location = $pilot->location;
$email = $pilot->email;
$background = $pilot->background;
$vatsim_id = $pilot->vatsimId;
$ivao_id = $pilot->ivaoId;
$facebook = $pilot->facebookLink;
$youtube = $pilot->youtubeLink;
$twitter = $pilot->twitterLink;
$skype = $pilot->skypeLink;
$custom1 = $pilot->custom1;
$custom2 = $pilot->custom2;
$custom3 = $pilot->custom3;
$custom4 = $pilot->custom4;
$custom5 = $pilot->custom5;
$profile_image_name = $pilot->profileImage;
$hubId = $pilot->hubId;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanString($_POST['fullname']);
    $location = cleanString($_POST['location']);
    $email = cleanString($_POST['email']);
    $background = cleanString($_POST['background']);
    $vatsim_id = cleanString($_POST['vatsim_id']);
    $ivao_id = cleanString($_POST['ivao_id']);
    $facebook = cleanString($_POST['facebook']);
    $youtube = cleanString($_POST['youtube']);
    $twitter = cleanString($_POST['twitter']);
    $skype = cleanString($_POST['skype']);
    $custom1 = cleanString($_POST['custom1']);
    $custom2 = cleanString($_POST['custom2']);
    $custom3 = cleanString($_POST['custom3']);
    $custom4 = cleanString($_POST['custom4']);
    $custom5 = cleanString($_POST['custom5']);

    $valid_file = true;
    if ($_FILES['photo']['name']) {
        if (!$_FILES['photo']['error']) {
            $new_file_name = strtolower($_FILES['photo']['tmp_name']);
            if ($_FILES['photo']['size'] > (2024000)) { //can't be larger than 2 MB
                $valid_file = false;
                $errormessage = 'Oops!  Your file size is to large, maximum 2mb.';
            }
            if ($valid_file) {
                $profile_image_name = store_uploaded_image('photo', 400, null, 'profiles');
            }
        } else {
            $errormessage = 'Oops! Your upload triggered the following error: ' . $_FILES['photo']['error'];
        }
    }
    if ($valid_file == false) {
        $status = "file";
    }
    if (empty($status)) {
        $data = [
            'Id' => $id,
            'Name' => $name,
            'Email' => $email,
            'Location' => $location,
            'Background' => $background,
            'Activated' => $pilot->activated,
            'VatsimId' => $vatsim_id,
            'IvaoId' => $ivao_id,
            'AdminComments' => $pilot->adminComments,
            'FacebookLink' => $facebook,
            'YoutubeLink' => $youtube,
            'TwitterLink' => $twitter,
            'SkypeLink' => $skype,
            'Custom1' => $custom1,
            'Custom2' => $custom2,
            'Custom3' => $custom3,
            'Custom4' => $custom4,
            'Custom5' => $custom5,
            'ProfileImage' => $profile_image_name,
            'HubId' => $hubId,
        ];
        $res = Api::sendSync('PUT', 'v1/pilot', $data);
        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                break;
            case 400:
                switch (json_decode($res->getBody())->Message) {
                    case "Email":
                        $status = "email";
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
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php';?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Edit Profile</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($status == "success") {?>
                            <div class="alert alert-success">Pilot has been successfully editted.</div>
                            <?php } else {?>
                            <?php if (!empty($status)) {?>
                            <div class="alert alert-danger" role="alert">
                                <?php
if ($status == 'error') {
    echo '<p>An error occurred updating your profile. Please try again later.</p>';
}
    if ($status == 'file') {
        echo '<p>' . $errormessage . '</p>';
    }
    if ($status == 'email') {
        echo '<p>Email address is invalid or already in use.</p>';
    }
    ?>
                            </div>
                            <?php }?>
                            <?php }?>
                            <form method="post" class="form" enctype="multipart/form-data">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name*</label>
                                        <input name="fullname" minlength="2" type="text" id="fullname"
                                            value="<?php echo $name; ?>" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Location</label>
                                        <select name="location" class="form-control">
                                            <OPTION value="<?php echo $location; ?>" SELECTED class="form-control">
                                                <?php echo $location; ?>
                                            </OPTION>
                                            <?php include '../lib/country-list.php';?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address*</label>
                                        <input name="email" type="email" id="email" value="<?php echo $email; ?>"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Vatsim Id</label>
                                        <input name="vatsim_id" type="text" id="vatsim_id"
                                            value="<?php echo $vatsim_id; ?>" class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>IVAO Id</label>
                                        <input name="ivao_id" type="text" id="ivao_id" value="<?php echo $ivao_id; ?>"
                                            class="form-control" maxlength="15">
                                    </div>
                                    <div class="form-group">
                                        <label>Facebook Link</label>
                                        <input name="facebook" type="text" id="facebook"
                                            value="<?php echo $facebook; ?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>YouTube Link</label>
                                        <input name="youtube" type="text" id="youtube" value="<?php echo $youtube; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Twitter Link</label>
                                        <input name="twitter" type="text" id="twitter" value="<?php echo $twitter; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Discord</label>
                                        <input name="skype" type="text" id="skype" value="<?php echo $skype; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Background</label>
                                        <textarea name="background" rows="5" id="background"
                                            class="form-control"><?php echo htmlspecialchars(strval($background)); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Custom Field 1</label>
                                        <input name="custom1" type="text" id="custom1" value="<?php echo $custom1; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Custom Field 2</label>
                                        <input name="custom2" type="text" id="custom2" value="<?php echo $custom2; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Custom Field 3</label>
                                        <input name="custom3" type="text" id="custom3" value="<?php echo $custom3; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Custom Field 4</label>
                                        <input name="custom4" type="text" id="custom4" value="<?php echo $custom4; ?>"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Custom Field 5</label>
                                        <input name="custom5" type="text" id="custom5" value="<?php echo $custom5; ?>"
                                            class="form-control">
                                    </div>
                                    <?php if ($profile_image_name != "") {?>
                                    <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $profile_image_name ?>"
                                        width="200" class="img-circle pilot-profile-image" />
                                    <?php } else {?>
                                    <i class="fa fa-user-circle profile" aria-hidden="true"></i>
                                    <?php }?>
                                    <hr />
                                    <div class="form-group">
                                        <label>Upload Profile Photo</label>
                                        <input type="file" name="photo" class="form-control"
                                            accept=".jpg,.png,.gif,.jpeg" />
                                        <p class="help-block">Maximum file size 2MB</p>
                                    </div>
                                    <div class="form-group">
                                        <input name="profile" type="submit" id="profile" value="Update Profile"
                                            class="btn btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php';