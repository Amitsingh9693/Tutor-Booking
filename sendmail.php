<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function SendEmail($email, $num, $name)
{
    
    // Adjust these paths according to where you placed PHPMailer
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // Server settings for Gmail (adjust as needed)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tutorsphere48@gmail.com'; // Your Gmail
        $mail->Password = 'covj xjut apjb auil';    // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('tutorsphere48@gmail.com', 'TutorSphere');
        $mail->addAddress($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verification Code';
        $mail->Body = 'Hey,'.$name.'<br>Your verification code is: ' . $num;
        $mail->AltBody ='Hey,'.$name.'<br>Your verification code is: ' . $num;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>