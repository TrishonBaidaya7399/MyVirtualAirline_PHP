<?php
use Proxy\Api\Api;

require_once __DIR__.'/../proxy/api.php';
include '../lib/functions.php';
include '../config.php';
include '../lib/emailer.php';

if (!empty($_SESSION['pilotid'])) {
    header('Location: pilot_centre.php');
    exit();
}
Api::__constructStatic();
$status = null;
if (isset($_POST['forgot_password'])) {
    $email = addslashes(trim($_POST['email']));
    if (empty($email)) {
        $status = "required";
    }
    if (empty($status)) {
        $res = Api::sendSync('POST', 'v1/account/auth/forgottenpasswordtoken', $email);
        $token = json_decode($res->getBody());

        if ($res->getStatusCode() == 200 && !empty($token)) {
            $to = $email;
            $subject ="".virtual_airline_name." | Reset Password Instructions";
            $message = "
		Please click the link below within 2 hours to reset your password.<br /><br />If you did not request to reset your password please ignore this email.
		<br /><br />
		<a href='".website_base_url."/authentication/reset_password.php?token=".$token."'>Reset Password</a>
		<br /><br />Kind Regards,<br />
		".virtual_airline_name." Team.";

            echo sendEmail($subject, $message, $to);
        }
        $status = "sent";
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
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Reset Password</h3>
					</div>
					<div class="panel-body">
						<?php if ($status == 'sent') { ?>
						<div class="alert alert-success">
							<p>If you have an account with us you will receive instructions via email on how to reset
								your password.</p>
						</div>
						<?php } else { ?>
						<?php if ($status == 'required') { ?>
						<div class="alert alert-danger">
							<p>Please enter an email address.</p>
						</div>
						<?php } ?>
						<form method="post">
							<div class="form-group">
								<label>Email Address</label>
								<input name="email" type="text" id="email" class="form-control" required />
							</div>
							<div class-="form-group">
								<input name="forgot_password" class="btn btn-success" type="submit" id="forgot_password"
									value="Reset Password" />
							</div>
						</form>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include '../includes/footer.php';
