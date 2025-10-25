# IP to ASN
This directory contains data files that map IP address ranges to Autonomous System Numbers (ASNs). This information is useful for network analysis, cybersecurity, and internet infrastructure research.

## Source of files
The data files in this directory are sourced from publicly available datasets from [https://iptoasn.com/](https://iptoasn.com/).

## Data Files
The following data files are included in this directory:

- `ip2asn-v4.tsv.gz`: A compressed TSV file containing IPv4 address ranges mapped to ASNs.
- `ip2asn-v6.tsv.gz`: A compressed TSV file containing IPv6 address ranges mapped to ASNs.

## File Format
The data files are in TSV (Tab-Separated Values) format.

Each row in the ip2asn file includes the following fields in this specific order:
- `ip_start`: The starting IP address of the range.
- `ip_end`: The ending IP address of the range.
- `asn`: The Autonomous System Number associated with the IP range.
- `country_code`: The country code where the ASN is registered.
- `organization description`: The organization that owns the ASN.
