# Satispay Integration
Magento module for Satispay integration.

Based on official Satispay documentation v. 1.0, 2016/02/15

## System requirements
@todo

## Installation
@todo

## Configuration
@todo

## Usage examples
@todo

## Development guidelines
Here are the guidelines we followed during the development of the module.
 
* The group name is **satispay**
* The module can be installed via **composer** or **modman**; composer uses modman file to know which files to install 
  and where to put them, so the modman file should be always kept up to date
* To **log messages** a specific model is provided: `Satispay_PaymentProcessor_Model_Logger`; it can be instanced by using 
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

### v 0.2.0
* Add basic system configuration flags and corresponding helper methods 

### v 0.1.2
* Add missing comments in Satispay_PaymentProcessor_Model_Logger

### v 0.1.1
* Add missing getter in Satispay_PaymentProcessor_Helper_Data

### v 0.1.0
* First commit and module structure