<?php
// update_provider_sources.php
/**
 * Script to update provider lists from their official source lists.
 */

// Composer autoload
if( file_exists( dirname(__FILE__) . '/vendor/autoload.php' ) ) {
  require_once 'vendor/autoload.php';
}

require_once dirname(__FILE__) . '/cidr.include.php';

// Store in microseconds time script started
$startTime = microtime(true);

// Load sources.json to php array
$sourcesFilePath = dirname(dirname(__FILE__)) . '/data/sources/sources.json';
$sourcesJson = file_get_contents($sourcesFilePath);
$sourcesArray = json_decode($sourcesJson, true);

// Download the first round of files (pre-download list)
$preDownloadLists = cidrDownload($sourcesArray['pre_download_list']);

if( $preDownloadLists instanceof Exception ) {
  echo "Error downloading pre-download list files: " . $preDownloadLists->getMessage() . "\n";
  exit(1);
}

// Handle special case for Azure to get the date for the ip-list URL
if( !empty($preDownloadLists['azure']) ) {
  if( preg_match('/da13a5de5b63\/ServiceTags_Public_(\d{8})\.json/is', $preDownloadLists['azure'], $matches) ) {
    $sourcesArray['download_list']['azure'] = str_replace('%YYYYMMDD%', $matches[1], $sourcesArray['replace_download_list']['azure']);
    echo "Added Azure download URL: " . $sourcesArray['download_list']['azure'] . "\n";
  } else {
    echo "Azure download URL not added: Date not found in pre-download file.\n";
  }
} else {
  echo "Azure download URL not added: Pre-download file not set.\n";
}

$downloadLists = cidrDownload($sourcesArray['download_list']);
if( $downloadLists instanceof Exception ) {
  echo "Error downloading download list files: " . $downloadLists->getMessage() . "\n";
  exit(1);
}
echo "Provider source files downloaded in " . round((microtime(true) - $startTime), 2) . " seconds.\n";

// Set timestamp for creating lists
$createTime = microtime(true);

foreach( $downloadLists as $providerName => $fileContents ) {

  // For debugging purposes:
  $filePathOrig = dirname(dirname(__FILE__)) . '/lists/source/' . $providerName . '.orig';
  file_put_contents($filePathOrig, $fileContents);

  $dataToSave = [];
  $dataToSave['combined'] = []; // ipv4 + ipv6
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];

  // Handle special case for provider names with underscores, we combine the records into 1 file assuming  they are 2 separate lists
  if( preg_match('/^([^_]*)\_(.*)$/is', $providerName, $matches) ) {
    $ipVersion = $matches[2]; // ipv4 or ipv6
    $providerShorterName = $matches[1];
    if( $ipVersion === 'ipv4' ) {
      continue; // Skip saving here, wait for ipv6 part
    }
    $providerName = $providerShorterName; // Use the shorter name as the provider name
  }

  echo "$providerName: \n";
  switch( $providerName ) {
    case 'aws':
      $dataToSave = parseAWSIPRanges($fileContents);
      break;
    case 'azure':
      $dataToSave = parseAzureIPRanges($fileContents);
      break;
    case 'google-cloud':
      $dataToSave = parseGoogleCloudIPRanges($fileContents);
      break;
    case 'digitalocean':
      $dataToSave = parseDigitalOceanIPRanges($fileContents);
      break;
    case 'cloudflare':
      $dataToSave = parseCloudflareIPRanges($downloadLists[$providerShorterName . '_ipv4'] ?? '', $downloadLists[$providerShorterName . '_ipv6'] ?? '');
      break;
    case 'oracle-cloud':
      $dataToSave = parseOracleCloudIPRanges($fileContents);
      break;
    case 'linode':
      // Linode uses Akamai for their IP ranges
      $dataToSave = parseLinodeIPRanges($fileContents);
      break;
    case 'github':
      $dataToSave = parseGithubIPRanges($fileContents);
      break;
    default:
      break;
  }

  if( empty($dataToSave['combined']) ) {
    echo "\tNo data to save for $providerName, skipping.\n";
    continue;
  }

  // Destination for provider source files
  $filePath = dirname(dirname(__FILE__)) . '/lists/source/' . $providerName . '.txt';
  $filePathIPv4 = dirname(dirname(__FILE__)) . '/lists/source/' . $providerName . '-ipv4.txt';
  $filePathIPv6 = dirname(dirname(__FILE__)) . '/lists/source/' . $providerName . '-ipv6.txt';
  
  // Save lists to files
  file_put_contents($filePath, implode("\n", $dataToSave['combined']));
  echo "\tlists/source/" . basename($filePath) . "\n";
  if( !empty($dataToSave['ipv4']) ) {
    file_put_contents($filePathIPv4, implode("\n", $dataToSave['ipv4']));
    echo "\tlists/source/" . basename($filePathIPv4) . "\n";
  } else {
    // Unable to save IPv4 file, skipping.
    echo "\tlists/source/" . basename($filePathIPv4) . " (skipped, no IPv4 data)\n";
  }
  if( !empty($dataToSave['ipv6']) ) {
    file_put_contents($filePathIPv6, implode("\n", $dataToSave['ipv6']));
    echo "\tlists/source/" . basename($filePathIPv6) . "\n";
  } else {
    // Unable to save IPv6 file, skipping.
    echo "\tlists/source/" . basename($filePathIPv6) . " (skipped, no IPv6 data)\n";
  }
  
  // Make versions of the files that are aggregated (simplified)
  $aggregatedFilePath = dirname(dirname(__FILE__)) . '/lists/source/aggregated/' . $providerName . '.txt';
  $aggregatedFilePathIPv4 = dirname(dirname(__FILE__)) . '/lists/source/aggregated/' . $providerName . '-ipv4.txt';
  $aggregatedFilePathIPv6 = dirname(dirname(__FILE__)) . '/lists/source/aggregated/' . $providerName . '-ipv6.txt';
  $dataToSave['combined'] = []; // free up memory
  $aggregatedDataToSave = [];
  $aggregatedDataToSave['combined'] = [];
  $aggregatedDataToSave['ipv4'] = [];
  $aggregatedDataToSave['ipv6'] = [];
  if( !empty($dataToSave['ipv4']) ) {
    $aggregatedDataToSave['ipv4'] = aggregateIPv4Cidrs($dataToSave['ipv4']);
    $aggregatedDataToSave['combined'] = array_merge($aggregatedDataToSave['combined'], $aggregatedDataToSave['ipv4']);
  }

  if( !empty($dataToSave['ipv6']) ) {
    $aggregatedDataToSave['ipv6'] = aggregateIPv6Cidrs($dataToSave['ipv6']);
    $aggregatedDataToSave['combined'] = array_merge($aggregatedDataToSave['combined'], $aggregatedDataToSave['ipv6']);
  }
  file_put_contents($aggregatedFilePath, implode("\n", $aggregatedDataToSave['combined']));
  echo "\tlists/source/aggregated/" . basename($aggregatedFilePath) . "\n";
  if( !empty($aggregatedDataToSave['ipv4']) ) {
    file_put_contents($aggregatedFilePathIPv4, implode("\n", $aggregatedDataToSave['ipv4']));
    echo "\tlists/source/aggregated/" . basename($aggregatedFilePathIPv4) . "\n";
  } else {
    // Unable to save aggregated IPv4 file, skipping.
    echo "\tlists/source/aggregated/" . basename($aggregatedFilePathIPv4) . " (skipped, no IPv4 data)\n";
  }
  if( !empty($aggregatedDataToSave['ipv6']) ) {
    file_put_contents($aggregatedFilePathIPv6, implode("\n", $aggregatedDataToSave['ipv6']));
    echo "\tlists/source/aggregated/" . basename($aggregatedFilePathIPv6) . "\n";
  } else {
    // Unable to save aggregated IPv6 file, skipping.
    echo "\tlists/source/aggregated/" . basename($aggregatedFilePathIPv6) . " (skipped, no IPv6 data)\n";
  }
}

$memoryUsage = memory_get_peak_usage(true);
echo sprintf("Peak memory usage: %s bytes (%.2f MB)\n", number_format($memoryUsage), $memoryUsage / (1024 * 1024)); 

function parseAWSIPRanges($jsonContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];

  $jsonData = json_decode($jsonContents, true);
  if( isset($jsonData['prefixes']) ) {
    foreach( $jsonData['prefixes'] as $prefixRecord ) {
      if( isset($prefixRecord['ip_prefix']) ) {
        if( in_array($prefixRecord['ip_prefix'], $dataToSave['ipv4']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv4'][] = $prefixRecord['ip_prefix'];
      }
    }
  }
  if( isset($jsonData['ipv6_prefixes']) ) {
    foreach( $jsonData['ipv6_prefixes'] as $prefixRecord ) {
      if( isset($prefixRecord['ipv6_prefix']) ) {
        if( in_array($prefixRecord['ipv6_prefix'], $dataToSave['ipv6']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv6'][] = $prefixRecord['ipv6_prefix'];
      }
    }
  }

  return sortAndCombineData($dataToSave);
}

// This function is not working yet, Azure changed their IP list format recently
function parseAzureIPRanges($jsonContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];

  $jsonData = json_decode($jsonContents, true);
  if( isset($jsonData['values']) ) {
    foreach( $jsonData['values'] as $valueRecord ) {
      if( isset($valueRecord['properties']['addressPrefixes']) ) {
        foreach( $valueRecord['properties']['addressPrefixes'] as $prefix ) {

          if( filter_var($prefix, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || strpos($prefix, '/') !== false && filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
            if( in_array($prefix, $dataToSave['ipv4']) ) {
              continue; // Skip duplicates
            }
            $dataToSave['ipv4'][] = $prefix;
          } elseif( filter_var($prefix, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || strpos($prefix, '/') !== false && filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
            if( in_array($prefix, $dataToSave['ipv6']) ) {
              continue; // Skip duplicates
            }
            $dataToSave['ipv6'][] = $prefix;
          } else {
            echo "Unrecognized prefix format: $prefix\n";
          }
        }
      } else {
        return new Exception("Unexpected Azure IP list format, 'addressPrefixes' key not found in value record.");
      }
    }
  } else {
    return new Exception("Unexpected Azure IP list format, 'values' key not found.");
  }
  return sortAndCombineData($dataToSave);
}

function parseGoogleCloudIPRanges($jsonContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];

  $jsonData = json_decode($jsonContents, true);
  if( isset($jsonData['prefixes']) ) {
    foreach( $jsonData['prefixes'] as $prefixRecord ) {
      if( isset($prefixRecord['ipv4Prefix']) ) {
        if( in_array($prefixRecord['ipv4Prefix'], $dataToSave['ipv4']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv4'][] = $prefixRecord['ipv4Prefix'];
      }
      if( isset($prefixRecord['ipv6Prefix']) ) {
        if( in_array($prefixRecord['ipv6Prefix'], $dataToSave['ipv6']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv6'][] = $prefixRecord['ipv6Prefix'];
      }
    }
  }
  return sortAndCombineData($dataToSave);
}

// Parse digital ocean ip ranges from a csv file, we only need the first column from each row
function parseDigitalOceanIPRanges($csvContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];

  $lines = explode("\n", trim($csvContents));
  foreach( $lines as $line ) {
    $columns = str_getcsv($line, ',', '"', '\\');
    if( !empty($columns[0]) ) {
      $prefix = trim($columns[0]);
      if( filter_var($prefix, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || strpos($prefix, '/') !== false && filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
        if( in_array($prefix, $dataToSave['ipv4']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv4'][] = $prefix;
      } elseif( filter_var($prefix, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || strpos($prefix, '/') !== false && filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
        if( in_array($prefix, $dataToSave['ipv6']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv6'][] = $prefix;
      }
    }
  }
  return sortAndCombineData($dataToSave);
}

function parseCloudflareIPRanges($ipv4Contents = '', $ipv6Contents = '') {
  $dataToSave = [];
  $dataToSave['ipv6'] = explode("\n", trim($ipv6Contents));
  $dataToSave['ipv4'] = explode("\n", trim($ipv4Contents));
  return sortAndCombineData($dataToSave);
}

function parseOracleCloudIPRanges($jsonContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];

  $jsonData = json_decode($jsonContents, true);
  if( isset($jsonData['regions']) ) {
    foreach( $jsonData['regions'] as $regionRecord ) {
      if( isset($regionRecord['cidrs']) ) {
        foreach( $regionRecord['cidrs'] as $cidrRecord ) {
          if( isset($cidrRecord['cidr']) ) {
            $prefix = $cidrRecord['cidr'];
            if( filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
              if( in_array($prefix, $dataToSave['ipv4']) ) {
                continue; // Skip duplicates
              }
              $dataToSave['ipv4'][] = $prefix;
            } elseif( filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
              if( in_array($prefix, $dataToSave['ipv6']) ) {
                continue; // Skip duplicates
              }
              $dataToSave['ipv6'][] = $prefix;
            }
          }
        }
      }
    }
  }
  return sortAndCombineData($dataToSave);
}

// Parse linode ip ranges from a csv file, we only need the first column from each row and we can skip rows that start with a # character.
function parseLinodeIPRanges($jsonContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];
  $lines = explode("\n", trim($jsonContents));
  foreach( $lines as $line ) {
    $columns = str_getcsv($line, ',', '"', '\\');
    if( !empty($columns[0]) && substr(trim($columns[0]), 0, 1) !== '#' ) {
      $prefix = trim($columns[0]);
      if( filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
        if( in_array($prefix, $dataToSave['ipv4']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv4'][] = $prefix;
      } elseif( filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
        if( in_array($prefix, $dataToSave['ipv6']) ) {
          continue; // Skip duplicates
        }
        $dataToSave['ipv6'][] = $prefix;
      }
    }
  }

  return sortAndCombineData($dataToSave);
}

function parseGithubIPRanges($jsonContents) {
  $dataToSave = [];
  $dataToSave['combined'] = [];
  $dataToSave['ipv4'] = [];
  $dataToSave['ipv6'] = [];
  $sections = ['hooks', 'web', 'git', 'pages', 'actions'];
  $jsonData = json_decode($jsonContents, true);

  foreach( $sections as $section ) {
    if( isset($jsonData[$section]) ) {
      foreach( $jsonData[$section] as $prefix ) {
        if( filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
          if( in_array($prefix, $dataToSave['ipv4']) ) {
            continue; // Skip duplicates
          }
          $dataToSave['ipv4'][] = $prefix;
        } elseif( filter_var(explode('/', $prefix)[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
          if( in_array($prefix, $dataToSave['ipv6']) ) {
            continue; // Skip duplicates
          }
          $dataToSave['ipv6'][] = $prefix;
        }
      }
    }
  }

  return sortAndCombineData($dataToSave);
}


// eof