<?php

class Potato_PrintAnyOrder_Model_Sales_Pdf_Order_Items_Default
    extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = array();

        // draw Product name
        $lines[0] = array(
            array(
                'text' => Mage::helper('core/string')->str_split(
                    $item->getName(), 35, true, true
                ),
                'feed' => 35,
            )
        );

        // draw SKU
        $lines[0][] = array(
            'text' => Mage::helper('core/string')->str_split(
                $this->getSku($item), 25
            ),
            'feed' => 385,
            'align' => 'right',
        );

        // draw QTY
        $currentQtyLine = 0;
        if ((int)$item->getQtyOrdered()) {
            $status = Mage::helper('po_pao')->__('Ordered');
            $lines[$currentQtyLine][] = array(
                'text' => "{$status}: " . $item->getQtyOrdered() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        if ((int)$item->getQtyInvoiced()) {
            $status = Mage::helper('po_pao')->__('Invoiced');
            $lines[$currentQtyLine][] = array(
                'text' => "{$status}: " . $item->getQtyInvoiced() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        if ((int)$item->getQtyShipped()) {
            $status = Mage::helper('po_pao')->__('Shipped');
            $lines[$currentQtyLine][] = array(
                'text' => "{$status}: " . $item->getQtyShipped() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        if ((int)$item->getQtyRefunded()) {
            $status = Mage::helper('po_pao')->__('Refunded');
            $lines[$currentQtyLine][] = array(
                'text' => "{$status}: " . $item->getQtyRefunded() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        if ((int)$item->getQtyCanceled()) {
            $status = Mage::helper('po_pao')->__('Canceled');
            $lines[$currentQtyLine][] = array(
                'text' => "{$status}: " . $item->getQtyCanceled() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
        }

        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 455,
            'font'  => 'bold',
            'align' => 'right',
        );

        // draw Tax
        /*$lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right',
        );*/

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 565,
            'font'  => 'bold',
            'align' => 'right',
        );

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                /*$lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(
                        strip_tags($option['label']), 70, true, true
                    ),
                    'font' => 'italic',
                    'feed' => 35,
                );*/

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split(
                                htmlspecialchars_decode($value), 50, true, true
                            ),
                            'feed' => 40,
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20,
        );

        $page = $pdf->drawLineBlocks(
            $page, array($lineBlock), array('table_header' => true)
        );

        $this->setPage($page);
    }
}