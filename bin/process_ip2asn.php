<?php
// process_ip2asn.php

require 'vendor/autoload.php';


$startIp  = '2001:0db8:0000:0042:0000:8a2e:0370:7334';
$endIp    = '2001:0db8:0000:0042:0000:8a2e:0370:7334';
// This will print 2001:0db8:0000:0042:0000:8a2e:0370:7334/30 2001:0db8:0000:0042:0000:8a2e:0370:7344/31
$endIp =   '2001:0db8:0000:0042:0000:8a2e:0370:7343';
#$startIp = '2001:0db8:0000:0000:0000:0000:0000:0000';
#$endIp = '2001:0db8:0000:0000:0000:0000:0000:ffff';

$ranges = \IPLib\Factory::getRangesFromBoundaries($startIp, $endIp);


echo implode(' ', $ranges);
