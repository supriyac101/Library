<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */
class QS_Discount4Order_Model_Source_Discount_Event extends Mage_Core_Model_Abstract {
    /**
     * Events name to catch to Option Array
     * @return array
     */
    public function toOptionArray() {
        $vals = array(
                'sales_order_place_after' => Mage::helper('discount4order')->__('On place order'),
                'sales_order_shipment_save_after' => Mage::helper('discount4order')->__('On ship order'),
				'sales_order_invoice_pay' => Mage::helper('discount4order')->__('On pay order'),
        );

        $options = array();
        foreach ($vals as $k => $v)
            $options[] = array(
                    'value' => $k,
                    'label' => $v
            );

        return $options;
    }

}