# datacenter-cloud-ip-lists
Datacenter and Cloud Services Public IP Lists.

# Special Thanks
Special thanks to the following projects and contributors and their maintainers for providing valuable data sources that made this repository possible:

## Projects
- [X4BNet/lists_vpn](https://github.com/X4BNet/lists_vpn) - For providing a comprehensive list of ASNs associated with datacenter and cloud service providers.
- [ipverse/asn-info](https://github.com/ipverse/asn-info) - For providing detailed ASN information used in this project.
- [iptoasn.com](https://iptoasn.com/) - For providing IP to ASN mapping data.
- [shivammathur/setup-php](https://github.com/shivammathur/setup-php) - For providing PHP setup actions for GitHub workflows.
- Deep in the weeds:
-- [mlocati/ip-lib](https://github.com/mlocati/ip-lib) - A PHP library for IP address manipulation and validation.
-- [composer/composer](https://github.com/composer/composer) - Dependency Manager for PHP.
-- [PHP](https://www.php.net) - The programming language used for scripting in this project.
-- [GitHub](https://github.com/) - For providing GIT, automating workflows, and continuous integration.

Their contributions to the open-source community are greatly appreciated! Please consider supporting their work by visiting their sites and contributing feedback, code, and/or financially if possible.  

## Contributors
- [Painless Analytics](https://www.painlessanalytics.com) and founder [Angelo Mandato](https://angelo.mandato.com) - For maintaining and contributing to this repository.
- YOUR NAME HERE! Call us old school Gen-Xers... we like to pay it forward and give credit when credit is due, like how it was done in the 90's and early 2000's. If you have contributed to this project in any way, please let us know so we can add your name here!

# Directory Structure
- `data/asn/`: Contains ASN data files used for identifying datacenter and cloud service providers.
- `data/ip2asn/`: Contains data files that map IP address ranges to Autonomous System Numbers (ASNs).
- `bin/`: Contains executable scripts and programs for processing and managing the datacenter and cloud IP lists.
- `.github/workflows/`: Contains GitHub Actions workflows for automating data updates and processing.

# Update Process
The data files in this repository are automatically updated on a weekly basis using GitHub Actions workflows defined in the `.github/workflows/` directory. These workflows download the latest data from the respective sources and process them as needed.

# License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

# Contributing
Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request (pull requests are recommended). Please share your name if you would like to be credited in the contributors section of this README file.

# Change Log
All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md) file.
