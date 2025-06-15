<?php
    session_start();
    
    include '../lib/functions.php';
    include '../config.php';

    validateSession();

    use Proxy\Api\Api;

    require_once __DIR__.'/../proxy/api.php';
    Api::__constructStatic();
    $status = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $current_password = cleanString($_POST['current_password']);
        $password = cleanString($_POST['password']);
        $confirm = cleanString($_POST['confirm']);
        
        if (((((empty($password)) || (empty($confirm)) || (empty($current_password)))))) {
            $status = "incomplete";
        }
        if ($password != $confirm) {
            $status = "incomplete";
        }
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            $status = "password";
        }
        if (empty($status)) {
            $data = [
                'PilotId' => $_SESSION['pilotid'],
                'NewPassword' => $password,
                'CurrentPassword' => $current_password
            ];
            $res = Api::sendSync('PUT', 'v1/account/auth/changepassword', $data);
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
					<h3 class="panel-title">Change Password</h3>
				</div>
				<div class="panel-body">
					<?php if ($status == 'success') { ?>
					<div class="alert alert-success">
						<p>Your password has been successfully changed.</p>
					</div>
					<?php } elseif ($status == "incomplete") { ?>
					<div class="alert alert-warning">
						<p>Your passwords do not match. Please check and try again.</p>
					</div>
					<?php } elseif ($status == "error") { ?>
					<div class="alert alert-warning">
						<p>There was an error changing your password. Please try again later.</p>
					</div>
					<?php } elseif ($status == "password") { ?>
					<div class="alert alert-danger">
						<p>Your password must meet the complexity requirements outlined below.</p>
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
							<label>Current Password</label>
							<input name="current_password" type="password" id="current_password" class="form-control"
								required />
						</div>
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
							<input name="change_password" type="submit" id="change_password" value="Change Password"
								class="btn btn-success" />
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
</section>
<?php include '../includes/footer.php';
