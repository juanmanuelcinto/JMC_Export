# Mage2 Module JMC Export

    ``jmc/module-export``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Specifications](#markdown-header-specifications)


## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/JMC`
 - Enable the module by running `php bin/magento module:enable JMC_ExportCategories`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Specifications

### Export Categories

 - run *bin/magento jmc:export:categories* <store-id>
   - **Generated file**: var/categories.csv
   - **Parameter**: store (Optional)


### Export Products

 - run *bin/magento jmc:export:products* <store-id>
	- **Generated file**: var/products.csv
   - **Parameter**: store (Required)