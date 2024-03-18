<?php

/**
 * Block class to get the shopping cart popup settings as per the store 
 */
class Velocity_Supercheckout_Block_Modalbox extends Mage_Core_Block_Text_Tag_Js {

    /**
     * Function to add modal box after add to cart product
     * Created By - Brajendra
     * @return Vss_ShoppingCartPopup_Block_Modalbox 
     */
    protected function _toHtml() {
        
        $store_id = Mage::app()->getStore()->getStoreId();
        $settings = Mage::getStoreConfig('checkout/supercheckout/settings', $store_id);

        $settings_array = unserialize($settings);
        Mage::getSingleton('core/session')->setShoppingCartPopUpSettings($settings); // Set popup settings in session as per the store/website
        //return parent::_toHtml();
    }

}

?>
