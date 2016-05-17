<?php

class Bitbull_Satispay_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'satispay';
    protected $_formBlockType = 'satispay/payment_form';
    protected $_infoBlockType = 'satispay/payment_info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    
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
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return Mage::helper('satispay')->isCurrencyCodeSupported($currencyCode);
    }
    
    /**
     * Validate payment method information object
     *
     * @return Bitbull_Satispay_Model_Payment
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
     * @return Bitbull_Satispay_Model_Payment
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
    
    /**
     * Refund specified amount
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return Bitbull_Satispay_Model_Payment
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $chargeId = $payment->getLastTransId();
        if($chargeId) {
            
            $helper = Mage::helper('satispay');
            $client = $helper->getClient();
            
            $helper->getLogger()->info('Issuing a refund for charge ' . $chargeId);
            $refund = $client->refundCreate(
                $chargeId,
                $payment->getOrder()->getBaseCurrencyCode(),
                round($amount * 100)
            );
            
            if(!$refund || !isset($refund->id)) {
                $helper->getLogger()->err('Error issuing a refund for charge ' . $chargeId);
                $helper->getLogger()->err($refund);
                
                Mage::throwException(($refund && isset($refund->message)) ? $refund->message : 'Invalid server response');
            }
            
            $helper->getLogger()->info('Refund issued successfully');
            $helper->getLogger()->info($refund);

        } else {
            Mage::throwException('Charge ID not set for the payment');
        }
        
        return $this;
    }
}
