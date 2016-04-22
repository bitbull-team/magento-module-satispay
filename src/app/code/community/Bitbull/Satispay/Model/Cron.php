<?php

class Bitbull_Satispay_Model_Cron {
    /**
     * Process pending orders with Satispay method
     */
    public function processPendingOrders() {
        $helper = Mage::helper('satispay');
		if(!$helper->isActive()) {
			return;
		}
		
		$helper->getLogger()->info('Pending orders cron started');
		
		// Get the list of pending orders
		$collection = Mage::getModel('sales/order')->getCollection()
			->join(
				array('payment' => 'sales/order_payment'),
				'main_table.entity_id=payment.parent_id',
				array('payment_method' => 'payment.method')
			);

		// Add base filters to collection
		$collection
			->addFieldToFilter('payment.method', 'satispay')
			->addFieldToFilter('status', Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
		
		// Load collection
		$collection->load();
		$helper->getLogger()->info('Loaded ' . $collection->count() . ' orders');

		// Process orders
		$orderHelper = Mage::helper('satispay/order');
		foreach($collection as $order) {
			try {

				$orderHelper->update($order);

			} catch(Exception $ex) {
				$helper->getLogger()->err('Error in pending orders cron job: ' . $ex->getMessage());
			}
		}
	}
}
