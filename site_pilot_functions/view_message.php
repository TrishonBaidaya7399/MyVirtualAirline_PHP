<?php
include '../lib/functions.php';
include '../config.php';
include '../lib/emailer.php';

use Proxy\Api\Api;

Api::__constructStatic();
session_start();

validateSession();

$id = addslashes(trim($_GET['id']));
$mail = null;
$res = Api::sendSync('GET', 'v1/mailbox/' . $id, null);
if ($res->getStatusCode() == 200) {
    $mail = json_decode($res->getBody(), false);
} else {
    header('Location: ' . website_base_url . 'site_pilot_functions/inbox.php');
    exit();
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}
if ($function == "delete_reply") {
    Api::sendAsync('DELETE', 'v1/mailbox/reply/' . cleanString($_GET['replyid']), null);
}

$status = null;
$reply = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reply = cleanString($_POST['message']);

    if (empty($reply)) {
        $status = "required_fields";
    }

    if (empty($status)) {
        $data = [
            'mailId' => $id,
            'body' => $reply
        ];

        $res = Api::sendSync('POST', 'v1/mailbox/reply', $data);

        switch ($res->getStatusCode()) {
            case 200:
                //send email notification
                $subject = virtual_airline_name . " | Message Notification";
                $message = "Dear Pilot, <br /><br />You have received a new message in your inbox."
                    . "<br /><br /><a href='" . website_base_url . "site_pilot_functions/view_message.php?id=" . $id . "'>> View Message</a><br /><br />Kind Regards,<br />
            " . virtual_airline_name . " Team.";

                if ($_SESSION['pilotid'] == $mail->receiverNavigation->id) {
                    $email = $mail->senderNavigation->email;
                } else {
                    $email = $mail->receiverNavigation->email;
                }
                echo sendEmail($subject, $message, $email);
                header('Location: ' . website_base_url . 'site_pilot_functions/view_message.php?id=' . $id);
                exit();
            case 400:
                $status = "error";
                $responseMessage = $res->getBody();
                break;
            default:
                $status = "error";
                break;
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
<style>
    .messagebox {
        max-height: 550px;
        overflow-y: scroll;
        padding-left: 40px;
        padding-right: 40px;
        margin-bottom: 20px;
    }

    .reply {
        width: 60%;
        background-color: #eee;
        clear: right;
        margin-top: 20px !important;
        padding: 20px;
        border-radius: 15px;
    }

    .blue {
        clear: left;
        width: 60%;
        background-color: #d2eef7;
        float: right;
    }

    .name {
        font-size: 13px;
    }

    .name .fa {
        font-size: 18px;
        font-weight: normal;
        float: right;
        margin-left: 10px;
    }

    .check-unread {
        color: #BBE4F2;
    }

    .check-read {
        color: #176680;
    }

    .reply .read {

        float: right;
        padding-top: 10px;
    }
</style>
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
                    <h3 class="panel-title"><?php echo $mail->subject; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if (!empty($status)) { ?>
                        <div class="row">
                            <?php if ($status == "error") { ?>
                                <div class="alert alert-danger" role="alert">
                                    <p>
                                        <?php if ($status == 'error') {
                                            if (!empty($responseMessage)) {
                                                echo $responseMessage;
                                            } else {
                                                echo '<i class="fa fa-warning" aria-hidden="true"></i> An error occurred when sending message.';
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
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12 messagebox" id="messagebox">
                            <?php foreach ($mail->mailItems as $key => $reply) { ?>
                                <div class="row">
                                    <div class="reply <?php echo $_SESSION["pilotid"] == $reply->sender ? 'blue' : ''; ?>"
                                        id="reply-<?php echo $reply->id; ?>"
                                        title="sent: <?php echo $reply->dateString; ?>">
                                        <p class="name">
                                            <?php echo $_SESSION["pilotid"] == $reply->sender ? 'You' : ($reply->senderNavigation->name . ' (' . $reply->senderNavigation->callsign . ')'); ?>
                                            <?php
                                            if ($_SESSION["pilotid"] == $reply->sender && $reply->receiverRead == false) {
                                                echo '<a href="?id=' . $id . '&replyid=' . $reply->id . '&function=delete_reply"><i class="fa fa-trash" title="delete"></i></a>';
                                            }
                                            ?>
                                            <?php
                                            if ($_SESSION["pilotid"] == $reply->sender) {
                                                echo $reply->receiverRead == true ? '<i class="fa fa-check check-read" title="Read"></i>' : '<i class="fa fa-check check-unread" title="Delivered"></i>';
                                            }
                                            ?>
                                        </p>
                                        <p class="body">
                                            <?php echo $reply->body; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" class="form" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea rows="4" class="form-control" id="message" name="message"
                                        required></textarea>
                                </div>
                                <div class="form-group">
                                    <input name="profile" type="submit" id="profile" value="Reply"
                                        class="btn btn-success">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    window.onload = function() {
        // var d = $('#messagebox');
        // d.scrollBottom(d.prop("scrollHeight"));

        // $("#messagebox").animate({
        // 	scrollTop: $('#messagebox').height()
        // }, 1000);


        var objDiv = document.getElementById("messagebox");
        objDiv.scrollTop = objDiv.scrollHeight;
    }
</script>
<?php include '../includes/footer.php'; ?>