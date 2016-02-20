<?php

class Satispay_PaymentProcessor_Block_Payment_Form extends Mage_Payment_Block_Form
{
    /**
     * Block construction. Set block template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('satispay/payment/form.phtml');
    }
    
    public function getInstructions()
    {
        return Mage::helper('satispay')
            ->getInstructions();
    }
}
