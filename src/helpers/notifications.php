<?php
require '../bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function SendSMS($number, $message)
{

    $username = $_ENV['SMS_USERNAME'];

    $password = $_ENV['SMS_PASSWORD'];

    $sender = $_ENV['SMS_SENDER'];

    $message_type = $_ENV['SMS_MESSAGE_TYPE'];

    $message_category = $_ENV['SMS_MESSAGE_CATEGORY'];

    $url = "sms.thepandoranetworks.com/API/send_sms/?";

    $parameters = "number=[number]&message=[message]&username=[username]&password=[password]&sender=[sender]&message_type=[message_type]&message_category=[message_category]";

    $parameters = str_replace("[message]", urlencode($message), $parameters);

    $parameters = str_replace("[sender]", urlencode($sender), $parameters);

    $parameters = str_replace("[number]", urlencode($number), $parameters);

    $parameters = str_replace("[username]", urlencode($username), $parameters);

    $parameters = str_replace("[password]", urlencode($password), $parameters);

    $parameters = str_replace("[message_type]", urlencode($message_type), $parameters);

    $parameters = str_replace("[message_category]", urlencode($message_category), $parameters);

    $live_url = "https://" . $url . $parameters;

    $parse_url = file($live_url);

    $response = $parse_url[0];

    return json_decode($response, true);
}

function SendEmail($email, $subject, $message)
{

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                    
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];       
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USERNAME'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];

        //Recipients
        $mail->setFrom('student@pandoranetworks.com', 'Pandora Networks');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        // echo 'Message has been sent';
    } catch (Exception $exception) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
