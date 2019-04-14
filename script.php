<?php

require('DataLayer.php');
require('src/autoload.php');

use Commands\DoEmailWork;
use Commands\DoEmailWork2;
use Helpers\Logger;
use Helpers\Mailer;

define('DEBUG', true);

$mailer = new Mailer();

//Call the method that do the work for me, I.E. sending the mails
$doEmailWork = new DoEmailWork($mailer);
$success = $doEmailWork->run();

$doEmailWork2 = new DoEmailWork2($mailer);
$success = $doEmailWork2->run("ComebackToUs");

if ($success) {
    //Check if the sending went OK
    Logger::success("All mails are sent, I hope...");
} else {
    //Check if the sending was not going well...
    Logger::error("Oops, something went wrong when sending mail (I think...)");
}

Logger::info("done");