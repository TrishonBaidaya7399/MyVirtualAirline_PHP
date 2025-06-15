<?php
include '../lib/functions.php';
include '../config.php';
include '../lib/emailer.php';

use Proxy\Api\Api;

Api::__constructStatic();
session_start();
validateSession();

$status = "";
$recipient = null;
$title = null;
$message = null;
$errorMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$recipient = addslashes(trim($_POST['recipient']));
	$title = addslashes(trim($_POST['subject']));
	$message = addslashes(trim($_POST['message']));

	if (empty($recipient) || empty($title) || empty($message)) {
		$status = "required_fields";
	}

	if (empty($status)) {
		$data = [
			'receiver' => $recipient,
			'subject' => $title,
			'body' => $message,
		];
		$res = Api::sendSync('POST', 'v1/mailbox', $data);
		switch ($res->getStatusCode()) {
			case 200:
				$result = json_decode($res->getBody(), false);
				//send email notification
				$subject = virtual_airline_name . " | Message Notification";
				$message = "Dear Pilot, <br /><br />You have received a new message in your inbox."
					. "<br /><br /><a href='" . website_base_url . "site_pilot_functions/view_message.php?id=" . $result->id . "'>> View Message</a><br /><br />Kind Regards,<br />
            " . virtual_airline_name . " Team.";

				if ($_SESSION['pilotid'] == $result->receiverNavigation->id) {
					$email = $result->senderNavigation->email;
				} else {
					$email = $result->receiverNavigation->email;
				}
				echo sendEmail($subject, $message, $email);
				header('Location: ' . website_base_url . 'site_pilot_functions/view_message.php?id=' . $result->id);
				exit();
			case 400:
				$status = "error";
				$errorMessage = $res->getBody();
				break;
			default:
				$status = "error";
		}
	}
}

$recipients = null;
$res = Api::sendSync('GET', 'v1/mailbox/recipients', null);
if ($res->getStatusCode() == 200) {
	$recipients = json_decode($res->getBody(), false);
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
			<div class="col-md-12">
				<a href="<?php echo website_base_url; ?>site_pilot_functions/inbox.php"><i
						class="fa fa-angle-double-left js_showloader" aria-hidden="true"></i> Back to
					Inbox</a><br /><br />
			</div>
		</div>
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">New Message</h3>
				</div>
				<div class="panel-body">
					<?php if (!empty($status)) { ?>
						<?php if ($status == "error") { ?>
							<div class="alert alert-danger" role="alert">
								<p>
									<?php if ($status == 'error') {
										if (!empty($errorMessage)) {
											echo $errorMessage;
										} else {
											echo '<i class="fa fa-warning" aria-hidden="true"></i> An error occurred when sending the message.';
										}
									} ?>
								</p>
							</div>
						<?php } ?>
						<?php if ($status == "required_fields") { ?>
							<div class="alert alert-danger" role="alert">
								<p> <i class="fa fa-warning" aria-hidden="true"></i> Please check all fields have been
									completed
									correctly.</p>
							</div>
						<?php } ?>
					<?php } ?>
					<form method="post" class="form">
						<div class="form-group">
							<label>To</label>
							<select name="recipient" id="recipient" class="form-control" required>
								<option value="" selected="true">
									Select User
								</option>
								<?php
								if (!empty($recipients)) {
									foreach ($recipients as $user) {
								?>
										<option value="<?php echo $user->id; ?>"
											<?php echo $user->id != $recipient ? "" : 'selected="true"' ?>>
											<?php echo $user->name; ?>
										</option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="form-group">
							<label>Subject*</label>
							<input name="subject" type="text" id="subject" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Message*</label>
							<textarea name="message" cols="50" rows="10" id="message" class="form-control"
								required></textarea>
						</div>
						<div class="form-group">
							<input name="send_message" type="submit" id="send_message" value="Send"
								class="btn btn-success" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include '../includes/footer.php'; ?>