<?php


namespace Helpers;


use Interfaces\LoggerInterface;

class Logger implements LoggerInterface
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