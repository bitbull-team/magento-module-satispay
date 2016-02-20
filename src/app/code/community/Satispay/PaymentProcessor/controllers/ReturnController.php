<?php

class Satispay_PaymentProcessor_PaymentController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
        $session = Mage::getSingleton('satispay/session');
        
        $helper->getLogger()->info('Loaded payment page');
        $helper->getLogger()->info($session->getData());
    }
}
