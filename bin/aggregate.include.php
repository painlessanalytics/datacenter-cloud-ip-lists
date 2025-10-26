<?php
/**
 * aggregate.include.php
 * 
 * Aggregate IP range functions
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