<?php
class Mage_Adminhtml_Block_Sales_Order_Renderer_Customercomment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
public function render(Varien_Object $_order)
{
$value =  $_order->getData($this->getColumn()->getIndex());
$order = Mage::getModel('sales/order')->load($_order->getId());
$quoteId = $order->getQuoteId();

$store = Mage::getSingleton('core/store')->load(1);
$quoteObject = Mage::getModel('sales/quote')->setStore($store)->load($quoteId);
$val = $quoteObject->getOnestepcheckoutCustomercomment();
//echo "<pre>";
//print_r($quoteObject); exit;
return $val;
 
}
 
}
?>