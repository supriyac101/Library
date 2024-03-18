<?php

class Potato_PrintAnyOrder_Helper_Data extends Mage_Catalog_Helper_Data
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        $isModuleEnabled = $this->isModuleEnabled();
        $isModuleOutputEnabled = $this->isModuleOutputEnabled();
        return $isModuleEnabled && $isModuleOutputEnabled;
    }

    public function isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/po_pao');
    }
}