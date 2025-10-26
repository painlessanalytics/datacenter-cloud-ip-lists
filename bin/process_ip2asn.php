<?php
// process_ip2asn.php
/**
 * Script to process ip2asn data files and generate aggregated IP lists by ASN.
 */

// Composer autoload
require 'vendor/autoload.php';

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

/**
 * Helper Functions below
 */

/**
 * Aggregate a list of IPv6 CIDR blocks into minimal CIDRs.
 *
 * @param array $cidrs List of IPv6 CIDRs (e.g., ['2001:db8::1/128', '2001:db8::2/128'])
 * @return array Aggregated list of minimal IPv6 CIDRs
 */
function aggregateIPv6Cidrs(array $cidrs): array
{
  $ranges = [];

  // Step 1: Convert each CIDR to start/end GMP numbers
  foreach ($cidrs as $cidr) {
    [$ip, $prefix] = explode('/', $cidr);
    $ipGmp = inet6ToGmp($ip);

    $mask = gmpShiftLeft(gmp_init(-1), 128 - $prefix);
    $start = gmp_and($ipGmp, $mask);
    $end = gmp_or($start, gmp_and(gmp_com($mask), gmp_init("0xffffffffffffffffffffffffffffffff", 16)));

    $ranges[] = [$start, $end];
  }

  // Step 2: Sort by start
  usort($ranges, fn($a, $b) => gmp_cmp($a[0], $b[0]));

  // Step 3: Merge overlapping or contiguous ranges
  $merged = [];
  foreach ($ranges as [$start, $end]) {
    if (empty($merged)) {
      $merged[] = [$start, $end];
    } else {
      [$lastStart, $lastEnd] = $merged[count($merged) - 1];
      $nextAfterLast = gmp_add($lastEnd, 1);

      if (gmp_cmp($start, $nextAfterLast) <= 0) {
        // Merge overlapping or contiguous ranges
        $merged[count($merged) - 1][1] = gmpCompareMax($lastEnd, $end);
      } else {
        $merged[] = [$start, $end];
      }
    }
  }

  // Step 4: Convert merged ranges back to minimal CIDRs
  $result = [];
  foreach ($merged as [$start, $end]) {
    $result = array_merge($result, rangeToIPv6Cidrs($start, $end));
  }

  return $result;
}

/**
 * Convert an IPv6 range (start → end) to minimal CIDR blocks.
 */
function rangeToIPv6Cidrs(GMP $start, GMP $end): array
{
  $cidrs = [];

  while (gmp_cmp($start, $end) <= 0) {
    $maxSize = 128;

    // Find largest prefix aligned with start
    while ($maxSize > 0) {
      $mask = gmpShiftLeft(gmp_init(-1), 128 - ($maxSize - 1));
      if (gmp_cmp(gmp_and($start, $mask), $start) !== 0) {
        break;
      }
      $maxSize--;
    }

    // Adjust prefix to fit in range
    while (true) {
      $blockSize = gmp_pow(2, 128 - $maxSize);
      $blockEnd = gmp_add($start, gmp_sub($blockSize, 1));
      if (gmp_cmp($blockEnd, $end) > 0) {
        $maxSize++;
      } else {
        break;
      }
    }

    $cidrs[] = gmpToInet6($start) . "/$maxSize";
    $start = gmp_add($start, gmp_pow(2, 128 - $maxSize));
  }

  return $cidrs;
}

/**
 * Convert IPv6 address to GMP number.
 */
function inet6ToGmp(string $ip): GMP
{
  $bin = inet_pton($ip);
  $hex = unpack('H*', $bin)[1];
  return gmp_init($hex, 16);
}

/**
 * Convert GMP number to IPv6 address.
 */
function gmpToInet6(GMP $num): string
{
  $hex = str_pad(gmp_strval($num, 16), 32, '0', STR_PAD_LEFT);
  $bin = pack('H*', $hex);
  return inet_ntop($bin);
}

/**
 * Left-shift for GMP (since gmp_shiftl doesn’t exist natively).
 */
function gmpShiftLeft(GMP $num, int $bits): GMP
{
  return gmp_mul($num, gmp_pow(2, $bits));
}

/**
 * Return the greater of two GMP numbers.
 */
function gmpCompareMax(GMP $a, GMP $b): GMP
{
  return gmp_cmp($a, $b) >= 0 ? $a : $b;
}

/**
 * Aggregate a list of CIDR ranges into minimal CIDRs.
 *
 * @param array $cidrs Array of CIDR strings (e.g. ['10.10.10.8/32', '10.10.10.9/32'])
 * @return array Aggregated array of CIDR strings
 */
function aggregateIPv4Cidrs(array $cidrs): array
{
  $ranges = [];

  // Step 1: Expand all CIDRs into start/end IP ranges
  foreach ($cidrs as $cidr) {
    [$ip, $prefix] = explode('/', $cidr);
    $ipLong = ip2long($ip);
    $mask = 0xFFFFFFFF << (32 - $prefix);
    $start = $ipLong & $mask;
    $end = $start | (~$mask & 0xFFFFFFFF);
    $ranges[] = [$start, $end];
  }

  // Step 2: Sort by start address
  usort($ranges, fn($a, $b) => $a[0] <=> $b[0]);

  // Step 3: Merge overlapping or contiguous ranges
  $merged = [];
  foreach ($ranges as [$start, $end]) {
    if (empty($merged)) {
      $merged[] = [$start, $end];
    } else {
      [$lastStart, $lastEnd] = $merged[count($merged) - 1];
      if ($start <= $lastEnd + 1) {
        // Merge
        $merged[count($merged) - 1][1] = max($lastEnd, $end);
      } else {
        $merged[] = [$start, $end];
      }
    }
  }

  // Step 4: Convert merged ranges back to minimal CIDR blocks
  $result = [];
  foreach ($merged as [$start, $end]) {
    $result = array_merge($result, rangeToCidrs($start, $end));
  }

  return $result;
}

/**
 * Convert a range of IPs (start → end) into minimal CIDR blocks.
 */
function rangeToCidrs(int $start, int $end): array
{
  $cidrs = [];
  while ($start <= $end) {
    $maxSize = 32;
    while ($maxSize > 0) {
      $mask = 0xFFFFFFFF << (32 - ($maxSize - 1));
      if (($start & ~$mask) !== 0) {
        break;
      }
      $maxSize--;
    }

    // Find largest block that fits in the range
    $remaining = $end - $start + 1;
    while ((1 << (32 - $maxSize)) > $remaining) {
      $maxSize++;
    }

    $cidrs[] = long2ip($start) . "/$maxSize";
    $start += (1 << (32 - $maxSize));
  }
  return $cidrs;
}

// eof