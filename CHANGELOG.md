# Datacenter and Cloud IP Lists Changelog

This is the Datacenter and Cloud IP Lists change log/changelog.

All notable changes to this project will be documented in this file.

**Keep a Changelog**
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)

Each version section should start with a H2 (`## [<version>] - <date>`): Two hash tags and a space, the *version* number
in hard brackets, a space-dash-space (` - `), and the release *date* in ISO date format (ISO 8601 / RFC 3339 date)
*YYYY-MM-DD*. The proceeding line may contain 1 or more sentences describing the purpose of the release.
A blank line is added to separate the heading/paragraph from the list of changes.
Changes are listed, each item prefixed with a minus (-) character. Tabs may be used to indent the list.
A blank line is added to separate the list from the next heading/paragraph. The ordering of the versions
is from the most recent release at the top to the oldest at the bottom. A special "Unreleased"
section may be added at the top for upcoming changes that have not yet been released.

**Semantic Versioning**
This project adheres *somewhat* to [Semantic Versioning](https://semver.org/).

The first version (`<version>`) of a MAJOR or MINOR release will exclude the second dot followed by zero (`.0`).
For example `2.0` will be used rather than `2.0.0`. Otherwise Semantic Versioning is strictly followed.
*We don't waste zeros!*

## [Unreleased]

TBD

## [1.3.4] - 2026-07-18

Added a safeguard against shrunken provider source data.

- Provider source lists are no longer overwritten when a source returns a valid-but-partial response that parses to fewer than 5 records, unless the previous version already had fewer than 10 records. This prevents a truncated or mostly-empty source response from silently replacing a good previous list.

## [1.3.3] - 2026-04-26

Added CloudFlare ASN's as the CloudFlare public list is not nearly complete.

- Added data/asn/specific/cloudflare.txt

## [1.3.2] - 2025-12-07

Documented the new AWS source lists and SNS notification functionality.

- Added documentation for the new AWS source lists organized by service name and the all.txt file.
- Added examples in documentation how to use the aws specific lists.
- Documented the SNS notification functionality for AWS CloudFront Origin Facing list, if others find it useful to contact me to make the SNS topic public.

## [1.3.1] - 2025-12-07

Expanded source lists for AWS with SNS notification functionality.

- Tested AWS source list sending SNS notification functionality via GitHub Actions workflow.
- Fixed bug in update-provider-lists.yml where last-updated.txt should be added after checking for changes, not before.

## [1.3] - 2025-12-07

Expanded source lists for AWS.

* Added advanced AWS source lists organized by service name and an all.txt file containing all AWS IP ranges.
* Updated GitHub Actions workflow to generate the new AWS source lists that is executed via an API call from an AWS Lambda function (when the list is updated).
* Note: Version 1.3 was not released via a Git tag and was released for testing the workflow. The next release will be version 1.3.x once this functionality is tested and verified.


## [1.2] - 2025-11-08

Added Apple iCloud Private Relay IP ranges list.

- Added parsing function for Apple iCloud Private Relay IP ranges from their published CSV file.

## [1.1] - 2025-11-07

Some minor improvements.

- Lists are now updated daily instead of weekly
- Now downloading the ASN.txt from X4BNet/lists_vpn without any modifications
- Added ASN-add.txt and ASN-remove.txt files to allow adding or removing ASNs from the main ASN list without modifying the original file
- Added azure and cloudflare to post-download list processing to handle their multiple source files
- Azure lists now include US Government, China, and Germany IP ranges
- Added ASN lists for IBM and Scaleway

## [1.0] - 2025-10-27

Initial release of datacenter and cloud IP lists repository.

- Generated ASN based IP lists for major datacenter and cloud service providers.
- Generated source based IP lists for major cloud service providers.
- Implemented GitHub Actions workflows for automated data updates.
