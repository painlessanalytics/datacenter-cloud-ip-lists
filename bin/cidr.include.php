<?php
/**
 * cidr.include.php
 * 
 * CIDR helper functions for aggregating and sorting CIDR blocks.
 * 
 * This library requires the GMP PHP extension for IPv6 support and curl PHP extension for fetching URLs.
 * This library requires Composer packages mlocati/ip-lib and optionally composer/ca-bundle.
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
 * 
 * @param GMP $start Starting IPv6 address as GMP number.
 * @param GMP $end Ending IPv6 address as GMP number.
 * @return array Array of CIDR strings.
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
 * 
 * @param string $ip The IPv6 address in standard notation.
 * @return GMP The GMP number representing the IPv6 address.
 */
function inet6ToGmp(string $ip): GMP
{
  $bin = inet_pton($ip);
  $hex = unpack('H*', $bin)[1];
  return gmp_init($hex, 16);
}

/**
 * Convert GMP number to IPv6 address.
 * 
 * @param GMP $num The GMP number representing the IPv6 address.
 * @return string The IPv6 address in standard notation.
 */
function gmpToInet6(GMP $num): string
{
  $hex = str_pad(gmp_strval($num, 16), 32, '0', STR_PAD_LEFT);
  $bin = pack('H*', $hex);
  return inet_ntop($bin);
}

/**
 * Left-shift for GMP (since gmp_shiftl doesn’t exist natively).
 * 
 * @param GMP $num The GMP number to shift.
 * @param int $bits Number of bits to shift left.
 * @return GMP The left-shifted GMP number.
 */
function gmpShiftLeft(GMP $num, int $bits): GMP
{
  return gmp_mul($num, gmp_pow(2, $bits));
}

/**
 * Return the greater of two GMP numbers.
 * 
 * @param GMP $a First GMP number
 * @param GMP $b Second GMP number
 * @return GMP Greater of the two GMP numbers
 */
function gmpCompareMax(GMP $a, GMP $b): GMP
{
  return gmp_cmp($a, $b) >= 0 ? $a : $b;
}

/**
 * Sort list of CIDR strings in IPv4 order.
 * 
 * @param array $cidrs Array of IPv4 CIDR strings
 * @return array Sorted array of IPv4 CIDR strings
 */
function sortIPv4Cidrs(array &$cidrs): array
{
  usort($cidrs, function ($a, $b) {  
    $a = str_replace(strstr($a, '/'), '', $a); // Remove CIDR suffix if present
    $b = str_replace(strstr($b, '/'), '', $b); // Remove CIDR suffix if present
    $aLong = ip2long($a);
    $bLong = ip2long($b);

      // Using the spaceship operator (<=>) for concise comparison
      return $aLong <=> $bLong;
  });
  return $cidrs;
}

/**
 * Sort list of CIDR strings in IPv6 order.
 * 
 * @param array $cidrs Array of IPv6 CIDR strings
 * @return array Sorted array of IPv6 CIDR strings
 */
function sortIPv6Cidrs(array &$cidrs): array
{
  usort($cidrs, function ($a, $b) {
    $aGmp = inet6ToGmp(str_replace(strstr($a, '/'), '', $a)); // Remove CIDR suffix if present
    $bGmp = inet6ToGmp(str_replace(strstr($b, '/'), '', $b)); // Remove CIDR suffix if present
    return gmp_cmp($aGmp, $bGmp);
  });
  return $cidrs;
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
  /*
  usort($ranges, function ($a, $b) {
    
    $a = str_replace(strstr($a, '/'), '', $a); // Remove CIDR suffix if present
    $b = str_replace(strstr($b, '/'), '', $b); // Remove CIDR suffix if present
    $aLong = ip2long($a);
    $bLong = ip2long($b);

      // Using the spaceship operator (<=>) for concise comparison
      return $aLong <=> $bLong;
  });
  */

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
 * 
 * @param int $start Starting IP as long integer.
 * @param int $end Ending IP as long integer.
 * @return array Array of CIDR strings.
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

/**
 * Sort and combine IPv4 and IPv6 CIDR lists.
 *
 * @param array $data Associative array with 'ipv4' and 'ipv6' keys containing CIDR lists
 * @return array Sorted and combined array with 'ipv4', 'ipv6', and 'combined' keys
 */
function sortAndCombineData(array $data): array {
  sortIPv4Cidrs($data['ipv4']);
  sortIPv6Cidrs($data['ipv6']);
  $data['combined'] = array_merge($data['ipv4'], $data['ipv6']);
  return $data;
}

/**
 * Downloads multiple files concurrently using cURL multi handles.
 *
 * @param array $arrayOfUrls An array of URLs to download with unique keys to map results to.
 * @param array $options Optional cURL options to set for each handle.
 * @return array An associative array with URLs as keys and their corresponding downloaded content as values or an Exception error message
 */
function cidrDownload($arrayOfUrls, $options = []) {

  $multiHandle = curl_multi_init();
  $caPathOrFile = '';
  if( class_exists("Composer\\CaBundle\\CaBundle")) {
    $caPathOrFile = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
  }
  $curlHandles = [];
  $responses = [];

  // Initialize individual cURL handles and add them to the multi handle
  foreach ($arrayOfUrls as $key => $url) {

    $curlHandles[$key] = curl_init($url);
    if ($caPathOrFile !== '') {
      if (is_dir($caPathOrFile)) {
        curl_setopt($curlHandles[$key], CURLOPT_CAPATH, $caPathOrFile);
      } else {
        curl_setopt($curlHandles[$key], CURLOPT_CAINFO, $caPathOrFile);
      }
    }

    // Set default options
    curl_setopt($curlHandles[$key], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandles[$key], CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curlHandles[$key], CURLOPT_USERAGENT, 'Datacenter Cloud IP Lists Script/1.0 (+https://github.com/painlessanalytics/datacenter-cloud-ip-lists)');
    
    // Apply any additional options provided
    foreach ($options as $optionKey => $optionValue) {
        curl_setopt($curlHandles[$key], $optionKey, $optionValue);
    }
    
    curl_multi_add_handle($multiHandle, $curlHandles[$key]);
  }

  // Execute all queries simultaneously
  $mrc = null;
  $running = null;
  do {
    $mrc = curl_multi_exec($multiHandle, $running);
    curl_multi_select($multiHandle);
  } while ($running > 0);

  if ($mrc != CURLM_OK) {
    // Handle multi-handle error
    return new Exception("Multi-handle error: " . curl_multi_strerror($mrc));
  }

  // Collect responses and remove handles
  foreach ($curlHandles as $key => $ch) {
    //echo "Downloaded: " . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) . "\n";
    $responses[$key] = curl_multi_getcontent($ch);
    $responseDetail[$key] = curl_getinfo($ch);
    $error_code = curl_errno($ch);
    if ($error_code != 0) {
      // Handle individual cURL handle error
      return new Exception("cURL handle error for " . $ch . ": " . curl_error($ch) . " (Code: " . $error_code . ")");
    }
    curl_multi_remove_handle($multiHandle, $ch);
    curl_close($ch);
  }

  curl_multi_close($multiHandle);
  return $responses;
}

// eof