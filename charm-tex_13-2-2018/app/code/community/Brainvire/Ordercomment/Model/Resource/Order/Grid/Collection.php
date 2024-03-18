<?php
/** 
 * @category Brainvire 
 * @package Brainvire_Ordercomment 
 * @copyright Copyright (c) 2015 Brainvire Infotech Pvt Ltd
 */
class Brainvire_Ordercomment_Model_Resource_Order_Grid_Collection extends Mage_Sales_Model_Mysql4_Order_Grid_Collection
{
    /**
     * Initialize collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        // Join order comment
        $this->getSelect()->joinLeft(
            array('ordercomment_table' => $this->getTable('sales/order_status_history')),
            'main_table.entity_id = ordercomment_table.parent_id AND ordercomment_table.comment IS NOT NULL',
            array(
                'ordercomment' => 'ordercomment_table.comment',
            )
        )->group('main_table.entity_id');

        return $this;
    }

    /**
     * Initialize collection count select
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        return parent::getSelectCountSql()->reset(Zend_Db_Select::GROUP);
    }
}