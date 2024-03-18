<?php
class Custom_Orderplaceafter_Model_Order_Observer extends Varien_Object
{
    public function customFieldsAddToOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();     // Table: sales_flat_order
        $orderId = $order->getId();
        $order = Mage::getModel('sales/order')->load($order->getId());
        $orderId = $order->getId();
        
        $my_field = $_REQUEST['order_po'];
	//exit;
        $order->setorder_po($my_field);
        
        
        $order->save();
        
        return $this;
    }
}
?>  