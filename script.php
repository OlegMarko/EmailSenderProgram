<?php

require('DataLayer.php');

define('DEBUG', true);

class Mailer
{
    public function __construct()
    {
        //
    }

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

class Logger
{
    public static function info($text)
    {
        echo "{$text}\r\n";
    }

    public static function error($text)
    {
        echo "{$text}\r\n";
    }

    public static function success($text)
    {
        echo "{$text}\r\n";
    }
}

interface EmailWork
{
    public function getBody(...$param);
    public function run(...$param);
}

class DoEmailWork implements EmailWork
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
        return "Hi " . $param[0] . "<br>We would like to welcome you as customer on our site!<br><br>Best Regards,<br>Forbytes Team";
    }

    public function run(...$param)
    {
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

class DoEmailWork2 implements EmailWork
{
    private $subject = "We miss you as a customer";
    private $from = "info@forbytes.com";

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function getBody(...$param)
    {
        return "Hi " . $param[0] . "<br>We miss you as a customer. Our shop is filled with nice products. Here is a voucher that gives you 50 kr to shop for.<br>Voucher: " . $param[1] . "<br><br>Best Regards,<br>Forbytes Team";
    }

    public function run(...$param)
    {
        //List all customers
        $e = DataLayer::ListCustomers();
        //List all orders
        $f = DataLayer::ListOrders();

        $success = true;

        //loop through list of customers
        foreach ($e as $c) {
            // We send mail if customer hasn't put an order
            $send = true;
            //loop through list of orders to see if customer don't exist in that list
            foreach ($f as $o) {
                // Email exists in order list
                if ($c->email == $o->customerEmail) {
                    //We don't send email to that customer
                    $send = false;
                }
            }

            //Send if customer hasn't put order
            if ($send == true) {
                //Add customer to reciever list
                $body = $this->getBody($c->email, $param[0]);

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

$mailer = new Mailer();

//Call the method that do the work for me, I.E. sending the mails
Logger::info("Send Welcomemail");
$success = (new DoEmailWork($mailer))->run();

Logger::info("Send Comebackmail");
if (DEBUG) {
    //Debug mode, always send Comeback mail
    $success = (new DoEmailWork2($mailer))->run("ComebackToUs");
} else {
    //Every Sunday run Comeback mail
    if (date('D', time()) === 'Sun') {
        $success = (new DoEmailWork2($mailer))->run("ComebackToUs");
    }
}

//Check if the sending went OK
if ($success == true) {
    Logger::success("All mails are sent, I hope...");
}
//Check if the sending was not going well...
if ($success == false) {
    Logger::error("Oops, something went wrong when sending mail (I think...)");
}

Logger::info("done");