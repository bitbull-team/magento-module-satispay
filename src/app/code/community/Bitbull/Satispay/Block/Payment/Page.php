<?php

class Bitbull_Satispay_Block_Payment_Page extends Mage_Core_Block_Template
{
    protected $_session;
    protected $_order;
    
    /**
     * Return satispay session singleton
     */
    protected function getSession()
    {
        if(is_null($this->_session)) {
            $this->_session = Mage::getSingleton('satispay/session');
        }
        
        return $this->_session;
    }
    
    
    /**
     * Return current order model
     */
    protected function getOrder()
    {
        if(is_null($this->_order)) {
            $session = $this->getSession();
            $this->_order = Mage::getModel('sales/order')
                ->load($session->getOrderId(), 'increment_id');
        }
        
        return $this->_order;
    }
    
    /**
     * Return telephone number set on billing address
     */
    protected function getBillingTelephone()
    {
        $telephone = '';
        
        $order = $this->getOrder();
        if($order && ($billing = $order->getBillingAddress())) {
            $telephone = $billing->getTelephone();
        }
        
        return $telephone;
    }
    
    /**
     * Return country codes list
     */
    public function getCountryCodes()
    {
        return Mage::helper('satispay/phone')
            ->getCountryCodes();
    }
    
    /**
     * Return country code from billing telephone number if set,
     * and the configuration default one otherwise
     */
    public function getDefaultCountryCode()
    {
        // Try to get the country code from billing telephone
        $telephone = $this->getBillingTelephone();
        
        if($telephone) {
            $countryCodes = $this->getCountryCodes();
            foreach($countryCodes as $countryCode) {
                $l = strlen($countryCode);
                if(substr($telephone, 0, $l) == $countryCode) {
                    return $countryCode;
                }
            }
        }
        
        // Return default from configuration
        return Mage::helper('satispay')
            ->getDefaultCountryCode();
    }
    
    /**
     * Return phone number from billing telephone
     * removing country prefix if necessary
     */
    public function getPhoneNumber()
    {
        $telephone = $this->getBillingTelephone();
        if(!$telephone) {
            return '';
        }
        
        $countryCode = $this->getDefaultCountryCode();
        $l = strlen($countryCode);
        if(substr($telephone, 0, $l) == $countryCode) {
            $telephone = substr($telephone, $l);
        }
        
        return $telephone;
    }
}
