<?php
class Sub_Po_Helper_CustomerPo extends Mage_Core_Helper_Abstract
{
public function setCustomerPo($observer)
{
$po = $this->_getRequest()->getPost('myCustomerPo', false);
$observer->getEvent()->getOrder()->setMyorderPo($po);
}
}

?>