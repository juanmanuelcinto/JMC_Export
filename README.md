# Magento 2 ExportCategories By Juan Manuel Cinto

    ``jmc/module-exportcategories``

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

To exports categories in a CSV File use the next command:
  - bin/magento jmc:export:categories


