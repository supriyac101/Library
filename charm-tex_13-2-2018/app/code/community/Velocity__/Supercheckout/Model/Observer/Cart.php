<?php
/**
 * Observer class is to set the modal box status true when customer added a product in cart
  */
class Velocity_Supercheckout_Model_Observer_Cart extends Varien_Event_Observer {

    public function __construct() {
        
    }

    public function setShoppingCartSession($observer) {
        $event = $observer->getEvent();
        
        // Session variable set to know that product is added into cart
        Mage::getSingleton('core/session')->setShowModalBox(true);
    }

}

?>