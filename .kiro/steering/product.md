# Product Overview

This repository provides curated lists of CIDR records (IPv4 and IPv6 address ranges) for major datacenter and cloud service providers. The lists are updated automatically and are useful for network administrators, security professionals, and developers who need to identify and manage traffic from cloud sources.

## Three List Types

1. **ASN-based lists** (`lists/asn/`) - Generated from Autonomous System Number data, providing broad coverage of IP ranges associated with cloud providers
2. **Source provider lists** (`lists/source/`) - Generated from cloud providers' official published IP range data for higher accuracy
3. **AWS service lists** (`lists/aws/`) - Detailed lists organized by individual AWS service names (e.g., CloudFront, S3, Route53)

## Key Features

- All lists are in CIDR notation, one record per line
- Lists sorted in ascending order (IPv4 before IPv6)
- Aggregated versions available in `aggregated/` subfolders (simplified/optimized for firewall rules)
- Updated daily at 1:23 AM UTC (ASN and source lists)
- AWS lists updated within 5 minutes when AWS publishes changes
- All data is public domain (MIT License)

## Supported Providers

ASN/Source: AWS, Azure, IBM Cloud, Scaleway, Google Cloud, Cloudflare, DigitalOcean, GitHub, iCloud Private Relay, Linode, Oracle Cloud

AWS Services: 25+ individual services including EC2, S3, CloudFront, Route53, Lambda, and more

## File Naming Convention

- `provider.txt` - Both IPv4 and IPv6
- `provider-ipv4.txt` - IPv4 only
- `provider-ipv6.txt` - IPv6 only
- `all.txt` - All providers combined
- `other.txt` - Providers not listed individually
- `last-updated.txt` - Timestamp of last update (RFC 3339 format)

**Always prefer aggregated lists** (`aggregated/` subfolder) when possible for optimal performance.
