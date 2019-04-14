<?php


namespace Helpers;


use Exception;
use Interfaces\MailerInterface;

class Mailer implements MailerInterface
{
    public function send($from, $to, $subject, $body)
    {
        if (DEBUG) {
            //Don't send mails in debug mode, just write the emails in console
            echo "Send mail to:" . $to . "\r\n";
        } else {
            $headers = $this->headers($from);
            $result = mail($to, $subject, $body, $headers);

            if($result === false) {
                throw new Exception("Cannot send email");
            }
        }

        return true;
    }

    private function headers($from)
    {
        return "From: {$from}";
    }
}