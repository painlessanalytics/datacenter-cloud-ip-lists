# datacenter-cloud-ip-lists
Datacenter and Cloud Services Public IP Lists.

## Introduction
This repository provides curated lists of IPv4 and IPv6 address ranges associated with major datacenter and cloud service providers. 
These lists are useful for network administrators, security professionals, and developers who need to identify and manage traffic 
from these sources. The data is sourced from reputable projects and is updated weekly to ensure accuracy.

This list was heavily influenced by the excellent work done by [X4BNet/lists_vpn](https://github.com/X4BNet/lists_vpn). Our needs 
require IPv6 as well as IPv4 addresses with additional lists specifically listing the top cloud service providers utilizing their 
source lists rather than rely on ARN data. Many thanks to X4BNet for their foundational work!

All lists provided in this repository are in the public domain and can be freely used and distributed. See the License section 
below for more details.

All lists are saved as txt files with records separated by new lines. Each line contains either IPv4, IPv6, or both address ranges 
in CIDR notation. Each record is in CIDR format which represents a block of IP addresses assigned to a specific datacenter or 
cloud service provider. All lists are sorted in ascending order, with IPv4 addresses listed before IPv6 addresses when both are present.

## ASN Lists
The lists are available in folders found in the `[lists/asn/](lists/asn/)` directory. Each list is named according to the datacenter or cloud service provider it represents. The naming convention follows the format: `provider-name[-ip-version].txt`. Each named cloud provider may have up to 3 variations of the lists that include both IPv4 and IPv6 addresses, IPv4 only, or IPv6 only. The provider name 'all' is used to indicate that the list contains IP address ranges of ALL providers combined into a single file. The provider name 'other' is used to indicate that the list contains IP address ranges of other providers other than the ones listed in the provider specific files.
 
Examples of list file names:
- `aws.txt`: Contains both IPv4 and IPv6 address ranges for Amazon Web Services.
- `googlecloud-ipv4.txt`: Contains only IPv4 address ranges for Google Cloud Platform.
- `azure-ipv6.txt`: Contains only IPv6 address ranges for Microsoft Azure.
- `all.txt`: Contains both IPv4 and IPv6 address ranges for all listed providers combined.
- `other.txt`: Contains both IPv4 and IPv6 address ranges for all other providers except for the ones listed in the provider specific files.

The `all` list can be thought of as an aggregate of the `other` list and individual provider lists.

## Source Provider Lists
In addition to the ASN based lists, there are also lists generated from specific cloud service providers' published IP range data. These lists are found in the `[lists/source/](lists/source/)` directory. Each list is named according to the cloud service provider it represents, following the same naming convention as the ASN lists.

Source provider lists do not have an `all` or `other` variation as they are specific to each provider's published data.

## Aggregated Lists
In addition to the individual provider lists, there is a sub folder named `aggregated/` that contains an aggregated (simplified) version of the lists. Aggregated lists simplify multiple CIDR blocks when possible.

Note that the aggregated lists may not be identical to the sum of the individual provider lists due to the simplification process.

**What is an aggregated (simplified) list?**
An aggregated list combines multiple CIDR blocks into larger blocks when possible, reducing the total number of entries. This is useful for optimizing firewall rules and routing tables, as it minimizes the number of entries that need to be processed.

# Special Thanks
Special thanks to the following projects and contributors and their maintainers for providing valuable data sources that made this repository possible:

## Thank You Projects
- [X4BNet/lists_vpn](https://github.com/X4BNet/lists_vpn) - For providing a comprehensive list of ASNs associated with datacenter and cloud service providers.
- [ipverse/asn-info](https://github.com/ipverse/asn-info) - For providing detailed ASN information used in this project.
- [iptoasn.com](https://iptoasn.com/) - For providing IP to ASN mapping data.
- [shivammathur/setup-php](https://github.com/shivammathur/setup-php) - For providing PHP setup actions for GitHub workflows.
- Deep in the weeds:
  - [mlocati/ip-lib](https://github.com/mlocati/ip-lib) - A PHP library for IP address manipulation and validation.
  - [composer/composer](https://github.com/composer/composer) - Dependency Manager for PHP.
  - [PHP](https://www.php.net) - The programming language used for scripting in this project.
  - [GitHub](https://github.com/) - For providing GIT, automating workflows, and continuous integration.

Their contributions to the open-source community are greatly appreciated! Please consider supporting their work by visiting their sites and contributing feedback, code, and/or financially if possible.  

## Thank You Contributors
- [Painless Analytics](https://www.painlessanalytics.com) and founder [Angelo Mandato](https://angelo.mandato.com) - For maintaining and contributing to this repository.
- YOUR NAME HERE!

Call us old school Gen-Xers... we like to pay it forward and give credit when credit is due, like how it was done in the 90's and early 2000's. If you have contributed to this project in any way, please let us know so we can add your name here!

## Directory Structure
- [`.github/workflows/`](.github/workflows/): Contains GitHub Actions workflows for automating data updates and processing.
- [`bin/`](bin/): Contains executable scripts for processing and managing the datacenter and cloud IP lists.
- [`data/asn/`](data/asn/): Contains ASN data files used for identifying datacenter and cloud service providers.
- [`data/ip2asn/`](data/ip2asn/): Contains data files that map IP address ranges to Autonomous System Numbers (ASNs).
- [`data/sources/`](data/sources/): Contains URLs for obtaining IP address ranges published by cloud service providers.
- [`lists/asn/`](lists/asn/): Contains the generated IP address range lists for datacenters and cloud service providers from ASN data.
- [`lists/source/`](lists/source/): Contains the generated IP address range lists for specific cloud service providers using their published ip-range data.

## Update Process
The data files in this repository are automatically updated on a weekly basis using GitHub Actions workflows defined in the `.github/workflows/` directory. These workflows download the latest data from the respective sources and process.

**Scheduled to run at 4:23 AM UTC every Sunday**

The update process consists of the following steps:
1. **Update ASN Data**: Downloads the latest ASN data.
2. **Process ASN Data**: Processes the downloaded ASN data to generate updated IP address range lists for datacenter and cloud service providers.
3. **Update Source Data**: Downloads the latest IP range data from specific cloud service providers (coming soon) and saves in the same list format.

The workflow takes about 3 to 4 minutes to complete. It should be safe to assume after 4:30 AM UTC on Sundays that the data in this repository is up to date and ready for use.

## Usage
Clone the repository and use the lists found in the `lists/asn/` and `lists/source/` directories as needed. Each list is a plain text file containing CIDR notation entries, one per line.

To manually download the latest `all.txt` in this project, you can use the following command:
```
curl -O https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/master/lists/asn/all.txt
```

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing
Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request (pull requests are recommended). Please share your name if you would like to be credited in the contributors section of this README file.

## Change Log
All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md) file.
