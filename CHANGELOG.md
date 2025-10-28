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

## [1.0] - 2025-10-27

Initial release of datacenter and cloud IP lists repository.

- Generated ASN based IP lists for major datacenter and cloud service providers.
- Generated source based IP lists for major cloud service providers.
- Implemented GitHub Actions workflows for automated data updates.
