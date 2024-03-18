<?php
class Anowave_Price_Block_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{
    protected function _afterLoadCollection()
    {
    	foreach ($this->getCollection() as $item)
    	{
    		Mage::dispatchEvent('catalog_product_get_final_price_admin', array
        	(
        		'product' 	=> $item, 
        		'customer' 	=> Mage::getSingleton('adminhtml/session_quote')->getCustomer()
        	));
    	}
    }
}