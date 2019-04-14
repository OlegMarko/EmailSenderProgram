<?php


namespace Commands;


use DataLayer;
use DateTime;
use Exception;
use Helpers\Logger;
use Helpers\Mailer;
use Interfaces\EmailWorkInterface;

class DoEmailWork implements EmailWorkInterface
{
    private $subject = "Welcome as a new customer";
    private $from = "info@forbytes.com";

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function getBody(...$param)
    {
        return "Hi { $param[0] }<br>We would like to welcome you as customer on our site!<br><br>Best Regards,<br>Forbytes Team";
    }

    public function run(...$param)
    {
        Logger::info("Send Welcomemail");

        //List all customers
        $e = DataLayer::ListCustomers();
        $success = true;

        //loop through list of new customers
        foreach ($e as $c) {
            //If the customer is newly registered, one day back in time
            if ($c->createdAt > (new DateTime())->modify('-1 day')) {
                //Add customer to reciever list
                $body = $this->getBody($c->email);

                try {
                    $this->mailer->send($this->from, $c->email, $this->subject, $body);
                } catch (Exception $e) {
                    Logger::error($e->getMessage());
                    $success = false;
                }
            }
        }

        return $success;
    }
}