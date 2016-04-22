<?php

class Bitbull_Satispay_PaymentController extends Mage_Core_Controller_Front_Action {
    /**
     * Payment index page
     */
    public function indexAction() {
        $helper = Mage::helper('satispay');
        $session = Mage::getSingleton('satispay/session');
        
        // Check session parameters
        if(!$session->getOrderId() || !$session->getCurrency() || !$session->getAmount()) {
            $helper->getLogger()->err('Access to payment page with invalid session');
            $helper->getLogger()->err($session->getData());
        
            return $this->_redirect('checkout/cart/index');
        }
        
        $helper->getLogger()->info('Loading payment page');
        $helper->getLogger()->info($session->getData());
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Charge the selected user
     */
    public function charge_userAction() {
        $helper = Mage::helper('satispay');
        $session = Mage::getSingleton('satispay/session');
        
        // Check session parameters
        if(!$session->getOrderId() || !$session->getCurrency() || !$session->getAmount()) {
            $helper->getLogger()->err('Called charge_user action with invalid session');
            $helper->getLogger()->err($session->getData());
        
            return $this->renderAjaxResponse(array(
                'success' => false,
                'message' => $this->__('Invalid session. Please try placing the order again.'),
            ), 400);
        }
        
        // Check post parameters
        $phoneNumber = $this->getRequest()->getParam('phone_number');
        
        if(!$phoneNumber) {
            $helper->getLogger()->err('Called charge_user action with no phone number');
            $helper->getLogger()->err($session->getData());

            return $this->renderAjaxResponse(array(
                'success' => false,
                'message' => $this->__('Phone number is a required field'),
            ), 400);
        }
        
        $helper->getLogger()->info('Called charge_user action with phone number: ' . $phoneNumber);
        $helper->getLogger()->info($session->getData());
        
        // Process request
        $client = $helper->getClient();
        $user = $client->userCreate($phoneNumber);
        
        // Check user creation response
        if(!isset($user->id)) {
            
            $helper->getLogger()->err('Error in user creation');
            $helper->getLogger()->err($user);
            
            return $this->renderAjaxResponse(array(
                'success' => false,
                'message' => $user->message,
            ), 400);
        }
        
        // Create charge
        $charge = $client->chargeCreate(
            $session->getOrderId(),
            $user->id,
            $session->getCurrency(),
            $session->getAmount()
        );
        
        // Check user creation response
        if(!isset($charge->id)) {
            
            $helper->getLogger()->err('Error in charge request creation');
            $helper->getLogger()->err($charge);
            
            return $this->renderAjaxResponse(array(
                'success' => false,
                'message' => $charge->message,
            ), 400);
        }
        
        $helper->getLogger()->info('Charge request created');
        $helper->getLogger()->info($charge);
        
        // Update session
        $session->setChargeId($charge->id);
        
        // Update order payment informations
        $order = Mage::getModel('sales/order')
            ->load($session->getOrderId(), 'increment_id');
        
        $order->getPayment()
            ->setLastTransId($charge->id)
            ->save();
        
        return $this->renderAjaxResponse(array(
            'success' => true,
        ));
    }
    
    /**
     * Check payment status
     */
    public function check_statusAction() {
        $helper = Mage::helper('satispay');
        $session = Mage::getSingleton('satispay/session');
        
        // Check session parameters
        if(!$session->getChargeId()) {
            $helper->getLogger()->err('Called check_status action with invalid session');
            $helper->getLogger()->err($session->getData());
        
            return;
        }
        
        $helper->getLogger()->info('Called check_status action');
        $helper->getLogger()->info($session->getData());
        
        // Load order
        $order = Mage::getModel('sales/order')
            ->load($session->getOrderId(), 'increment_id');
        
        // If payment is still pending, check the charge request for updates
        if($order->getStatus() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            try {
                Mage::helper('satispay/order')
                    ->update($order, $session->getChargeId());
            } catch(Exception $ex) {
                $helper->getLogger()->err('Error in order update: ' . $ex->getMessage());
                
                return $this->renderAjaxResponse(array(
                    'redirect' => Mage::getUrl('/'),
                ), 500);
            }
        }
        
        // Payment is still pending
        if($order->getStatus() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            return $this->renderAjaxResponse(array(
                'pending' => true,
            ));
        } elseif($order->getStatus() == Mage_Sales_Model_Order::STATE_CANCELED) {
            $session->clear();
            
            return $this->renderAjaxResponse(array(
                'redirect' => Mage::getUrl('checkout/onepage/failure'),
            ));
        } else {
            $session->clear();
            
            return $this->renderAjaxResponse(array(
                'redirect' => Mage::getUrl('checkout/onepage/success'),
            ));
        }
    }
    
    /**
     * Render response object
     */
    protected function renderAjaxResponse($response, $statusCode=200) {
        $this->getResponse()
            ->setHeader('HTTP/1.0', $statusCode, true)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($response));
    }
}
