<?php
class Velocity_Supercheckout_Model_Mysql4_ShoppingCartPopup extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('supercheckout/shoppingcartpopup', 'config_id');
     }
}
?>