# BIN (Not so Binary Executables)

The `bin` directory contains executable scripts and programs that are used for processing and managing the datacenter and cloud IP lists.

## fix_ipverse_csv.php

This PHP script is used to fix formatting issues in the `as.csv` file sourced from the [ipverse/asn-info](https://github.com/ipverse/asn-info) repository. The script ensures that the last column in the CSV file is properly enclosed in quotes, which is necessary for correct parsing and importing into databases.

This script does not require composer dependencies and can be run directly using the PHP command line interface.

## process_ip2asn.php

This PHP script processes the `ip2asn-v4.tsv.gz` and `ip2asn-v6.tsv.gz` files sourced from [iptoasn.com](https://iptoasn.com/). The script reads the TSV files, extracts relevant information, and generates optimized data files that can be used for efficient IP to ASN lookups.

This script relies on external PHP libraries managed via Composer. See the "Composer Dependencies" section below for more information.

## generate_asn_lists.php

This PHP script generates IP address range lists for datacenter and cloud service providers based on ASN data. It reads the ASN data files and produces plain text files containing CIDR notation entries for each provider

See [data/sources/](../data/sources/) for more information on the sources used.

## cidr.include.php

This PHP file contains common functions and utilities for handling CIDR notation and IP address manipulation. It is included by other scripts in the `bin` directory to provide shared functionality.

## Composer Dependencies

The scripts in the `bin` directory may rely on external PHP libraries. These dependencies are managed using [Composer](https://getcomposer.org/).

Required dependencies:

- [mlocati/ip-lib](https://github.com/mlocati/ip-lib): A PHP library for handling and manipulating IP addresses.
- [composer/ca-bundle](https://github.com/composer/ca-bundle): A PHP library for managing a list of CA certificates.

To install the required dependencies, navigate to the root directory of the project and run:

```bash
composer install
```

This will download and install the necessary libraries into the `vendor/` directory, making them available for use in the scripts.