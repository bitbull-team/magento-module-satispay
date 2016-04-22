<?php

class Bitbull_Satispay_Model_System_Config_Source_Countrycode {
    public function toOptionArray() {
        $countryCodes = Mage::helper('satispay/phone')
            ->getCountryCodes();
        
        $options = array();
        foreach($countryCodes as $countryCode) {
            $options[$countryCode] = $countryCode;
        }
        
        return $options;
    }
}
