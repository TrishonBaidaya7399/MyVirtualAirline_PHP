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
<link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/datatables.min.css" />

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}
.row {
    width: 100%;
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
    color: rgba(0, 0, 0, 0.9);
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

.flights-table-glass-card {
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
	padding: 15px;
    max-width: 100%;
}

.flights-table-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.flights-table-header {
    background: rgba(255, 255, 255, 0.9);
    color: rgba(255, 255, 255, 1);
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.flights-table-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    gap: 10px;
}

.flights-table-header .fa-plane {
    color: rgba(255, 215, 0, 1);
    font-size: 1.5rem;
}

.form-group label {
    font-weight: bold;
    color: #fff;
    margin-bottom: 5px;
    display: block;
}

.form-control {
    background: rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: #fff;
    padding: 5px;
    width: 100%;
    box-sizing: border-box;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.5);
    outline: none;
    box-shadow: 0 0 5px rgba(255, 215, 0, 0.5);
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

.alert {
    background: rgba(255, 0, 0, 0.2);
    border: 1px solid rgba(255, 0, 0, 0.5);
    color: #fff;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert i {
    margin-right: 5px;
}
hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 5px auto;
    width: 100%;
    border-radius: 2px;
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
        padding: 8px;
    }
    .btn-success {
        padding: 8px 18px;
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
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    .form-control {
        padding: 6px;
    }
    .btn-success {
        padding: 6px 16px;
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
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    .form-control {
        padding: 5px;
    }
    .btn-success {
        padding: 5px 14px;
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
    .route-map-glass-card,
    .flights-table-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    .form-control {
        padding: 4px;
    }
    .btn-success {
        padding: 4px 12px;
        font-size: 12px;
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
    .form-control {
        padding: 3px;
    }
    .btn-success {
        padding: 3px 10px;
        font-size: 11px;
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
    .route-map-header p {
        color: black;
        text-shadow: none;
    }
    .route-map-glass-card,
    .flights-table-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    .form-control,
    .btn-success,
    .alert {
        background: white;
        color: black;
        border-color: #ccc;
    }
}
</style>

<section id="content" class="section route-map-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="global-heading" style="display: flex; justify-content: space-between; align-items: center; flex-direction: row; gap: 20px; width: 100%; margin-bottom: 10px">
                    <h3 class="global-title"><i class="fa fa-inbox"></i> Inbox</h3>
                    <a style="color: #fff" href="<?php echo website_base_url; ?>site_pilot_functions/inbox.php"><i
                        class="fa fa-angle-double-left js_showloader" aria-hidden="true"></i> Back to
                        Inbox</a>
                </div>
				<hr/>
                <div class="global-heading" style="margin-bottom: 20px;">
                    <h3 class="global-title">New Message</h3>
                </div>
                <div class="flights-table-glass-card">
                    <div class="flights-table-wrapper">
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
                                        completed correctly.</p>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <form method="post" class="form">
                            <div class="form-group">
                                <label>To</label>
                                <select name="recipient" id="recipient" class="form-control" required>
                                    <option value="" selected="true" style="color: #000">
                                        Select User
                                    </option>
                                    <?php
                                    if (!empty($recipients)) {
                                        foreach ($recipients as $user) {
                                    ?>
                                            <option style="color: #000" value="<?php echo $user->id; ?>"
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
    </div>
</section>
<?php include '../includes/footer.php'; ?>