<?php
// process_ip2asn.php
/**
 * Script to process ip2asn data files and generate aggregated IP lists by ASN.
 */

// Composer autoload
require 'vendor/autoload.php';
require_once dirname(__FILE__) . '/cidr.include.php';

// Store in microseconds time script started
$startTime = microtime(true);

/*
 * Preload our data variable files
 */
$allDatacenterASNs = file(dirname(dirname(__FILE__)).'/data/asn/ASN.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$asnMap = array_flip($allDatacenterASNs);
$asnMap = array_fill_keys($allDatacenterASNs, 'other'); // Initially map them all to the 'other' provider

// Initialize results array with 'other' and 'all' providers for both IPv4 and IPv6
$results = [];
$results['all']['ipv4'] = [];
$results['all']['ipv6'] = [];
$results['other']['ipv4'] = [];
$results['other']['ipv6'] = [];

// As we load more specific ASNs for known providers, we can overwrite the asn mapping
$specificProviders = glob(dirname(dirname(__FILE__)).'/data/asn/specific/*.txt');
foreach ($specificProviders as $providerFile) {
  $providerName = pathinfo($providerFile, PATHINFO_FILENAME);
  $providerASNs = file($providerFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($providerASNs as $asn) {
    $asnMap[$asn] = $providerName;
  }
  // Initialize results array for the specific provider for both IPv4 and IPv6
  $results[$providerName]['ipv4'] = [];
  $results[$providerName]['ipv6'] = [];
}
$asn2ipFiles = [
  dirname(dirname(__FILE__)) . '/data/ip2asn/ip2asn-v4.tsv.gz',
  dirname(dirname(__FILE__)) . '/data/ip2asn/ip2asn-v6.tsv.gz',
];


// Store time when loading variables is done
$loadTime = microtime(true);
$loadDuration = $loadTime - $startTime;
echo sprintf("Loaded variables in %.4f seconds.\n", $loadDuration);

/*
 * Preload our data
 */
foreach( $asn2ipFiles as $asn2ipFile) {
  // Store time when processing each file starts
  $processStartTime = microtime(true);

  $ipType = str_ends_with($asn2ipFile, 'v6.tsv.gz') ? 'ipv6' : 'ipv4';
  $count = 0;
  $inCount = 0;
  echo sprintf("Processing %s...\n", basename($asn2ipFile));
  $gzFile = "compress.zlib://" . $asn2ipFile;
  $file = new SplFileObject($gzFile);
  $file->setFlags(SplFileObject::READ_CSV);

  while (!$file->eof()) {
    $count++;
    $row = $file->fgetcsv("\t", "\"", "\\");
    if (count($row) < 3) {
      continue;
    }
    $ipStart = $row[0];
    $ipEnd   = $row[1];
    $asn     = ltrim($row[2], 'AS');
    if (isset($asnMap[$asn])) {
      if( !isset($results[ $asnMap[$asn] ][$ipType]) ) {
        $results[ $asnMap[$asn] ][$ipType] = [];
      }
      $results[ $asnMap[$asn] ][$ipType][] = \IPLib\Factory::getRangesFromBoundaries($ipStart, $ipEnd);
      $results['all'][$ipType][] = \IPLib\Factory::getRangesFromBoundaries($ipStart, $ipEnd);
      $inCount++;
    }
  }

  // Store time when processing each file is done
  $processEndTime = microtime(true);
  $processDuration = $processEndTime - $processStartTime;

  echo sprintf("Processed %s lines, found %s matching ASNs in %.4f seconds.\n", number_format($count), number_format($inCount), $processDuration);
}

/**
 * Lets start processing data and saving output files
 */
$processStartTime = microtime(true);
$ip4And6 = [];
$ip4And6Aggregated = [];

// Sort then save the results to the appropriate files
foreach ($results as $provider => $ipVersions) {
  
  foreach (['ipv4', 'ipv6'] as $ipVersion) {
    if (empty($ipVersions[$ipVersion])) {
      continue;
    }
    // Flatten the array of arrays
    $allRanges = [];
    foreach ($ipVersions[$ipVersion] as $ranges) {
      $allRanges = array_merge($allRanges, $ranges);
    }

    // Sort the ranges then save to file
    sort($allRanges);
    $outputFilePath = dirname(dirname(__FILE__)) . sprintf('/lists/asn/%s-%s.txt', $provider, $ipVersion);
    $outputFile = fopen($outputFilePath, 'w');
    if ($outputFile === false) {
      echo "Failed to open file for writing: $outputFilePath\n";
      continue;
    }
    foreach ($allRanges as $range) {
      fwrite($outputFile, $range . "\n");
    }
    fclose($outputFile);
    $ip4And6 = array_merge($ip4And6, $allRanges);// Save before we aggregate
    echo sprintf("%s list includes %s %s ranges.\n", basename($outputFilePath), number_format(count($allRanges)), $ipVersion);

    // aggregate the ranges
    if( $ipVersion === 'ipv6' ) {
      $allRanges = aggregateIPv6Cidrs($allRanges);
    } else {
      $allRanges = aggregateIPv4Cidrs($allRanges);
    }
    
    // Save aggregated (simplified) ranges back to file
    $outputFilePath = dirname(dirname(__FILE__)) . sprintf('/lists/asn/aggregated/%s-%s.txt', $provider, $ipVersion);
    $outputFile = fopen($outputFilePath, 'w');
    if ($outputFile === false) {
      echo "Failed to open file for writing: $outputFilePath\n";
      continue;
    }
    foreach ($allRanges as $range) {
      fwrite($outputFile, $range . "\n");
    }
    fclose($outputFile);
    $ip4And6Aggregated = array_merge($ip4And6Aggregated, $allRanges); // Save before we aggregate
    echo sprintf("aggregated/%s list includes %s %s ranges.\n", basename($outputFilePath), number_format(count($allRanges)), $ipVersion);
  }

  // Save combined IPv4 and IPv6 file
  $combinedOutputFilePath = dirname(dirname(__FILE__)) . sprintf('/lists/asn/%s.txt', $provider);
  $combinedOutputFile = fopen($combinedOutputFilePath, 'w');
  if ($combinedOutputFile === false) {
    echo "Failed to open file for writing: $combinedOutputFilePath\n";
    continue;
  }
  foreach ( $ip4And6 as $range) {
    fwrite($combinedOutputFile, $range . "\n");
  }
  fclose($combinedOutputFile);
  echo sprintf("%s list includes %s ranges.\n", basename($combinedOutputFilePath), number_format(count($ip4And6)));

  // Save combined aggregated IPv4 and IPv6 file
  $combinedAggregatedOutputFilePath = dirname(dirname(__FILE__)) . sprintf('/lists/asn/aggregated/%s.txt', $provider);
  $combinedAggregatedOutputFile = fopen($combinedAggregatedOutputFilePath, 'w');
  if ($combinedAggregatedOutputFile === false) {
    echo "Failed to open file for writing: $combinedAggregatedOutputFilePath\n";
    continue;
  }
  foreach ($ip4And6Aggregated as $range) {
    fwrite($combinedAggregatedOutputFile, $range . "\n");
  }
  fclose($combinedAggregatedOutputFile);
  echo sprintf("aggregated/%s list includes %s ranges.\n", basename($combinedAggregatedOutputFilePath), number_format(count($ip4And6Aggregated)));
  
  // Free up memory
  $ip4And6 = [];
  $ip4And6Aggregated = [];
}

// Store end time when saving files is done
$processEndTime = microtime(true);
$processDuration = $processEndTime - $processStartTime;
echo sprintf("Saved output files in %.4f seconds.\n", $processDuration);

// Report memory usage
$memoryUsage = memory_get_peak_usage(true);
echo sprintf("Peak memory usage: %s bytes (%.2f MB)\n", number_format($memoryUsage), $memoryUsage / (1024 * 1024)); 

// Store time when processing is done
$endTime = microtime(true);
$processDuration = $endTime - $loadTime;
$totalDuration = $endTime - $startTime;
echo sprintf("Processed data in %.4f seconds.\n", $processDuration);
echo sprintf("Total script duration: %.4f seconds.\n", $totalDuration);

// eof