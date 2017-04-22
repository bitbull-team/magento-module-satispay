# Satispay Integration
Magento module for Satispay integration.

Based on official Satispay documentation v. 1.0, 2016/02/15

http://bit.ly/API-online

## System requirements
This extension supports the following versions of Magento:

*	Community Edition (CE) version 1.7.x, 1.8.x, 1.9.x

*	Enterprise Edition (EE) version 1.12.x, 1.13.x, 1.14.x

Cron jobs need to be configured in the Magento installation, otherwise orders will remain in *Pending Payment* status. This is because the transaction status check is scheduled to happen every 10 minutes as part of a cron job.

## Installation
### Using Composer
To install *Bitbull_Satispay* module via Composer you need to add this repository to your project's *composer.json* file:
```
  "repositories":[
    {"type": "vcs", "url": "git@github.com:bitbull-team/magento-module-satispay.git"}
  ]
```

Once done, you will be able to add it to the requirements:
```
  "require":{
    "bitbull/satispay":"*"
  },
```

With this configuration, the last version of the extension will be installed running `composer install` or `composer update`.

### Using Modman
`modman clone https://github.com/bitbull-team/magento-module-satispay.git`

### Zip Download
Finally, you can install the extension by downloading the archive package, extract it and copy the content of the *src* folder over your Magento root directory.

## Configuration
After installing the extension login to your shop's admin area and perform the following steps to enable the extension:
* Refresh the cache (if enabled)
* Go to **System** > **Configuration** > **Payment Methods** > ** Bitbull Satispay Integration**
* Set *Enabled* to *Yes*
* Past your *Security Token* in the dedicated field
* Press *Save*

From the same section you can also personalise the payment method title (what the customer sees in the payment method list), the instruction (what appears when the payment method is selected), the default phone country code and wether to activate the *Test Mode* or debug informations in the logs.

## Usage Examples
After activating the extension you can simply add a product to cart and proceed to checkout.
Satispay will be displayed between the payment method options and, if selected, you will be redirected to a page where you will be able to specify the mobile number you want to charge for the order. A notification will appear in the customer's app, asking to confirm the payment.
Mind that the extension will only appear if it's enabled and the website currency is **EUR**. Other currencies are not supported by Satispay at the moment.

## Development guidelines
Here are the guidelines we followed during the development of the module.
 
* The group name is **satispay**
* The module can be installed via **composer** or **modman**; composer uses modman file to know which files to install 
  and where to put them, so the modman file should be always kept up to date
* To **log messages** a specific model is provided: `Bitbull_Satispay_Model_Logger`; it can be instanced by using 
  the default helper this way: `helper('satispay')->getLogger()`; for the list of methods please refer to the model 
  class source code
* The following annotated comments are used:
    * `// @todo - followed by a description of what has to be completed`
    * `// @fixme - followed by a description of what has to be fixed`
* **Commit messages** and **comments** are in **English**; verbs use the **second person** and not the third 
  (e.g.: *Add something*, *Fix something* and not *Adds something*, *Fixes something*)
  
## Release notes
The extension uses [semantic versioning v 2.0.0](http://semver.org/) convention.

Note: you can use the **{break}.{feature}.{fix}** formula to easily remember which number has to be changed after some
code changes.

### v 0.2.1
* Add support for Magento versions 1.7.x and 1.8.x

### v 0.2.0
* Add basic system configuration flags and corresponding helper methods 

### v 0.1.2
* Add missing comments in Bitbull_Satispay_Model_Logger

### v 0.1.1
* Add missing getter in Bitbull_Satispay_Helper_Data

### v 0.1.0
* First commit and module structure

## License
Licensed under the Open Software License version 3.0

## Testing
This package contains unit tests that can be executed enabling the extension *Bitbull_SatispayTest*.
[EcomDev_PHPUnit](https://github.com/EcomDev/EcomDev_PHPUnit) is a dependency, for more details about how to configure the environment refer to the [official guide](https://github.com/EcomDev/EcomDev_LayoutCompiler/blob/master/docs/INSTALLATION.md).
