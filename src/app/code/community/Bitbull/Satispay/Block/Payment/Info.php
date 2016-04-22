<?php

class Bitbull_Satispay_Block_Payment_Info extends Mage_Payment_Block_Info
{
    /**
     * Block construction. Set block template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bitbull/satispay/payment/info.phtml');
    }
    
    protected function getTitle()
    {
        return Mage::helper('satispay')
            ->getTitle();
    }
}
