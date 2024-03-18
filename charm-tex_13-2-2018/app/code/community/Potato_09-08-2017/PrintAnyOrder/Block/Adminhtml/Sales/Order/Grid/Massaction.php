<?php

class Potato_PrintAnyOrder_Block_Adminhtml_Sales_Order_Grid_Massaction
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @return bool
     */
    public function canShow()
    {
        return Mage::helper('po_pao')->isEnabled() && $this->getGridBlock();
    }

    public function getJsObjectName()
    {
        $orderGridBlock = $this->getGridBlock();
        return $orderGridBlock->getId() . '_massactionJsObject';
    }

    public function getGridBlock()
    {
        return $this->getLayout()->getBlock('sales_order.grid');
    }

    public function getPrintOrderOptionId()
    {
        return 'pdf_order';
    }

    public function getPrintAllOptionId()
    {
        return 'pdf_all';
    }
}