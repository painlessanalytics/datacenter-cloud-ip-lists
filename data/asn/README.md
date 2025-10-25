# ASN Data
This directory contains ASN (Autonomous System Number) data files used for identifying datacenter and cloud service providers based on their ASN.

Some of the files in this directory are sourced from external repositories, others are maintained by [Painless Analytics](https://www.painlessanalytics.com) and pull requests from the community.

## ASN.txt
The `ASN.txt` file is a plain text file that lists the ASNs associated with various datacenter and cloud service providers. Each line in the file represents a single ASN.

The ASN.txt file is sourced from the [X4BNet/lists_vpn](https://github.com/X4BNet/lists_vpn) repository. In this version, each leading 'AS' prefix has been removed as well as the comment following the number to ensure compatibility with systems that expect numeric ASN values.

## as.csv
The `as.csv` file is a CSV (Comma-Separated Values) file that contains detailed information about autonomous systems. This file includes columns such as ASN, organization handle, and organization name.

The as.csv file is sourced from the [ipverse/asn-info](https://github.com/ipverse/asn-info) repository. This version is slightly modified, the last column is enclosed in quotes to ensure proper formatting for importing to a database.

### as.csv to MySQL or PostgreSQL
The `as.csv` file is formatted to be compatible with MySQL's `LOAD DATA INFILE` command for easy import into a database table.

Create a MySQL table with the following structure to match the `as.csv` file:

```sql
CREATE TABLE IF NOT EXISTS `asn` (
  `as_number` bigint UNSIGNED NOT NULL,
  `as_handle` varchar(100) NOT NULL,
  `as_description` varchar(200) NOT NULL,
  PRIMARY KEY (`as_number`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

The `as.csv` file can be imported into a MySQL database table using the following command. Note that you may need to adjust the file path and table name as necessary. This can only be performed on the same machine where the MySQL server is running, and you may need to enable local file loading if not enabled.

```sql
LOAD DATA INFILE '/path/to/as.csv'
INTO TABLE your_table_name
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;
```

MySQL Workbench and phpMyAdmin also provide graphical interfaces to import CSV files into database tables. Make sure to skip the first row as it contains column name information.

The CSV file can also be imported into a PostgreSQL database using the following command:

```sql
COPY your_table_name (as_number, as_handle, as_description)
FROM '/path/to/as.csv'
WITH (FORMAT csv, HEADER true);
```

The CSV file can also be imported into other database systems that support CSV import functionality, but the specific commands may vary.

## Update Process
The ASN data files are automatically updated on a weekly basis using a GitHub Actions workflow defined in `.github/workflows/update-ip2asn-data.yml`. This workflow downloads the latest ASN data from the respective sources and commits any changes to the repository.
