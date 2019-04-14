<?php


namespace Interfaces;


interface MailerInterface
{
    public function send($from, $to, $subject, $body);
}