<?php

class Satispay_PaymentProcessor_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'satispay';
    protected $_formBlockType = 'satispay/payment_form';
    protected $_infoBlockType = 'satispay/payment_info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    
    /**
     * Whether current operation is order placement
     *
     * @return bool
     */
    private function _isPlaceOrder()
    {
        $info = $this->getInfoInstance();
        if ($info instanceof Mage_Sales_Model_Quote_Payment) {
            return false;
        } elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
            return true;
        }
    }
    
    /**
     * Order increment ID getter (either real from order or a reserved from quote)
     *
     * @return string
     */
    private function _getOrderId()
    {
        $info = $this->getInfoInstance();

        if ($this->_isPlaceOrder()) {
            return $info->getOrder()->getIncrementId();
        } else {
            if (!$info->getQuote()->getReservedOrderId()) {
                $info->getQuote()->reserveOrderId();
            }
            return $info->getQuote()->getReservedOrderId();
        }
    }
    
    /**
     * Currency getter
     *
     * @return string
     */
    private function _getCurrency()
    {
        $info = $this->getInfoInstance();
        if ($this->_isPlaceOrder()) {
            return $info->getOrder()->getBaseCurrencyCode();
        } else {
            return $info->getQuote()->getQuoteBaseCurrencyCode();
        }
    }
    
    /**
     * Grand total getter
     *
     * @return float
     */
    private function _getAmount()
    {
        $info = $this->getInfoInstance();
        if ($this->_isPlaceOrder()) {
            return (float)$info->getOrder()->getQuoteBaseGrandTotal();
        } else {
            return (float)$info->getQuote()->getBaseGrandTotal();
        }
    }
    
    /**
     * Validate payment method information object
     *
     * @return Satispay_PaymentProcessor_Model_Payment
     */
    public function validate()
    {
        parent::validate();
        
        $helper = Mage::helper('satispay');
        if(!$helper->getStaging() && !$helper->getSecurityToken()) {
            $helper->getLogger()->err('Satispay security token is not set.');
            Mage::throwException($helper->__('Satispay security token is not set.'));
        }

        return $this;
    }
    
    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitializeNeeded set to true
     *
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return Satispay_PaymentProcessor_Model_Payment
     */
    public function initialize($paymentAction, $stateObject)
    {
        // Transaction informations
        $orderId = $this->_getOrderId();
        $currency = $this->_getCurrency();
        $amount = $this->_getAmount();
        
        $helper = Mage::helper('satispay');
        $helper->getLogger()->info('Payment initialization');
        $helper->getLogger()->info('Order ID: ' . $orderId);
        $helper->getLogger()->info('Currency: ' . $currency);
        $helper->getLogger()->info('Amount: ' . $amount);
        
        // Clear session to avoid collisions
        $session = Mage::getSingleton('satispay/session');
        $session->clear();
        
        // Store transaction informations in session
        $session->setOrderId($orderId)
            ->setCurrency($currency)
            ->setAmount(round($amount * 100));

        return $this;
    }
    
    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        Mage::helper('satispay')->getLogger()
            ->info('Redirecting customer to payment page');
        
        return Mage::getUrl('satispay/payment/index', array(
            '_secure'=>true
        ));
    }
}