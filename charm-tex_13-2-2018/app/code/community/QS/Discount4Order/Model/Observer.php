<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */
class QS_Discount4Order_Model_Observer {

	/**
	 * Catch event to send or queue email
	 * with discount code
	 * - Mage::dispatchEvent('sales_order_place_after', array('order'=>$this));
	 * - Mage::dispatchEvent('sales_order_invoice_pay', array('invoice'=>$this));
	 * - Mage::dispatchEvent('sales_order_shipment_save_after', array('invoice'=>$this));
	 * @param object $observer
	 */
	public function catchPlaceOrder($observer)
	{
		$order = $observer->getEvent()->getOrder();
		if($this->isApplicable($observer->getEvent(),$order->getStoreId())
			&& ($order->getBaseSubtotal() >= Mage::getStoreConfig('discount_for_order/discount_for_order/minimal_order_amount',$order->getStoreId()))
			&& $this->allowCategory($order)
			&& $this->allowGroup($order)
		)
		{
			Mage::getModel('discount4order/rule')->createDiscount($order);
		}
	}
	
	public function catchSaveShipment($observer)
	{
		$order = $observer->getEvent()->getShipment()->getOrder();
		if($this->isApplicable($observer->getEvent(),$order->getStoreId())
			&& ($order->getBaseSubtotal() >= Mage::getStoreConfig('discount_for_order/discount_for_order/minimal_order_amount',$order->getStoreId()))
			&& $this->allowCategory($order)
			&& $this->allowGroup($order)
		)
		{
			Mage::getModel('discount4order/rule')->createDiscount($order);
		}
	}
	
	public function catchPayInvoice($observer)
	{
		$order = $observer->getEvent()->getInvoice()->getOrder();
		if($this->isApplicable($observer->getEvent(),$order->getStoreId())
			&& ($order->getBaseSubtotal() >= Mage::getStoreConfig('discount_for_order/discount_for_order/minimal_order_amount',$order->getStoreId()))
			&& $this->allowCategory($order)
			&& $this->allowGroup($order)
		)
		{
			Mage::getModel('discount4order/rule')->createDiscount($order);
		}
	}
	
	/**
	 * Check module and event availability
	 * @param object $event
	 * @param integer $store_id
	 */
	protected function isApplicable($event,$store_id)
	{
		if (Mage::helper('discount4order')->isEnabled($store_id))
			return ($event->getName() == Mage::getStoreConfig('discount_for_order/discount_for_order/email_send_event',$store_id));
		return false;
	}
	
	/**
	 * Check order items categories
	 * @param object $order
	 * @param integer $store_id
	 */
	protected function allowCategory($order)
	{		
		if ($order) {
			if(Mage::getStoreConfig('discount_for_order/discount_for_order/categories_all',$order->getStoreId())) {
				return true;
			}
			$exclude = array();
			if($string = Mage::getStoreConfig('discount_for_order/discount_for_order/categories_exclude',$order->getStoreId())) {
				$exclude = explode(',',$string);
			}
			$allowed = false;
			foreach($order->getAllVisibleItems() as $item) {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				$catIds = $product->getCategoryIds($product);
				$catIdsArray = $catIds;
				$inCat = array_intersect($catIdsArray, $exclude);
				if(count($inCat)>0) {
					//product from exclude category
					if(Mage::getStoreConfig('discount_for_order/discount_for_order/consider_only_all_allowed',$order->getStoreId()) ) {
						//strict all product have to be allowed
						return false;
					}
				}else{
					$allowed = true;
				}
			}
			return $allowed;
		}
		return false;
	}
	
	/**
	 * Check customer group
	 * @param object $order
	 */
	protected function allowGroup($order)
	{		
		if ($order) {
			if ($order->getCustomerGroupId() != 1) {
				return false;
			}
			//enable for all customers
			if(Mage::getStoreConfig('discount_for_order/discount_for_order/customer_group_all',$order->getStoreId())) {
				return true;
			}
			//enable for customers in groups (disabled for guests)
			if ($order->getCustomerIsGuest()) {
				return false;
			}
			$customer = null;
			if($customer_id = $order->getCustomerId()){
				$customer = Mage::getModel('customer/customer')->load($customer_id);
			}
			if(!$customer) {
				return false;
			}
			
			$groups = array();
			if($string = Mage::getStoreConfig('discount_for_order/discount_for_order/customer_group_allowed',$order->getStoreId())) {
				$groups = explode(',',$string);
			}
			return in_array($customer->getGroupId(),$groups) ;
		}
		return false;
	}
	
}