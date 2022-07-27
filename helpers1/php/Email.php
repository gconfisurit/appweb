<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email {

    public static function valid_email($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function send_email($title = 'empty', $body = '', $recipients=array())
    {
        require (PATH_VENDOR.'autoload.php');
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                     //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = EMAIL_HOST;                             //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = EMAIL_USER;                             //SMTP username
            $mail->Password   = EMAIL_PASSWORD;                         //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = EMAIL_PORT;                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->Timeout    = EMAIL_TIMEOUT;

            $empresa = (isset($_SESSION['empresa']) == true)
                ? $_SESSION['empresa']
                : "GRUPO CONFISUR C.A.";

            //Recipients
            $mail->setFrom(EMAIL_EMAILFROM,  $empresa);
            if (is_array($recipients)==true and count($recipients)>0) {
                foreach ($recipients as $recipient) {
                    $mail->addAddress($recipient);
                }

                $mail->addReplyTo(EMAIL_REPLY_TO);
                # $mail->addCC(EMAIL_ADDCC);

                //Content
                $mail->isHTML(true);                             //Set email format to HTML
                $mail->Subject = $title;
                $mail->Body    = $body;
                $mail->AltBody = "Grupo Confisur IT -> The Innovation is our's priority..";

                $mail->send();
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}