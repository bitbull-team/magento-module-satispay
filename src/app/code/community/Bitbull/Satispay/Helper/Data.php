<?php
class Bitbull_Satispay_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML Paths for configuration constants
     */
    const XML_PATH_PAYMENT_SATISPAY_ACTIVE = 'payment/satispay/active';
    const XML_PATH_PAYMENT_SATISPAY_INSTRUCTIONS = 'payment/satispay/instructions';
    const XML_PATH_PAYMENT_SATISPAY_TITLE = 'payment/satispay/title';
    const XML_PATH_PAYMENT_SATISPAY_STAGING = 'payment/satispay/staging';
    const XML_PATH_PAYMENT_SATISPAY_SECURITY_TOKEN = 'payment/satispay/security_token';
    const XML_PATH_PAYMENT_SATISPAY_DEFAULT_COUNTRY_CODE = 'payment/satispay/default_country_code';
    const XML_PATH_PAYMENT_SATISPAY_DEBUG = 'payment/satispay/debug';

    /** @var  Bitbull_Satispay_Model_Logger */
    protected $_logger;
    
    /** @var array */
    protected $_supportedCurrencyCodes = array('EUR');

    public function __construct()
    {
        $this->_logger = Mage::getModel('satispay/logger', $this->isDebug());
    }

    public function isActive()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_SATISPAY_ACTIVE);
    }
    
    public function getTitle()
    {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_SATISPAY_TITLE);
    }
    public function getInstructions()
    {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_SATISPAY_INSTRUCTIONS);
    }

    public function getStaging()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_SATISPAY_STAGING);
    }
    
    public function getSecurityToken()
    {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_SATISPAY_SECURITY_TOKEN);
    }
    
    public function getDefaultCountryCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_SATISPAY_DEFAULT_COUNTRY_CODE);
    }

    public function isDebug()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_SATISPAY_DEBUG);
    }
    
    public function getClient() {
        $client = new Satispay_Core_Client();
        
        return $client->setStaging($this->getStaging())
            ->setSecurityToken($this->getSecurityToken());
    }
    
    /**
     * Check whether specified currency code is supported or not
     *
     * @param string $code
     * @return bool
     */
    public function isCurrencyCodeSupported($code)
    {
        return in_array($code, $this->_supportedCurrencyCodes);
    }

    /**
     * @return Bitbull_Satispay_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}