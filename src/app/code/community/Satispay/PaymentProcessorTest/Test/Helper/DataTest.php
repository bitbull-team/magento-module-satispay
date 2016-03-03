<?php

use Satispay_PaymentProcessor_Helper_Data as HelperData;

/**
 * Helper class test case
 *
 */
class Satispay_PaymentProcessorTest_Test_Helper_DataTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Satispay_PaymentProcessor_Helper_Data
     */
    private $helper;
    
    private $originalValueActive;
    private $originalValueInstructions;
    private $originalValueTitle;
    private $originalValueStaging;
    private $originalValueSecurityToken;
    private $originalValueDefaultCountryCode;
    private $originalValueDebug;

    protected function setUp()
    {
        $this->helper = new HelperData();
        
        $this->originalValueActive = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_ACTIVE);
        
        $this->originalValueInstructions = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_INSTRUCTIONS);
        
        $this->originalValueTitle = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_TITLE);
        
        $this->originalValueStaging = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_STAGING);
        
        $this->originalValueSecurityToken = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_SECURITY_TOKEN);
        
        $this->originalValueDefaultCountryCode = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEFAULT_COUNTRY_CODE);
        
        $this->originalValueDebug = $this->app()->getStore()
            ->getConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEBUG);
    }

    protected function tearDown()
    {
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_ACTIVE, $this->originalValueActive);
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_INSTRUCTIONS, $this->originalValueInstructions);
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_TITLE, $this->originalValueTitle);
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_STAGING, $this->originalValueStaging);
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_SECURITY_TOKEN, $this->originalValueSecurityToken);
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEFAULT_COUNTRY_CODE, $this->originalValueCountryCode);
        $this->app()->getStore()
            ->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEBUG, $this->originalValueDebug);
    }

    public function testItReturnsTrueForIsActiveFlagBasedOnConfigurationValue()
    {
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_ACTIVE, '1');
        $this->assertTrue($this->helper->isActive());
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_ACTIVE, '0');
        $this->assertFalse($this->helper->isActive());
    }
    
    public function testItReturnsInstructionsBasedOnConfigurationValue()
    {
        $value = uniqid();
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_INSTRUCTIONS, $value);
        $this->assertSame($this->helper->getInstructions(), $value);
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_INSTRUCTIONS, null);
        $this->assertEmpty($this->helper->getInstructions());
    }
    
    public function testItReturnsTitleBasedOnConfigurationValue()
    {
        $value = uniqid();
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_TITLE, $value);
        $this->assertSame($this->helper->getTitle(), $value);
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_TITLE, null);
        $this->assertEmpty($this->helper->getTitle());
    }
    
    public function testItReturnsTrueForIsStagingFlagBasedOnConfigurationValue()
    {
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_STAGING, '1');
        $this->assertTrue($this->helper->getStaging());
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_STAGING, '0');
        $this->assertFalse($this->helper->getStaging());
    }
    
    public function testItReturnsSecurityTokenBasedOnConfigurationValue()
    {
        $value = uniqid();
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_SECURITY_TOKEN, $value);
        $this->assertSame($this->helper->getSecurityToken(), $value);
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_SECURITY_TOKEN, null);
        $this->assertEmpty($this->helper->getSecurityToken());
    }
    
    public function testItReturnsDefaultCountryCodeBasedOnConfigurationValue()
    {
        $value = uniqid();
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEFAULT_COUNTRY_CODE, $value);
        $this->assertSame($this->helper->getDefaultCountryCode(), $value);
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEFAULT_COUNTRY_CODE, null);
        $this->assertEmpty($this->helper->getDefaultCountryCode());
    }
    
    public function testItReturnsTrueForIsDebugFlagBasedOnConfigurationValue()
    {
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEBUG, '1');
        $this->assertTrue($this->helper->isDebug());
        $this->app()->getStore()->setConfig(HelperData::XML_PATH_PAYMENT_SATISPAY_DEBUG, '0');
        $this->assertFalse($this->helper->isDebug());
    }
}
