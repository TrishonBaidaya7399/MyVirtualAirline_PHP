<?php
//This script can be used to test your email settings. Just browse to this file in your browser to send a test email to check config.php
// is configured correctly. If you see a blank screen the email has sent without error.
include '../../config.php';
include '../emailer.php';

$email_recipient = "TEST EMAIL";
$subject = "[vaBase] Test Email Notification";
$message = "This is a test notification email from the vaBase system.";

echo sendEmail($subject, $message, $email_recipient);