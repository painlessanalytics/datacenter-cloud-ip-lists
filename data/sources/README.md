# Data Sources
This directory contains files that list the URLs of various cloud service providers' published IP address ranges. These files are used as sources to generate the IP address range lists found in the `lists/source/` directory.

## Data Sources List

Version 1.0:
* Amazon Web Services (AWS): https://ip-ranges.amazonaws.com/ip-ranges.json
* Google Cloud (GCloud): https://www.gstatic.com/ipranges/cloud.json
* DigitalOcean: https://digitalocean.com/geo/google.csv
* Cloudflare: https://www.cloudflare.com/ips-v4 and https://www.cloudflare.com/ips-v6

Version 1.1:
* Microsoft Azure: https://www.microsoft.com/en-us/download/details.aspx?id=56519
* Salesforce: https://ip-ranges.salesforce.com/ip-ranges.json
* Oracle Cloud: https://docs.oracle.com/iaas/tools/public_ip_ranges.json
* Linode (Akamai): https://geoip.linode.com/

Version 1.2:
* IBM Cloud: https://cloud.ibm.com/docs/vpc?topic=vpc-public-ip-ranges
* Scaleway: https://www.scaleway.com/en/docs/account/reference-content/scaleway-network-information/


## Data Sources not planned for automated fetching
The following providers have IP range data available, but are not currently included in the automated fetching process. This inbformation is for reference purposes only.

* Microsoft 365: https://docs.microsoft.com/en-us/microsoft-365/enterprise/microsoft-365-ip-web-service?view=o365-worldwide
* Google Workspace: https://support.google.com/a/answer/10026322?hl=en
* Adobe: https://helpx.adobe.com/enterprise/kb/ips-ranges.html
* Akamai: https://www.akamai.com/us/en/about/our-thinking/research/akamai-ip-addresses.jsp
* Fastly: https://api.fastly.com/public-ip-list
* CloudFront: https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/LocationsOfEdgeServers.html
* Microsoft Azure DevOps: https://learn.microsoft.com/en-us/azure/devops/migrate/migration-import-git-repo?view=azure-devops#locations-of-azure-devops-services
* Shopify: https://help.shopify.com/en/manual/online-store/traffic-and-speed/using-cdn#shopify-cdn-ip-addresses
* Heroku: https://devcenter.heroku.com/articles/heroku-ips
* Zoom: https://support.zoom.us/hc/en-us/articles/360045038571-Zoom-IPs-and-Domains
* Slack: https://api.slack.com/ips
* Dropbox: https://www.dropbox.com/help/desktop-web/dropbox-ip-addresses
* GitHub: https://docs.github.com/en/free-pro-team@latest/github/setting-up-and-managing-your-github-user-account/managing-access-to-your-organizations-repositories/about-githubs-ip-addresses
* Twitter: https://help.twitter.com/en/rules-and-policies/twitter-ips
* Pinterest: https://help.pinterest.com/en/business/article/pinterest-ip-addresses
* ZoomInfo: https://support.zoominfo.com/hc/en-us/articles/360025667053-ZoomInfo-IP-Ranges

* Alibaba Cloud: https://www.alibabacloud.com/help/doc-detail/40654.htm
* Tencent Cloud: https://intl.cloud.tencent.com/document/product/213/6091
* Rackspace: https://support.rackspace.com/how-to/rackspace-cloud-ip-addresses/
* DreamHost: https://help.dreamhost.com/hc/en-us/articles/215769888-What-are-DreamHost-s-IP-addresses-
* OVHcloud: https://www.ovh.com/world/public-cloud/ips/
* Fastly: https://api.fastly.com/public-ip-list
* Vultr: https://www.vultr.com/media/vultr-public-ip-ranges.txt
* Hetzner: https://www.hetzner.com/cloud/ips
