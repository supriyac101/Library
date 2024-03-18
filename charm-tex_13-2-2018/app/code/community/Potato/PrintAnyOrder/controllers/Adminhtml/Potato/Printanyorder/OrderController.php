<?php

class Potato_PrintAnyOrder_Adminhtml_Potato_Printanyorder_OrderController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     * @throws Exception
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            throw new Exception($this->__('This order no longer exists'));
        }
        Mage::register('current_order', $order);
        return $order;
    }

    public function printAction()
    {
        try {
            $order = $this->_initOrder();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect('adminhtml/sales_order/index');
        }
        if ($order->getStoreId()) {
            Mage::app()->setCurrentStore($order->getStoreId());
        }
        $pdf = Mage::getModel('po_pao/sales_pdf_order')->getPdf(array($order));
        $printedAt = Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
        $fileName = 'order' . $printedAt . '.pdf';
        $this->_prepareDownloadResponse(
            $fileName, $pdf->render(), 'application/pdf'
        );
    }

    public function massPrintAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (empty($orderIds)) {
            return $this->_redirect('*/*/');
        }
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                continue;
            }

            $flag = true;
            if (!isset($pdf)) {
                $pdf = Mage::getModel('po_pao/sales_pdf_order')->getPdf(
                    array($order)
                );
            } else {
                $pages = Mage::getModel('po_pao/sales_pdf_order')->getPdf(
                    array($order)
                );
                $pdf->pages = array_merge($pdf->pages, $pages->pages);
            }
        }
        if ($flag) {
            $printedAt = Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
            $fileName = 'order' . $printedAt . '.pdf';
            return $this->_prepareDownloadResponse(
                $fileName, $pdf->render(), 'application/pdf'
            );
        } else {
            $this->_getSession()->addError(
                $this->__(
                    'There are no printable documents related to selected orders'
                )
            );
        }

        return $this->_redirect('*/*/');
    }

    public function printAllAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (empty($orderIds)) {
            return $this->_redirect('*/*/');
        }
        foreach ($orderIds as $orderId) {
            $orderModel = Mage::getModel('sales/order')->load($orderId);
            if ($orderModel->getId()) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('po_pao/sales_pdf_order')->getPdf(
                        array($orderModel)
                    );
                } else {
                    $pages = Mage::getModel('po_pao/sales_pdf_order')->getPdf(
                        array($orderModel)
                    );
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }

            $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->setOrderFilter($orderId)
                ->load()
            ;
            if ($invoices->getSize()) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(
                        $invoices
                    );
                } else {
                    $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf(
                        $invoices
                    );
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }

            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->setOrderFilter($orderId)
                ->load()
            ;
            if ($shipments->getSize()) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(
                        $shipments
                    );
                } else {
                    $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf(
                        $shipments
                    );
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }

            $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                ->setOrderFilter($orderId)
                ->load()
            ;
            if ($creditmemos->getSize()) {
                $flag = true;
                if (!isset($pdf)) {
                    $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf(
                        $creditmemos
                    );
                } else {
                    $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf(
                        $creditmemos
                    );
                    $pdf->pages = array_merge($pdf->pages, $pages->pages);
                }
            }
        }
        if ($flag) {
            $printedAt = Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
            $fileName = 'docs' . $printedAt . '.pdf';
            return $this->_prepareDownloadResponse(
                $fileName, $pdf->render(), 'application/pdf'
            );
        } else {
            $this->_getSession()->addError(
                $this->__(
                    'There are no printable documents related to selected orders'
                )
            );
        }

        return $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::helper('po_pao')->isAllowed();
    }
}