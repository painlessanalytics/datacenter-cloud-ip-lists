<?php
// fix_ipverse_csv.php

$filename = '/tmp/as.csv';
$fileOut = "data/asn/as.csv";
$delimiter = ",";
$enclosure = '"';
$escape = "\\";
$fpOut = fopen($fileOut, 'w');
$count = 0;

if (($handle = fopen($filename, 'r')) !== false) {
  while (($line = fgets($handle)) !== false) {
    if( preg_match('/^([^,]*),([^,]*),(.*)$/', $line, $matches) ) {
      fputcsv($fpOut, [$matches[1], $matches[2], $matches[3]], $delimiter, $enclosure, $escape);
      $count++;
    }
  }
  fclose($handle);
  fclose($fpOut);
}
