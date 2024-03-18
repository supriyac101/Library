<?php

class Potato_PrintAnyOrder_Model_Observer
{
    /**
     * @param Varien_Object $observer
     *
     * @return Potato_PrintAnyOrder_Model_Observer
     */
    public function addPrintButton(Varien_Object $observer)
    {
        if (!Mage::helper('po_pao')->isEnabled()) {
            return $this;
        }
        if (!Mage::helper('po_pao')->isAllowed()) {
            return $this;
        }
        $containerBlock = $observer->getBlock();
        if (!$containerBlock instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            return $this;
        }
        $containerBlock->addButton(
            'po_pao_print', array(
                'label'   => Mage::helper('po_pao')->__('Print'),
                'onclick' => "setLocation('{$this->_getPrintUrl()}')",
                'class'   => 'save',
            )
        );
        return $this;
    }

    public function addPrintMassAction($observer)
    {
        if (!Mage::helper('po_pao')->isEnabled()) {
            return $this;
        }
        if (!Mage::helper('po_pao')->isAllowed()) {
            return $this;
        }
        $massBlock = $observer->getEvent()->getBlock();
        if (
            !$massBlock instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
            && !$massBlock instanceof Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction
        ) {
            return $this;
        }
        if ($massBlock->getRequest()->getControllerName() != 'sales_order') {
            return $this;
        }
        $massBlock->addItem(
            'pdf_order',
            array(
                'label' => $massBlock->__('Print Orders'),
                'url'   => $this->_getMassPrintUrl(),
            )
        );
        $massBlock->addItem(
            'pdf_all',
            array(
                'label' => $massBlock->__('Print All incl. Order Prints'),
                'url'   => $this->_getPrintAllUrl(),
            )
        );
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @return string
     */
    protected function _getPrintUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl(
            'adminhtml/potato_printanyorder_order/print',
            array('order_id' => $this->getOrder()->getId())
        );
    }

    /**
     * @return string
     */
    protected function _getMassPrintUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl(
            'adminhtml/potato_printanyorder_order/massPrint'
        );
    }

    /**
     * @return string
     */
    protected function _getPrintAllUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl(
            'adminhtml/potato_printanyorder_order/printAll'
        );
    }
}