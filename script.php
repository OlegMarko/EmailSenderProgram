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

    private function headers()
    {
        return "From: {$from}";
    }
}

class DoEmailWork {

    private $subject = "Welcome as a new customer";
    private $from = "info@forbytes.com";

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    private function getBody($email)
    {
        return "Hi " . $email . "<br>We would like to welcome you as customer on our site!<br><br>Best Regards,<br>Forbytes Team";
    }

    public function run()
    {
        //List all customers
        $e = DataLayer::ListCustomers();

        //loop through list of new customers
        foreach ($e as $c) {
            //If the customer is newly registered, one day back in time
            if ($c->createdAt > (new DateTime())->modify('-1 day')) {
                //Add customer to reciever list
                $body = $this->getBody($c->email);
                $this->mailer->send($this->from, $c->email, $this->subject, $body);
            }
        }
    }
}

class DoEmailWork2 {

    private $subject = "We miss you as a customer";
    private $from = "info@forbytes.com";

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    private function getBody($email, $v)
    {
        return "Hi " . $email . "<br>We miss you as a customer. Our shop is filled with nice products. Here is a voucher that gives you 50 kr to shop for.<br>Voucher: " . $v . "<br><br>Best Regards,<br>Forbytes Team";
    }

    public function run($v)
    {
        //List all customers
        $e = DataLayer::ListCustomers();
        //List all orders
        $f = DataLayer::ListOrders();

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
                $body = $this->getBody($c->email, $v);
                $this->mailer->send($this->from, $c->email, $this->subject, $body);
            }
        }

        return true;
    }
}

$mailer = new Mailer();

//Call the method that do the work for me, I.E. sending the mails
echo "Send Welcomemail\r\n";
$success = (new DoEmailWork($mailer))->run();

if (DEBUG) {
    //Debug mode, always send Comeback mail
    echo("Send Comebackmail\r\n");
    $success = (new DoEmailWork2($mailer))->run("ComebackToUs");
} else {
    //Every Sunday run Comeback mail
    if (date('D', time()) === 'Sun') {
        echo("Send Comebackmail\r\n");
        $success = (new DoEmailWork2($mailer))->run("ComebackToUs");
    }
}

//Check if the sending went OK
if ($success == true) {
    echo("All mails are sent, I hope...\r\n");
}
//Check if the sending was not going well...
if ($success == false) {
    echo("Oops, something went wrong when sending mail (I think...)\r\n");
}
echo "done\r\n";