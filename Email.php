<?php
/** Defines the parameters for emails to be sent out */
//Uses PHPMailer-master: https://github.com/PHPMailer/PHPMailer
session_start();

//Calls the external dependencies and files from PHPMailer-master
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendEmail($Subject, $Body, $Address)
{
    $mail = new PHPMailer();

    //$mail->SMTPDebug = 2;// Enable verbose debug output

    $mail->IsSMTP(); // Set mailer to use SMTP
    $mail->SMTPAuth= true;
    $mail->SMTPSecure="ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->CharSet= "big5";

    $mail->Username="yokiotest@gmail.com"; //Email account used to send the email
    $mail->Password= "powaa7890"; //Email password(This is a throwaway email account, nothing of importance is inside)
    $mail->SetFrom('yokiotest@gmail.com','Cybersecurity Registration');//sender email and identifier


    $mail->Sender = 'account_bounces-user=yokiotest@gmail.com';
    $mail->Subject= $Subject; //Email header
    $mail->Body= $Body; //Content of the email
    $mail->IsHTML(true); //Enables HTML tags
    $mail->AddAddress($Address); //Address to be sent to

    if(!$mail->Send()) //Failed to send email
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}