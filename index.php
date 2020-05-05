<?php
require_once("Curl.php");
$curl = new Curl("http://localhost/test.php");
$curl = $curl->addCookie("a=b")->addCookie("c", "d")->addHeader("Content-Type", "application/json")->returnHeader()->post();
print_r($curl->getCookies());
