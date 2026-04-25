# Technology Stack

## Core Technologies

- **Language**: PHP 8.3+
- **Package Manager**: Composer
- **Automation**: GitHub Actions
- **Required PHP Extensions**: mbstring, zlib, gmp, curl

## Dependencies

Managed via Composer in `bin/composer.json`:

- `mlocati/ip-lib` (^1.22) - IP address manipulation and CIDR handling
- `composer/ca-bundle` (^1.5) - CA certificate management for secure downloads

## Project Structure

- `bin/` - PHP scripts for data processing
- `data/` - Source data (ASN mappings, IP2ASN data, provider sources)
- `lists/` - Generated output lists (asn/, source/, aws/)
- `.github/workflows/` - GitHub Actions automation

## Common Commands

### Setup

```bash
# Install PHP dependencies
composer install --working-dir=bin/

# Or from bin directory
cd bin && composer install
```

### Manual Script Execution

```bash
# Fix ipverse CSV formatting
php bin/fix_ipverse_csv.php

# Process IP2ASN data
php -d memory_limit=512M bin/process_ip2asn.php

# Update AWS provider lists
php -d memory_limit=512M bin/update_provider_aws.php

# Update source provider lists
php -d memory_limit=512M bin/update_provider_lists.php
```

**Note**: Use `memory_limit=512M` for processing scripts to handle large datasets.

### GitHub Actions Workflows

```bash
# Trigger scheduled workflow manually
gh workflow run scheduled-workflow.yml

# Trigger AWS update manually
gh workflow run update-provider-aws.yml

# View workflow runs
gh run list
```

## Automation Schedule

- **Daily updates**: 1:23 AM UTC (ASN and source provider lists)
- **AWS updates**: Event-driven via AWS Lambda SNS notifications (within 5 minutes of AWS IP range changes)

## Key Libraries and Functions

The `bin/cidr.include.php` file provides shared utilities:

- `aggregateIPv4Cidrs()` / `aggregateIPv6Cidrs()` - Merge and simplify CIDR blocks
- `sortIPv4Cidrs()` / `sortIPv6Cidrs()` - Sort CIDR lists
- `sortAndCombineData()` - Combine IPv4 and IPv6 lists
- `cidrDownload()` - Concurrent multi-URL downloads with cURL
- GMP-based IPv6 manipulation functions

## Data Sources

Configured in `data/sources/sources.json`:

- AWS: `https://ip-ranges.amazonaws.com/ip-ranges.json`
- Azure: Microsoft Download Center (multiple regions)
- Google Cloud: `https://www.gstatic.com/ipranges/cloud.json`
- Cloudflare: `https://www.cloudflare.com/ips-v4` and `ips-v6`
- GitHub: `https://api.github.com/meta`
- And more...

## Output Format

All generated lists:
- Plain text files (`.txt`)
- One CIDR per line
- Sorted ascending (IPv4 before IPv6)
- RFC 3339 timestamps in `last-updated.txt`
