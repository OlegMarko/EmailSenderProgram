<?php


namespace Commands;


use DataLayer;
use DateTime;
use Exception;
use Helpers\Logger;
use Helpers\Mailer;
use Interfaces\EmailWorkInterface;

class DoEmailWork2 implements EmailWorkInterface
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
        return "Hi { $param[0] }<br>We miss you as a customer. Our shop is filled with nice products. Here is a voucher that gives you 50 kr to shop for.<br>Voucher: { $param[1] }<br><br>Best Regards,<br>Forbytes Team";
    }

    public function run(...$param)
    {
        Logger::info("Send Comebackmail");

        if (!DEBUG && date('D', time()) !== 'Sun') {
            return true;
        }

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
                if ($c->email == $o->customerEmail || $c->createdAt > (new DateTime())->modify('-1 day')) {
                    //We don't send email to that customer
                    $send = false;
                    break;
                }
            }

            //Send if customer hasn't put order
            if ($send) {
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