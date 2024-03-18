<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/19/12
 * Time: 11:05 AM
 * To change this template use File | Settings | File Templates.
 */
 
class QS_Discount4Order_Model_Source_Discount_Amount extends Mage_Core_Model_Abstract {
    /**
     * Type of purchases to next discount  to Option Array
     * @return array
     */
    public function toOptionArray() {
        $vals = array(
                'by_payments' => Mage::helper('salesrule')->__('Sum of purchase'),
                'by_orders' => Mage::helper('salesrule')->__('Amount of orders'),
                'by_quantity' => Mage::helper('salesrule')->__('Quantity of products'),
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