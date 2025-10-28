# Data Sources

This directory contains files that list the URLs of various cloud service providers' published IP address ranges. These files are used as sources to generate the IP address range lists found in the `lists/source/` directory.

## Data Sources List

Source lists include the following providers and their respective public lists:

* Amazon Web Services (AWS): https://ip-ranges.amazonaws.com/ip-ranges.json
* Microsoft Azure: https://www.microsoft.com/en-us/download/details.aspx?id=56519
* Google Cloud (GCloud): https://www.gstatic.com/ipranges/cloud.json
* DigitalOcean: https://digitalocean.com/geo/google.csv
* Cloudflare: https://www.cloudflare.com/ips-v4 and https://www.cloudflare.com/ips-v6
* Oracle Cloud: https://docs.oracle.com/iaas/tools/public_ip_ranges.json
* Linode (Akamai): https://geoip.linode.com/
* GitHub: https://api.github.com/meta

## Under Consideration for Future Versions

The following providers have publicly available IP range data, but are not currently included in the automated fetching process. These may be considered for inclusion in future versions.

* IBM Cloud: https://cloud.ibm.com/docs/vpc?topic=vpc-public-ip-ranges
* Scaleway: https://www.scaleway.com/en/docs/account/reference-content/scaleway-network-information/
* Fastly: https://api.fastly.com/public-ip-list
* Google (not cloud): https://www.gstatic.com/ipranges/goog.json
* Salesforce: https://ip-ranges.salesforce.com/ip-ranges.json


## Data Sources not planned for Inclusion

The following providers do not have IP range data available.

* Akamai: NO LIST AVAILABLE (Linode may cover some Akamai ranges))
* Heroku: NO LIST AVAILABLE
* Alibaba Cloud: NO LIST AVAILABLE
* Tencent Cloud: NO LIST AVAILABLE
* Rackspace: NO LIST AVAILABLE
* DreamHost: NO LIST AVAILABLE
* OVHcloud: NO LIST AVAILABLE
* Vultr: NO LIST AVAILABLE
* Hetzner: NO LIST AVAILABLE
* Huawei Cloud: NO LIST AVAILABLE
* Dell Cloud: NO LIST AVAILABLE
* Cisco Cloud: NO LIST AVAILABLE
* Wasabi: NO LIST AVAILABLE
* Vercel: NO LIST AVAILABLE
