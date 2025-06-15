<?php
use Proxy\Api\Api;

session_start();

require_once __DIR__.'/../proxy/api.php';
include '../lib/functions.php';
include '../config.php';
$status = null;
    if (!empty($_SESSION['pilotid'])) {
        header('Location: pilot_centre.php');
        exit();
    }
    $token = "";
    Api::__constructStatic();
    
        if (isset($_GET['token'])) {
            $token = addslashes(trim($_GET['token']));
            $res = Api::sendSync('POST', 'v1/account/auth/passwordtokenvalid', $token);
            if ($res->getStatusCode() != 200) {
                $status = "expired";
            }
        }

    if (isset($_POST['change_password'])) {
        $token = cleanString($_POST['token']);
        $password = cleanString($_POST['password']);
        $confirm = cleanString($_POST['confirm']);

        if (((((empty($password)) || (empty($confirm)))))) {
            $status = "required";
        }
        if ($password != $confirm) {
            $status = "required";
        }
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            $status = "password";
            exit;
        }
        if (empty($status)) {
            $data = [
            'Password' => $password,
            'token' => $token
        ];
            $res = Api::sendSync('PUT', 'v1/account/auth/resetpassword', $data);
            if ($res->getStatusCode() != 200) {
                $status = "error";
            } else {
                $status = "success";
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
                    <h3 class="panel-title">Reset Password</h3>
                </div>
                <div class="panel-body">
                    <?php if ($status == 'success') { ?>
                    <div class="alert alert-success">
                        <p>Your password has been successfully reset. <a href="login.php">Click here to login.</a> </p>
                    </div>
                    <?php } elseif ($status == "error") { ?>
                    <div class="alert alert-warning">
                        <p>There was an error resetting your password. Please try again later.</p>
                    </div>
                    <?php } elseif ($status == "expired") { ?>
                    <div class="alert alert-danger">
                        <p>Your reset password link has expired. Please request a new one by <a
                                href="forgot_password.php">clicking here</a></p>
                    </div>
                    <?php } else { ?>
                    <?php if ($status == "password") { ?>
                    <div class="alert alert-danger">
                        <p>Your password must meet the complexity requirements outlined below.</p>
                    </div>
                    <?php } elseif ($status == "required") { ?>
                    <div class="alert alert-danger">
                        <p>Please check all fields are complete and your passwords match.</p>
                    </div>
                    <?php } ?>
                    <p>Your password must Contain at least:
                    <ul>
                        <li>1 upper case letter</li>
                        <li>1 lower case letter</li>
                        <li>1 number</li>
                        <li>8 characters</li>
                    </ul>
                    </p>
                    <form method="post" class="form">
                        <div class="form-group">
                            <label>New Password</label>
                            <input name="password" type="password" id="password" class="form-control"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" maxlength="30" required />
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input name="confirm" type="password" id="confirm" class="form-control"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" maxlength="30" required />
                        </div>
                        <div class="form-group">
                            <input name="token" type="hidden" id="token"
                                value="<?php echo htmlspecialchars(strval($token));?>" />
                            <input name="change_password" type="submit" id="change_password" value="Change Password"
                                class="btn btn-success" />
                        </div>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php';