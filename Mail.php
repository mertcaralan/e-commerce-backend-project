<?php
//
// UPDATE Username and Password fields in "config.php"
//
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once './vendor/autoload.php' ;



const EMAIL = 'ali.unlu@ug.bilkent.edu.tr' ;

// your password
const PASS = 'unlu140213' ;

// your fullname
// example : Ahmet Yılmaz
const FULLNAME = "Muhammet Ali Unlu" ;

// About email : Recepient email address and Subject field
const SUBJECT = "e-commerce Verification Code" ;


class Mail {
    public static function send($to, $subject, $message) {
    $mail = new PHPMailer(true) ;
    try {
        //SMTP Server settings
        $mail->isSMTP();                                            
        $mail->Host       = 'asmtp.bilkent.edu.tr';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   =  EMAIL;                                       
        $mail->Password   =  PASS;                     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; 
    
        //Recipients
        $mail->setFrom(EMAIL, FULLNAME);
        // $mail->setFrom('ahmet@ug.bilkent.edu.tr', 'Ahmet Yılmaz');
        $mail->addAddress($to, $to);     //Add a recipient
        // You can add more than one address
        // See further option of recipients cc, bcc in phpmailer docs.

        // Attachment
        // See Documentation of phpmailer

        //Content
        $mail->isHTML(true);  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
    
        $mail->send();
    } catch (Exception $e) {
        echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
        echo "<p>DO NOT Forget to change 'config.php' file for your own Bilkent account</p>";
    }
   }
}