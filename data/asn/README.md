# ASN Data
This directory contains ASN (Autonomous System Number) data files used for identifying datacenter and cloud service providers based on their ASN.

Some of the files in this directory are sourced from external repositories, others are maintained by [Painless Analytics](https://www.painlessanalytics.com) and pull requests from the community.

## ASN.txt
The `ASN.txt` file is a plain text file that lists the ASNs associated with various datacenter and cloud service providers. Each line in the file represents a single ASN.

The ASN.txt file is sourced from the [X4BNet/lists_vpn](https://github.com/X4BNet/lists_vpn) repository.

## as.csv
The `as.csv` file is a CSV (Comma-Separated Values) file that contains detailed information about autonomous systems. This file includes columns such as ASN, organization handle, and organization name.

The as.csv file is sourced from the [ipverse/asn-info](https://github.com/ipverse/asn-info) repository.

## Update Process
The ASN data files are automatically updated on a weekly basis using a GitHub Actions workflow defined in `.github/workflows/update-ip2asn-data.yml`. This workflow downloads the latest ASN data from the respective sources and commits any changes to the repository.
