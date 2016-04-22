<?php
class Bitbull_Satispay_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * Update order status according to charge status
     */
    public function update($order, $chargeId=null) {
        // Check order status
        if($order->getStatus() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            throw new Exception('Invalid order status: ' . $order->getStatus());
        }

        // Load chargeId from payment if not set
        if(!$chargeId) {
            $chargeId = $order->getPayment()
                ->getLastTransId();
        }
        
        if(!$chargeId) {
            throw new Exception('Could not load charge id from transaction');
        }

        // Check transaction status
        $helper = Mage::helper('satispay');
        $charge = $helper->getClient()
            ->chargeGet($chargeId);
        
        if(!$charge || !isset($charge->id)) {
            $helper->getLogger()->err('Invalid server response for charge #' . $chargeId);
            $helper->getLogger()->err($charge);
            
            throw new Exception('Invalid server response');
        }
        
        // Update order
        $helper->getLogger()->info('Updating order ' . $order->getIncrementId() . ' from charge ' . $chargeId);
        $helper->getLogger()->info($charge);
        
        // Handle order update depending on payment status
        if($charge->status == Satispay_Core_Client::PAYMENT_STATUS_SUCCEESS) {
            $helper->getLogger()->info('Payment succeeded: invoicing order');
            
            // Update order status
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
            $order->addStatusHistoryComment('Charge in ' . $charge->status . ' status.');
            $order->sendNewOrderEmail();
            $order->save();
            
            // Generate invoice
            $invoice = Mage::getModel('sales/service_order', $order)
    			->prepareInvoice();
    
    		if (!$invoice->getTotalQty()) {
    		    $helper->getLogger()->err('Skipping invoicing for order ' . $order->getIncrementId() . ': invalid total quantity');
    			return;
    		}
    		
    		$invoice->setTransactionId($chargeId);
    		$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
    		$invoice->register();
    		$transactionSave = Mage::getModel('core/resource_transaction')
    			->addObject($invoice)
    			->addObject($invoice->getOrder());
    
    		$transactionSave->save();

        } elseif(in_array($charge->status, array(Satispay_Core_Client::PAYMENT_STATUS_DECLINED, Satispay_Core_Client::PAYMENT_STATUS_FAILURE))) {
            $helper->getLogger()->info('Charge in ' . $charge->status . ' status: cancelling order');
            
            $order->cancel();
			$order->addStatusHistoryComment('Charge in ' . $charge->status . ' status.');
			$order->save();
			
        } else {
            $helper->getLogger()->info('No action taken for status ' . $charge->status);
        }
    }
}
