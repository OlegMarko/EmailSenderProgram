<?php


namespace Interfaces;


interface LoggerInterface
{
    public static function info($text);
    public static function error($text);
    public static function success($text);
}