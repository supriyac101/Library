<?php
class Velocity_Supercheckout_Model_ShoppingCartPopup extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('supercheckout/shoppingcartpopup');
     }
}
?>