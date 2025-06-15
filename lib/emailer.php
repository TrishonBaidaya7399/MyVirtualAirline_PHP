<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

    function sendEmail($subject, $message, $to)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = smtp_host;
            //$mail->SMTPDebug = 2;  //uncomment to enable debug information
            $mail->SMTPAuth   = true;
            $mail->Username   = smtp_username;
            $mail->Password   = smtp_password;
            $mail->Port       = smtp_port;
            $mail->setFrom(smtp_from_address);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
