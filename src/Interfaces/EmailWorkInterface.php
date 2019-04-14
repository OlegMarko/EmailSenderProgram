<?php


namespace Interfaces;


interface EmailWorkInterface
{
    public function getBody(...$param);
    public function run(...$param);
}