<?php

use Proxy\Api\Api;

require_once '../proxy/api.php';
include '../lib/functions.php';
include '../config.php';

session_start();
Api::__constructStatic();

if (!empty($_SESSION['pilotid'])) {
    header('Location: ' . website_base_url . 'site_pilot_functions/pilot_centre.php');
    exit();
}
$status = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = cleanString($_POST['email']);
    $password = cleanString($_POST['password']);
    if (empty($email) || empty($password)) {
        $status = "error";
    } else {
        if (empty($status)) {
            $data = [
                'Email' => $email,
                'Password' => $password
            ];
            $res = Api::sendSync('POST', 'v1/account/auth/login', $data);
            $pilot = json_decode($res->getBody());
            $responseCode = $res->getStatusCode();
            $airlineRes = getAirlineDetails();
            if ($airlineRes->getStatusCode() != 200) {
                $status = "error";
            } else {
                $airlineDetails = json_decode($airlineRes->getBody());
            }
            if ($responseCode != 200) {
                $status = "error";
            } else {
                if ($pilot->pilot->activated == '0') {
                    $status = "activate_account";
                } else {
                    Api::updateAuthAccessToken($pilot->token);
                    $_SESSION['callsign'] = $pilot->pilot->callsign;
                    $_SESSION['pilotid'] = $pilot->pilot->id;
                    $_SESSION['email'] = $pilot->pilot->email;
                    $_SESSION['name'] = $pilot->pilot->name;
                    $_SESSION['booking_expire_hours'] = $airlineDetails->bookingExpireHours;
                    $_SESSION['site_level'] = $pilot->pilot->siteLevel;
                    $_SESSION['owner'] = $pilot->pilot->owner;
                    $_SESSION['permissions'] = "";
                    $_SESSION['profileImage'] = $pilot->pilot->profileImage;
                    if (!$pilot->pilot->owner && $pilot->pilot->siteLevel && $pilot->pilot->staffPrivileges != null) {
                        $_SESSION['role_description'] = $pilot->pilot->staffPrivileges->roleDescription;
                        $_SESSION['role_name'] = $pilot->pilot->staffPrivileges->roleName;
                        $_SESSION['permissions'] = explode(",", $pilot->pilot->staffPrivileges->permissions);
                    }
                    header('Location: ' . website_base_url . 'site_pilot_functions/pilot_centre.php');
                    exit();
                }
            }
        }
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Login</h3>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($status)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php
                                if ($status == 'error') {
                                    echo '<p>Your email address and/or password are incorrect.</p>';
                                }
                                if ($status == 'activate_account') {
                                    echo '<p>Your account is inactive.</p>';
                                } ?>
                            </div>
                        <?php } ?>
                        <form method="post">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input name="email" type="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input name="password" type="password" id="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <button name="login" type="submit" id="login" class="btn btn-lg btn-success"><i
                                        class="fa fa-sign-in" aria-hidden="true"></i> Login</button>
                            </div>
                            <div class="form-group">
                                <a href="<?php echo website_base_url; ?>authentication/forgot_password.php">Reset
                                    Password</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php';
