<?php
$curl = new PHPCurl("http://hamm.cn");
$curl = $curl->setRequestContentType($curl::CONTENT_TYPE_FORM)->doPost();
print_r($curl->getResponseHeader('WhereAreYou'));
print_r($curl->getResponseHeader());
print_r($curl->getResponseDetail());
print_r($curl->getResponseDetail('http_code'));
print_r($curl->getResponseBody());
