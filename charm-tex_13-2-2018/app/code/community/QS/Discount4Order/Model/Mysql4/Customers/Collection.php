<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com>
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */

class QS_Discount4Order_Model_Mysql4_Customers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /**
     * Initialize Model
     */
    public function _construct() {
        parent::_construct();
        $this->_init('discount4order/customers');
    }
}