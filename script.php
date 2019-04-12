<?php

require('DataLayer.php');

function DoEmailWork($debug)
{
    //List all customers
    $e = DataLayer::ListCustomers();

    //loop through list of new customers
    for ($i = 0; $i < count($e); $i++) {
        //If the customer is newly registered, one day back in time
        if ($e[$i]->createdAt > (new DateTime())->modify('-1 day')) {
            //Add customer to reciever list
            $to = $e[$i]->email;
            //Add subject
            $subject = "Welcome as a new customer";
            //Send mail from info@cdon.com
            $from = "info@forbytes.com";
            //Add body to mail
            $body = "Hi " . $e[$i]->email . "<br>We would like to welcome you as customer on our site!<br><br>Best Regards,<br>Forbytes Team";
            if ($debug) {
                //Don't send mails in debug mode, just write the emails in console
                echo "Send mail to:" . $e[$i]->email . "\r\n";
            } 
            else {
                $result = mail($to, $subject, $body);
                if($result === false) {
                    throw new Exception("Cannot send email");
                }
            }
        }
    }
    //All mails are sent! Success!
    return true;
    
}

function DoEmailWork2($debug, $v)
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
            $to = $c->email;
            //Add subject
            $subject = "We miss you as a customer";
            //Send mail from info@cdon.com
            $from = "infor@forbytes.com";
            //Add body to mail
            $body = "Hi " . $c->email . "<br>We miss you as a customer. Our shop is filled with nice products. Here is a voucher that gives you 50 kr to shop for.<br>Voucher: " . $v . "<br><br>Best Regards,<br>Forbytes Team";
            if ($debug) {
                //Don't send mails in debug mode, just write the emails in console
                echo("Send mail to:" . $c->email . "\r\n");
            } else {
                //Send mail
                $result = mail($to, $subject, $body);
                if($result === false) {
                    throw new Exception("Cannot send email");
                }
            }
        }
    }
    //All mails are sent! Success!
    return true;
}

$debug = false;

//Call the method that do the work for me, I.E. sending the mails
echo "Send Welcomemail\r\n";
$success = DoEmailWork($debug);

if ($debug) {
    //Debug mode, always send Comeback mail
    echo("Send Comebackmail\r\n");
    $success = DoEmailWork2($debug, "ComebackToUs");
} else {
    //Every Sunday run Comeback mail
    if (date('D', time()) === 'Mon') {
        echo("Send Comebackmail\r\n");
        $success = DoEmailWork2($debug, "ComebackToUs");
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