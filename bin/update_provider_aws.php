<?php
// update_provider_aws.php
/**
 * Script to update AWS lists from their officially posted ip ranges lists.
 */

// Composer autoload
if( file_exists( dirname(__FILE__) . '/vendor/autoload.php' ) ) {
  require_once 'vendor/autoload.php';
}

require_once dirname(__FILE__) . '/cidr.include.php';
$startTime = microtime(true);

// Download the AWS IP Ranges JSON file
echo "Fetching AWS IP Ranges...\n";
$downloadLists = cidrDownload( array('aws'=>'https://ip-ranges.amazonaws.com/ip-ranges.json') );
$awsData = '';
list('aws' => $awsData) = $downloadLists;

//print_r($awsData);
$dataArray = json_decode($awsData, true);
echo "Fetched " . count($dataArray['prefixes']) . " IPv4 prefixes and " . count($dataArray['ipv6_prefixes']) . " IPv6 prefixes from AWS.\n";

// Organize by service...
$results = [];
$services = [];

// Populate the ipv4 and ipv6 prefixes per service
foreach( $dataArray['prefixes'] as $prefix ) {
  $service = strtolower($prefix['service']);
  if( !isset($services[$service]['ipv4']) ) {
    $services[$service]['ipv4'] = [];
  }
  $services[$service]['ipv4'][ $prefix['ip_prefix'] ] = $prefix['ip_prefix'];
}
foreach( $dataArray['ipv6_prefixes'] as $prefix ) {
  $service = strtolower($prefix['service']);
  if( !isset($services[$service]['ipv6']) ) {
    $services[$service]['ipv6'] = [];
  }
  $services[$service]['ipv6'][ $prefix['ipv6_prefix'] ] = $prefix['ipv6_prefix'];
}

// Write the prefixes to files
$baseDir = dirname(dirname(__FILE__)) . '/lists/aws/';
if( !is_dir($baseDir) ) {
  mkdir($baseDir, 0755, true);
}
$baseAggregatedDir = dirname(dirname(__FILE__)) . '/lists/aws/aggregated/';
if( !is_dir($baseAggregatedDir) ) {
  mkdir($baseAggregatedDir, 0755, true);
}

$all = [];
$filesUpdatedCount = 0;
foreach( $services as $service => $prefixes ) {
  $filePath = $baseDir . $service . '.txt';
  if( !isset($prefixes['ipv4']) ) {
    $prefixes['ipv4'] = [];
  }
  if( !isset($prefixes['ipv6']) ) {
    $prefixes['ipv6'] = [];
  }

  // Save to all list
  $all['ipv4'] = array_merge( $all['ipv4'] ?? [], $prefixes['ipv4'] );
  $all['ipv6'] = array_merge( $all['ipv6'] ?? [], $prefixes['ipv6'] );

  // Sort the ipv4 and ipv6 prefixes then combined them for writing
  sortIPv4Cidrs($prefixes['ipv4']);
  sortIPv6Cidrs($prefixes['ipv6']);
  $prefixes['combined'] = array_merge($prefixes['ipv4'], $prefixes['ipv6']);

  $prefixesToSave = $prefixes['combined'] ?? [];
  file_put_contents( $filePath, implode("\n", $prefixesToSave) );
  $filesUpdatedCount++;
  //echo "Wrote " . count($prefixesToSave) . " prefixes to $filePath\n";
 
  // Write aggregated version of each service
  $filePath = $baseAggregatedDir . $service . '.txt';
  $prefixes['ipv4'] = aggregateIPv4Cidrs($prefixes['ipv4']);
  $prefixes['ipv6'] = aggregateIPv6Cidrs($prefixes['ipv6']);
  $aggregated = array_merge( $prefixes['ipv4'], $prefixes['ipv6'] );
  file_put_contents( $filePath, implode("\n", $aggregated) );
  $filesUpdatedCount++;
  // echo "Wrote aggregated " . count($aggregated) . " prefixes to $filePath\n";
}

// Write all service files combined
$filePath = $baseDir . 'all.txt';
sortIPv4Cidrs($all['ipv4']);
sortIPv6Cidrs($all['ipv6']);
$combined = array_merge($all['ipv4'], $all['ipv6']);
file_put_contents( $filePath, implode("\n", $combined) );
$filesUpdatedCount++;
//echo "Wrote " . count($combined) . " prefixes to $filePath\n";

// Aggregate and write
$all['ipv4'] = aggregateIPv4Cidrs($all['ipv4']);
$all['ipv6'] = aggregateIPv6Cidrs($all['ipv6']);
$filePath = $baseAggregatedDir . 'all.txt';
$aggregated = array_merge( $all['ipv4'], $all['ipv6'] );
file_put_contents( $filePath, implode("\n", $aggregated) );
$filesUpdatedCount++;
//echo "Wrote aggregated " . count($aggregated) . " prefixes to $filePath\n";

echo "Updated $filesUpdatedCount AWS service files.\n";

$endTime = microtime(true);
$duration = $endTime - $startTime;
echo "AWS Provider update completed in " . round($duration, 2) . " seconds\n";
exit(0);