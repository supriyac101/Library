<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */
class QS_Discount4Order_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isEnabled($store_id = null)
	{
		return Mage::getStoreConfig('discount_for_order/discount_for_order/enabled',$store_id);
	}
}