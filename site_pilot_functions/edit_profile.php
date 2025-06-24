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

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.route-map-section {
    position: relative;
    padding: 90px 0;
    min-height: calc(100vh - 128px);
    background-image: url('../assets/images/backgrounds/world_map2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.route-map-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.route-map-section .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.offset-header {
    padding-top: 100px;
}

.route-map-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
}

.route-map-title {
    font-size: 4rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.route-map-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.route-map-header {
    padding: 30px;
    text-align: center;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.route-map-header h1 {
    font-size: 2.5rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-header hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 15px auto;
    width: 80%;
    border-radius: 2px;
}

.route-map-header p {
    font-size: 2rem;
    line-height: 1.6;
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.route-map-header strong {
    color: rgba(255, 215, 0, 1);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.route-map-header a {
    color: rgba(255, 215, 0, 1);
    text-decoration: none;
    transition: all 0.3s ease;
}

.route-map-header a:hover {
    color: rgba(255, 255, 255, 1);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}

.alert-success {
    background: rgba(0, 128, 0, 0.2);
    color: #fff;
    border: 1px solid rgba(0, 128, 0, 0.5);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
}

.alert-danger {
    background: rgba(255, 0, 0, 0.2);
    color: #fff;
    border: 1px solid rgba(255, 0, 0, 0.5);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
}

.form-group label {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 5px;
    display: block;
    text-align: left;
}

.form-control {
    background: rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 6px;
    color: rgba(0, 0, 0, 0.9);
    padding: 5px;
    width: 100%;
    box-sizing: border-box;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.5);
    border-color: rgba(255, 215, 0, 0.6);
    outline: none;
    box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
}

.help-block {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 5px;
}

.btn-success {
    background: linear-gradient(45deg, rgba(0, 128, 0, 0.8), rgba(0, 100, 0, 0.9));
    border: 1px solid rgba(0, 128, 0, 0.5);
    color: #fff;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-success:hover {
    background: linear-gradient(45deg, rgba(0, 128, 0, 1), rgba(0, 100, 0, 1));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 128, 0, 0.4);
    color: #fff;
}

.pilot-profile-image {
    border: 4px solid rgba(255, 215, 0, 0.5);
    border-radius: 50%;
    margin-bottom: 15px;
}

.profile {
    font-size: 10rem;
    color: rgba(255, 215, 0, 0.8);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

@media (max-width: 1200px) {
    .route-map-section {
        padding: 80px 0;
    }
    .route-map-title {
        font-size: 2.5rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .global-heading .global-title {
        font-size: 35px;
    }
    .form-control {
        font-size: 14px;
    }
}

@media (max-width: 992px) {
    .route-map-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 80px;
    }
    .route-map-title {
        font-size: 2.2rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .global-heading .global-title {
        font-size: 30px;
    }
    .route-map-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
}

@media (max-width: 768px) {
    .route-map-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .route-map-title {
        font-size: 2rem;
    }
    .route-map-icon {
        font-size: 1.8rem;
    }
    .global-heading .global-title {
        font-size: 25px;
    }
    .route-map-header {
        padding: 25px 20px;
    }
    .route-map-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    .form-group label {
        font-size: 14px;
    }
    .form-control {
        font-size: 13px;
        padding: 8px;
    }
    .btn-success {
        padding: 8px 15px;
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .route-map-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 50px;
    }
    .route-map-title {
        font-size: 1.8rem;
    }
    .route-map-icon {
        font-size: 1.5rem;
    }
    .global-heading .global-title {
        font-size: 20px;
    }
    .route-map-header {
        padding: 20px 15px;
    }
    .route-map-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    .form-group {
        margin-bottom: 10px;
    }
    .form-group label {
        font-size: 13px;
    }
    .form-control {
        font-size: 12px;
        padding: 6px;
    }
    .btn-success {
        padding: 6px 12px;
        font-size: 12px;
    }
    .pilot-profile-image {
        width: 150px;
    }
    .profile {
        font-size: 8rem;
    }
}

@media (max-width: 480px) {
    .route-map-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 40px;
    }
    .route-map-title {
        font-size: 1.6rem;
    }
    .route-map-icon {
        font-size: 1.3rem;
    }
    .global-heading .global-title {
        font-size: 18px;
    }
    .form-group label {
        font-size: 12px;
    }
    .form-control {
        font-size: 11px;
        padding: 5px;
    }
    .btn-success {
        padding: 5px 10px;
        font-size: 11px;
    }
    .pilot-profile-image {
        width: 120px;
    }
    .profile {
        font-size: 6rem;
    }
}

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 25px;
    }
}

@media print {
    .route-map-section {
        background: white;
        padding: 20px 0;
    }
    .route-map-section::before {
        display: none;
    }
    .route-map-title,
    .route-map-header h1,
    .route-map-header p,
    .form-group label,
    .form-control,
    .alert-success,
    .alert-danger {
        color: rgba(255,255,255,0.9);
        text-shadow: none;
    }
    .route-map-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
}
</style>

<section id="content" class="section route-map-section">
    <div class="container">
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title"><i class="fa fa-edit"></i> Edit Profile</h3>
                </div>
                <div class="route-map-glass-card">
                    <div class="route-map-header">
                        <?php if ($status == "success") {?>
                        <div class="alert alert-success">Pilot has been successfully edited.</div>
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
                        <form method="post" class="form" enctype="multipart/form-data" style="width: 100%; margin-left: 0px; margin-right: 0px;">
                            <div class="row" style="width: 100%; margin-left: 0px; margin-right: 0px;">
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
                                <div class="col-md-6">
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
                                </div>
                            </div>
                            <hr/>
                            <input name="profile" type="submit" id="profile" value="Update Profile"
                                           class="btn btn-success">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php';