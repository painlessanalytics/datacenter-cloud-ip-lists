# Project Structure

## Directory Organization

```
datacenter-cloud-ip-lists/
├── .github/workflows/     # GitHub Actions automation
├── .kiro/steering/        # AI assistant guidance documents
├── bin/                   # PHP processing scripts
├── data/                  # Source data files
│   ├── asn/              # ASN provider mappings
│   ├── ip2asn/           # IP-to-ASN mapping data
│   └── sources/          # Provider source URLs (sources.json)
└── lists/                 # Generated output lists
    ├── asn/              # ASN-based lists
    ├── source/           # Source provider lists
    └── aws/              # AWS service-specific lists
```

## Key Directories

### `.github/workflows/`
GitHub Actions workflows for automated updates:
- `scheduled-workflow.yml` - Daily orchestrator (1:23 AM UTC)
- `update-ip2asn-data.yml` - Download IP2ASN data
- `process-ip2asn-data.yml` - Process ASN mappings
- `update-provider-lists.yml` - Generate source provider lists
- `update-provider-aws.yml` - Generate AWS service lists (event-driven)

### `bin/`
PHP scripts and utilities:
- `cidr.include.php` - Shared CIDR manipulation functions
- `fix_ipverse_csv.php` - Fix ASN CSV formatting
- `process_ip2asn.php` - Process IP2ASN data
- `update_provider_aws.php` - Generate AWS lists
- `update_provider_lists.php` - Generate source provider lists
- `composer.json` - PHP dependencies

### `data/`
Source data files (inputs):
- `asn/` - ASN provider mappings and lists
  - `specific/` - Provider-specific ASN files (aws.txt, azure.txt, etc.)
  - `ASN.txt` - Master ASN list
  - `as.csv` - ASN metadata from ipverse
- `ip2asn/` - IP-to-ASN mapping files
  - `ip2asn-v4.tsv.gz` - IPv4 mappings
  - `ip2asn-v6.tsv.gz` - IPv6 mappings
- `sources/sources.json` - Provider API/download URLs
- `last-updated.txt` - Data update timestamp

### `lists/`
Generated output lists (outputs):

Each subdirectory (`asn/`, `source/`, `aws/`) contains:
- Provider-specific `.txt` files (e.g., `aws.txt`, `azure-ipv4.txt`)
- `aggregated/` subfolder with optimized versions
- `last-updated.txt` - Generation timestamp

**List naming pattern**:
- `provider.txt` - Combined IPv4 + IPv6
- `provider-ipv4.txt` - IPv4 only
- `provider-ipv6.txt` - IPv6 only

## File Conventions

### List Files
- Plain text, one CIDR per line
- Sorted ascending (IPv4 before IPv6)
- No headers or comments
- CIDR notation only (e.g., `192.0.2.0/24`, `2001:db8::/32`)

### Timestamps
- `last-updated.txt` files use RFC 3339 format
- Example: `2026-04-24T01:23:45+00:00`

### Aggregated Lists
- Located in `aggregated/` subfolders
- Simplified/merged CIDR blocks for optimal performance
- **Preferred for production use** (firewall rules, routing tables)

## Data Flow

1. **Input**: Download source data (ASN mappings, provider APIs)
2. **Processing**: PHP scripts parse and aggregate CIDRs
3. **Output**: Generate sorted lists in `lists/` directories
4. **Commit**: GitHub Actions commits changes to repository

## Special Files

- `CHANGELOG.md` - Project change history
- `LICENSE` - MIT License
- `README.md` - Comprehensive documentation
- `.gitignore` - Excludes `bin/vendor/`, `bin/composer.lock`, `*.bak`

## Workflow Dependencies

Sequential execution order:
1. Update IP2ASN data
2. Process IP2ASN data
3. Update provider lists

AWS updates run independently when triggered by SNS notifications.
