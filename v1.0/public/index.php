<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include(__DIR__.'/../boot.php');

Helper::start();
Helper::route();
Helper::end();
