# Datacenter and Cloud Services Public IP Lists

Datacenter and Cloud Services Public IP Lists, By [Painless Analytics](https://www.painlessanalytics.com) and founder [Angelo Mandato](https://angelo.mandato.com).

## Introduction

This repository provides curated lists of [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing) records of IPv4 and IPv6 address ranges associated with major datacenter and cloud service providers. These lists are useful for network administrators, security professionals, and developers who need to identify and manage traffic from these sources. The data is sourced from reputable projects and is updated weekly to ensure accuracy.

This list was heavily influenced by the excellent work done by [X4BNet/lists_vpn](https://github.com/X4BNet/lists_vpn). Our needs 
require IPv6 as well as IPv4 addresses with additional lists specifically listing the top cloud service providers utilizing their 
source lists rather than rely on ARN data. Many thanks to X4BNet for their foundational work!

There are also source based lists for specific cloud service providers using their published
IP range data. See the Source Provider Lists section below for more details.

**NEW!** There is now a dedicated AWS folder containing lists organized by AWS service name (e.g. route53.txt). See the [AWS Specific Service Lists](#aws-specific-service-lists) section below for more details.

All lists provided in this repository are in the public domain and can be freely used and distributed. See the License section 
below for more details.

All lists are saved as txt files with records separated by new lines. Each line contains either IPv4 and/or IPv6 in CIDR notation. Each record is in CIDR format which represents a block of IP addresses assigned to a specific datacenter or cloud service provider. All lists are sorted in ascending order, with IPv4 addresses listed before IPv6 addresses when both are present. Lists that are specifically for IPv4 or IPv6 only will have a `-ipv4` or `-ipv6` suffix in the file name.

All lists (asn, service, and the AWS services) include a sub folder called `aggregated/` containing simplified versions of the lists. See the [Aggregated Lists](#aggregated-lists) section below for more details. **PLEASE USE THE AGGREGATED LISTS WHEN POSSIBLE**.

## Three types of lists in this repository

There are three types of lists provided in this repository:
1. ASN based lists found in the [lists/asn/](lists/asn/) directory.
2. Source provider based lists found in the [lists/source/](lists/source/) directory.
3. **NEW!** AWS specific service based lists found in the [lists/aws/](lists/aws/) directory.

ASN based lists are generated from ASN data that maps IP address ranges to Autonomous System Numbers (ASNs) associated with datacenter and cloud service providers. Though this method provides a broad coverage of IP ranges, it may include some addresses that are not directly published by the providers themselves, leased to third parties, or used for other purposes.

Source provider based lists are generated from specific cloud service providers' published IP range data. These lists are more accurate for the respective providers as they reflect the IP ranges that the providers themselves have made publicly available. However, not all providers publish their IP ranges, so coverage may be limited to those that do.

AWS specific service based lists are generated from AWS published IP range data organized by AWS service name. These lists provide detailed IP ranges for individual AWS services, allowing for more granular filtering and analysis.

## Purpose of Multiple List Types

Providing both ASN based and source provider based lists allows users (YOU) to choose the most appropriate data. ASN based lists offer broader coverage, while source provider based lists provide more accurate and up-to-date information for specific providers. The AWS specific service lists add an additional layer of granularity for users who need to manage traffic related to individual AWS services.

## ASN Lists

The lists are available in folders found in the [lists/asn/](lists/asn/) directory. Each list is named according to the datacenter or cloud service provider it represents. The naming convention follows the format: `provider-name[-ip-version].txt`. Each named cloud provider may have up to 3 variations of the lists that include both IPv4 and IPv6 addresses, IPv4 only, or IPv6 only. The provider name 'all' is used to indicate that the list contains IP address ranges of ALL providers combined into a single file. The provider name 'other' is used to indicate that the list contains IP address ranges of other providers other than the ones listed in the provider specific files.

Examples of list file names:

- `aws.txt`: Contains both IPv4 and IPv6 address ranges for Amazon Web Services.
- `aws-ipv4.txt`: Contains only IPv4 address ranges for Amazon Web Services.
- `azure-ipv6.txt`: Contains only IPv6 address ranges for Microsoft Azure.
- `all.txt`: Contains both IPv4 and IPv6 address ranges for all listed providers combined.
- `other.txt`: Contains both IPv4 and IPv6 address ranges for all other providers except for the ones listed in the provider specific files.

The `all` list is an aggregate of the `other` list and individual provider lists.

The exception to the above naming convention the file `last-updated.txt` which contains the date and time when the lists were last updated. See the Last Updated Information section below for more details.

Currently supported source providers include:

- [all.txt](lists/asn/aggregated/all.txt) (All providers)
- [aws.txt](lists/asn/aggregated/aws.txt) (Amazon Web Services)
- [azure.txt](lists/asn/aggregated/azure.txt) (Microsoft Azure)
- [ibm.txt](lists/asn/aggregated/ibm.txt) (IBM Cloud)
- [other.txt](lists/asn/aggregated/other.txt) (All other providers not listed individually)
- [scaleway.txt](lists/asn/aggregated/scaleway.txt) (Scaleway)

Please see the [lists/asn/aggregated/](lists/asn/aggregated/) directory for all of the variations of the aggregated versions of these ASN provider lists.

To see a list of all providers and their respective ASNs, refer to the [data/asn/specific/](data/asn/specific/) folder.

## Source Provider Lists

In addition to the ASN based lists, there are also lists generated from specific cloud service providers' published IP range data. These lists are found in the [lists/source/](lists/source/) directory. Each list is named according to the cloud service provider it represents, following the same naming convention as the ASN lists.

Source provider lists do not have an `all` or `other` variation as they are specific to each provider's published data.

Currently supported source providers include:

- [aws.txt](lists/source/aggregated/aws.txt) (Amazon Web Services)
- [azure.txt](lists/source/aggregated/azure.txt) (Microsoft Azure)
- [cloudflare.txt](lists/source/aggregated/cloudflare.txt) (Cloudflare)
- [digitalocean.txt](lists/source/aggregated/digitalocean.txt) (DigitalOcean)
- [github.txt](lists/source/aggregated/github.txt) (GitHub)
- [google-cloud.txt](lists/source/aggregated/google-cloud.txt) (Google Cloud)
- [icloud.txt](lists/source/aggregated/icloud.txt) (Apple iCloud Private Relay)
- [linode.txt](lists/source/aggregated/linode.txt) (Linode)
- [oracle-cloud.txt](lists/source/aggregated/oracle-cloud.txt) (Oracle Cloud)

Please see the [lists/source/aggregated/](lists/source/aggregated/) directory for all of the variations of the aggregated versions of these source provider lists.

## AWS Specific Service Lists

**NEW!**

In addition to the general source provider lists, there is a dedicated AWS folder containing lists organized by AWS service name. This folder includes an `all.txt` file that contains all AWS IP ranges combined.
These lists are found in the [lists/aws/](lists/aws/) directory. Each list is named according to the AWS service it represents, following the format: `service-name.txt`. The `all.txt` file contains all AWS IP ranges combined into a single file. These lists do not include separate ipv4 and ipv6 files, instead each file contains both IPv4 and IPv6 address ranges. If you only need IPv4 or IPv6 addresses, you can filter the lists with your preferred programming language or command line tools.

The [lists/aws/](lists/aws/) directory also includes an `aggregated/` sub folder containing simplified versions of the AWS service lists. Please use the aggregated lists when possible. See the [Aggregated Lists](#aggregated-lists) section below for more details.

AWS Services include:

- [all.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/all.txt): All AWS IP ranges combined.
- [amazon.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/amazon.txt): General Amazon IP ranges.
- [amazon_appflow.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/amazon_appflow.txt): Amazon AppFlow IP ranges.
- [amazon_connect.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/amazon_connect.txt): Amazon Connect IP ranges.
- [api_gateway.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/api_gateway.txt): Amazon API Gateway IP ranges.
- [aurora_dsql.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/aurora_dsql.txt): Amazon Aurora DSQL IP ranges.
- [chime_meetings.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/chime_meetings.txt): Amazon Chime Meetings IP ranges.
- [chime_voiceconnector.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/chime_voiceconnector.txt): Amazon Chime Voice Connector IP ranges.
- [cloud9.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/cloud9.txt): Amazon Cloud9 IP ranges.
- [cloudfront.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/cloudfront.txt): Amazon CloudFront IP ranges.
- [cloudfront_origin_facing.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/cloudfront_origin_facing.txt): Amazon CloudFront Origin Facing IP ranges.
- [codebuild.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/codebuild.txt): Amazon CodeBuild IP ranges.
- [dynamodb.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/dynamodb.txt): Amazon DynamoDB IP ranges.
- [ebs.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/ebs.txt): Amazon EBS IP ranges.
- [ec2.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/ec2.txt): Amazon EC2 IP ranges.
- [ec2_instance_connect.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/ec2_instance_connect.txt): Amazon EC2 Instance Connect IP ranges.
- [globalaccelerator.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/globalaccelerator.txt): Amazon Global Accelerator IP ranges.
- [ivs_low_latency.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/ivs_low_latency.txt): Amazon IVS Low Latency IP ranges.
- [ivs_realtime.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/ivs_realtime.txt): Amazon IVS Realtime IP ranges.
- [kinesis_video_streams.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/kinesis_video_streams.txt): Amazon Kinesis Video Streams IP ranges.
- [media_package_v2.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/media_package_v2.txt): Amazon Media Package V2 IP ranges.
- [route53.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/route53.txt): Amazon Route 53 IP ranges.
- [route53_healthchecks.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/route53_healthchecks.txt): Amazon Route 53 Health Checks IP ranges.
- [route53_healthchecks_publishing.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/route53_healthchecks_publishing.txt): Amazon Route 53 Health Checks Publishing IP ranges.
- [route53_resolver.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/route53_resolver.txt): Amazon Route 53 Resolver IP ranges.
- [s3.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/s3.txt): Amazon S3 IP ranges.
- [workspaces_gateways.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/workspaces_gateways.txt): Amazon WorkSpaces Gateways IP ranges.

To see when the AWS IP ranges were last updated, refer to the file: [last-updated.txt](https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/refs/heads/main/lists/aws/aggregated/last-updated.txt)

The AWS specific service lists are updated when the source data from AWS is updated. A separate GitHub Actions workflow handles this update process and is triggered by an AWS Lambda function when the [IP ranges](https://docs.aws.amazon.com/vpc/latest/userguide/aws-ip-ranges.html) change.

When the `cloudfront_origin_facing.txt` list is updated, an SNS notification is sent to an AWS SNS topic for my [angelo.mandato.com](https://angelo.mandato.com/) website. If you would like to be notified via SNS when this list is updated (or if you have a use case for one of the other lists), please [contact me](https://angelo.mandato.com/contact/) to express interest having the SNS topic becoming public.

Note, a AWS specific service list may contain IP ranges that overlap with other lists. For example the `cloudfront_origin_facing.txt` list contains IP ranges that are also found in the `cloudfront.txt` and `all.txt` lists.

### Example use of the AWS CloudFront Origin Facing List with the NGINX Realip module

If you are using NGINX as a web server, reverse proxy, or load balancer and want to only trust the X-Forwarded-For IP address header when it originates from an AWS CloudFront Origin Facing IP, you can use the `cloudfront_origin_facing.txt` list to create your realip.conf NGINX configuration. Here is an example of how to set this up:

```bash
#/bin/bash
cd /etc/nginx/conf.d/ # Change to where you save your nginx configuration files
echo -e "# realip.conf\n# Generated from the cloudfront_origin_facing.txt list from https://github.com/painlessanalytics/datacenter-cloud-ip-lists\n" > realip.conf
# Include the local network(s) as needed
echo "set_real_ip_from 127.0.0.1;" >> realip.conf
echo "set_real_ip_from 192.168.0.0/16;" >> realip.conf
echo "set_real_ip_from 172.16.0.0/12;" >> realip.conf
echo "set_real_ip_from 10.0.0.0/8;" >> realip.conf
# Download the latest CloudFront Origin Facing aggregated list
curl -o /tmp/cloudfront_origin_facing.txt https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/master/lists/aws/aggregated/cloudfront_origin_facing.txt
cat /tmp/cloudfront_origin_facing.txt | awk '{print "set_real_ip_from " $0 ";"}' >> realip.conf
echo -e "real_ip_header X-Forwarded-For;\nreal_ip_recursive on;\n" >> realip.conf
rm /tmp/cloudfront_origin_facing.txt
```

Then restart or reload NGINX to apply the changes:

```bash
sudo systemctl restart nginx
```

### Example retrieving only the IPv4 addresses from the EC2 AWS Service List

If you only need the IPv4 addresses from a specific AWS service list, you can use command line tools like `grep` or `awk` to filter the list. For example, to retrieve only the IPv4 addresses from the `ec2.txt` list, you can use the following command strictly matches IPv4 addresses that match CIDR notation:

```bash
curl -o ec2.txt https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/master/lists/aws/aggregated/ec2.txt
grep -E '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\/[0-9]+' ec2.txt > ec2-ipv4.txt
```

### Example retrieving the IPv4 and IPv6 addresses from the Route53 AWS Service List

A quick and dirty way split IPv4 and IPv6 is to assume all IPv4 addresses contain a dot (`.`) and IPv6 addresses do not:

```bash
curl -o route53.txt https://raw.githubusercontent.com/painlessanalytics/datacenter-cloud-ip-lists/master/lists/aws/aggregated/route53.txt
grep "\." route53.txt > route53-ipv4.txt
grep -v "\." route53.txt > route53-ipv6.txt
```

Note: The IP addresses in this entire repository are in CIDR notation and should not include a colon (`:`) unless they are IPv6 addresses. The period (`.`) is only found in IPv4 addresses. Colons are part of the standard IPv6 address notation, so filtering by the presence of a colon is unreliable for distinguishing IPv4 from IPv6. If an IPv6 address is ever specified with a port (not relevant for these lists), it uses bracket notation, e.g., `[2001:db8::1]:80`. The examples above use a period (`.`) to filter IPv4 addresses, and the `-v` option to extract the inverse for IPv6 addresses. There are many other ways to filter the lists using your preferred programming language or command line tools.

## Aggregated Lists

In addition to the lists (`asn/`, `source/`, and `aws/`), there is a sub folder named `aggregated/` in each folder list that contains aggregated (simplified) versions of the lists. Aggregated lists simplify multiple CIDR blocks where possible and are ideal for use in web server proxy configurations, firewall rules, and routing tables to minimize the number of entries that need to be processed.

Note that the aggregated lists may not be identical to the sum of the individual provider lists due to the simplification process.

**What is an aggregated (simplified) list?**
An aggregated list combines multiple CIDR blocks into larger blocks where possible, reducing the total number of entries. This is useful for optimizing firewall rules and routing tables as it minimizes the number of entries that need to be processed. For the most part the aggregated lists will have fewer entries than the non-aggregated lists and should be used when possible. The non-aggregated lists are still provided for reference, completeness, and can be used to audit the records provided by their sources.

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
- [`lists/aws/`](lists/aws/): **New!** Contains the generated IP address range lists for AWS services using their published ip-range data.

## Last Updated Information

Each of the `data/`, `lists/asn/`, `lists/source/`, `lists/aws/` directories contain a `last-updated.txt` file that indicates the date and time when the lists in that folder were last updated. This timestamp is automatically generated during the update process to provide users with information about the freshness of the data. The date and time is in ISO 8601 date format, see [RFC 3339](https://tools.ietf.org/html/rfc3339).

## Update Process for ASN and Source Provider Lists

The data in folders `data/`, `lists/asn/`, and `lists/source/` are updated on a <u><i>daily</i></u> basis using GitHub Actions workflows defined in the `.github/workflows/` directory. These workflows download the latest data from the respective sources.

**Scheduled to run at 1:23 AM UTC every day**

The update process consists of the following steps:

1. **Update ASN Data**: Downloads the latest ASN data.
2. **Process ASN Data**: Processes the downloaded ASN data to generate updated IP address range lists for datacenter and cloud service providers.
3. **Update Source Data**: Downloads the latest IP range data from specific cloud service providers and saves in the same list format.

The workflow takes about 3 to 4 minutes to complete. GitHub can take up to 20 minutes to start execution. It should be safe to assume after 2:00 AM UTC the data in this repository is up to date and ready for use.

### Update Process for AWS Specific Service Lists

The [AWS specific service lists](#aws-specific-service-lists) are updated when the source data from AWS is updated, within 5 minutes or less. A separate GitHub Actions workflow handles this update process and is triggered by a AWS Lambda function when the [IP ranges change SNS notification](https://aws.amazon.com/blogs/aws/subscribe-to-aws-public-ip-address-changes-via-amazon-sns/) is received from AWS. In other words, the AWS service lists are updated only when AWS updates their published IP ranges which can range from 2-3 times a week to 3-4 times a day. 

Currently the best way to know when to check this repository for updated AWS service lists is to subscribe to AWS's AmazonIpSpaceChanged SNS topic. Please see Jeff Barr's post [Subscribe to AWS Public IP Address Changes via SNS](https://aws.amazon.com/blogs/aws/subscribe-to-aws-public-ip-address-changes-via-amazon-sns/).

If you have a specific use case that requires SNS updates for a specific service, please [contact me](https://angelo.mandato.com/contact/) to discuss your needs. I am currently considering making the `cloudfront_origin_facing` SNS topic public for others to subscribe to.

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
