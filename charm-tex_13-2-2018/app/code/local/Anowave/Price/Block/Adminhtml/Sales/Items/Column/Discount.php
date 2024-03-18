<?php
class Anowave_Price_Block_Adminhtml_Sales_Items_Column_Discount extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
	public function _toHtml()
	{
		$discount = (float) $this->getItem()->getCustomerDiscount();
		
		if ($discount)
		{
			$order = Mage::getModel('sales/order')->load
			(
				$this->getItem()->getOrderId()
			);
			
			
			$name = join(chr(32), array
			(
				$order->getBillingAddress()->getFirstname(),
				$order->getBillingAddress()->getLastname()
			));

			return parent::_toHtml() . '<div style="background:rgb(255,251,224); padding:5px; margin:0px -4px 0px -4px;">Customer discount for <strong> ' . $name . '</strong><div>' .  Mage::helper('core')->currency($discount, true, false) . '</div></div>';
		}
		
		return parent::_toHtml();
	}
}