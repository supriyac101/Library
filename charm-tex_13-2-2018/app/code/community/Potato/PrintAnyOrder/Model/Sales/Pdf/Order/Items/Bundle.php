<?php

class Potato_PrintAnyOrder_Model_Sales_Pdf_Order_Items_Bundle
    extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();

        $this->_setFontRegular();
        $items = $this->getChildren($item);

        $_prevOptionId = '';
        $drawItems = array();

        foreach ($items as $_item) {
            $line = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => 10,
                );
            }

            if ($_item->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = array(
                        'font' => 'italic',
                        'text' => Mage::helper('core/string')->str_split(
                            $attributes['option_label'], 70, true, true
                        ),
                        'feed' => 35,
                    );
                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => 10,
                    );
                    $line = array();
                    $_prevOptionId = $attributes['option_id'];
                }
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getParentItem()) {
                $feed = 40;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = 35;
                $name = $_item->getName();
            }
            $line[] = array(
                'text' => Mage::helper('core/string')->str_split(
                    $name, 55, true, true
                ),
                'feed' => $feed,
            );

            // draw SKUs
            if (!$_item->getParentItem()) {
                $text = array();
                $sku = $item->getSku();
                $skuList = Mage::helper('core/string')->str_split($sku, 30);
                foreach ($skuList as $part) {
                    $text[] = $part;
                }
                $line[] = array(
                    'text' => $text,
                    'feed' => 255,
                );
            }

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                $price = $order->formatPriceTxt($_item->getPrice());
                $line[] = array(
                    'text'  => $price,
                    'feed'  => 365,
                    'font'  => 'bold',
                    'align' => 'right',
                );

                $tax = $order->formatPriceTxt($_item->getTaxAmount());
                $line[] = array(
                    'text'  => $tax,
                    'feed'  => 495,
                    'font'  => 'bold',
                    'align' => 'right',
                );

                $row_total = $order->formatPriceTxt($_item->getRowTotal());
                $line[] = array(
                    'text'  => $row_total,
                    'feed'  => 565,
                    'font'  => 'bold',
                    'align' => 'right',
                );
            }

            if (!$_item->getParentItem()) {
                $drawItems[$optionId]['lines'][] = $line;
            } else {
                // Draw QTY
                $currentQtyLine = 0;
                if ((int)$_item->getQtyOrdered()) {
                    $status = Mage::helper('po_pao')->__('Ordered');
                    $qtyElement = array(
                        'text' => "{$status}: " . $_item->getQtyOrdered() * 1,
                        'feed' => 390,
                        'font' => 'bold',
                    );
                    if ($currentQtyLine > 0) {
                        $line = array();
                    }
                    $line[] = $qtyElement;
                    $drawItems[$optionId]['lines'][] = $line;
                    $currentQtyLine++;
                }
                if ((int)$_item->getQtyInvoiced()) {
                    $status = Mage::helper('po_pao')->__('Invoiced');
                    $qtyElement = array(
                        'text' => "{$status}: " . $_item->getQtyInvoiced() * 1,
                        'feed' => 390,
                        'font' => 'bold',
                    );
                    if ($currentQtyLine > 0) {
                        $line = array();
                    }
                    $line[] = $qtyElement;
                    $drawItems[$optionId]['lines'][] = $line;
                    $currentQtyLine++;
                }
                if ((int)$_item->getQtyShipped()) {
                    $status = Mage::helper('po_pao')->__('Shipped');
                    $qtyElement = array(
                        'text' => "{$status}: " . $_item->getQtyShipped() * 1,
                        'feed' => 390,
                        'font' => 'bold',
                    );
                    if ($currentQtyLine > 0) {
                        $line = array();
                    }
                    $line[] = $qtyElement;
                    $drawItems[$optionId]['lines'][] = $line;
                    $currentQtyLine++;
                }
                if ((int)$_item->getQtyRefunded()) {
                    $status = Mage::helper('po_pao')->__('Refunded');
                    $qtyElement = array(
                        'text' => "{$status}: " . $_item->getQtyRefunded() * 1,
                        'feed' => 390,
                        'font' => 'bold',
                    );
                    if ($currentQtyLine > 0) {
                        $line = array();
                    }
                    $line[] = $qtyElement;
                    $drawItems[$optionId]['lines'][] = $line;
                    $currentQtyLine++;
                }
                if ((int)$_item->getQtyCanceled()) {
                    $status = Mage::helper('po_pao')->__('Canceled');
                    $qtyElement = array(
                        'text' => "{$status}: " . $_item->getQtyCanceled() * 1,
                        'feed' => 390,
                        'font' => 'bold',
                    );
                    if ($currentQtyLine > 0) {
                        $line = array();
                    }
                    $line[] = $qtyElement;
                    $drawItems[$optionId]['lines'][] = $line;
                }
            }
        }

        // custom options
        $options = $item->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = array();
                    $lines[][] = array(
                        'text' => Mage::helper('core/string')->str_split(
                            strip_tags($option['label']), 70, true, true
                        ),
                        'font' => 'italic',
                        'feed' => 35,
                    );

                    if ($option['value']) {
                        $text = array();
                        if (isset($option['print_value'])) {
                            $_printValue = $option['print_value'];
                        } else {
                            $_printValue = strip_tags($option['value']);
                        }
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            $valueList = Mage::helper('core/string')->str_split(
                                $value, 50, true, true
                            );
                            foreach ($valueList as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = array(
                            'text' => $text,
                            'feed' => 40,
                        );
                    }

                    $drawItems[] = array(
                        'lines'  => $lines,
                        'height' => 10,
                    );
                }
            }
        }

        $page = $pdf->drawLineBlocks(
            $page, $drawItems, array('table_header' => true)
        );
        $this->setPage($page);
    }

    /**
     * Getting all available children for Order item
     *
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return array
     */
    public function getChildren(Mage_Sales_Model_Order_Item $item)
    {
        $_itemsArray = array();
        $_items = $item->getOrder()->getAllItems();

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getParentItem();
                if ($parentItem) {
                    $_itemsArray[$parentItem->getId()][$_item->getId()] = $_item;
                } else {
                    $_itemsArray[$_item->getId()][$_item->getId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getId()])) {
            return $_itemsArray[$item->getId()];
        } else {
            return null;
        }
    }

    /**
     * Can show price info for item
     *
     * @param Mage_Sales_Order_Item $item
     *
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        if (
            ($item->getParentItem() && $this->isChildCalculated())
            || (!$item->getParentItem() && !$this->isChildCalculated())
        ) {
            return true;
        }
        return false;
    }
}