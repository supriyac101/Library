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
                    $item->getName(), 24, true, true
                ),
                'feed' => 150,
            )
        );

        // draw SKU
        $subsku = strlen(preg_replace('/-.*$/', '', $item->getSku()).'-');
        if(substr($this->getSku($item), $subsku)){
            $lines[0][] = array(
                'text' => Mage::helper('core/string')->str_split(
                    substr($this->getSku($item), $subsku), 25
                ),
                'feed' => 35,
            );
        }else{
            $lines[0][] = array(
                'text' => Mage::helper('core/string')->str_split(
                    $this->getSku($item), 25
                ),
                'feed' => 35,
            );
        }

        // draw QTY
        $currentQtyLine = 0;
        if ((int)$item->getQtyOrdered()) {
            $status = Mage::helper('po_pao')->__('ORDERED');
            $lines[$currentQtyLine][] = array(
                //'text' => "{$status}: " . $item->getQtyOrdered() * 1,
                'text' => $item->getQtyOrdered() * 1,
                'feed' => 435,
                'align' => 'right',
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        /*if ((int)$item->getQtyInvoiced()) {
            $status = Mage::helper('po_pao')->__('Invoiced');
            $lines[$currentQtyLine][] = array(
                //'text' => "{$status}: " . $item->getQtyInvoiced() * 1,
                'text' => $item->getQtyInvoiced() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }*/
        if ((int)$item->getQtyShipped()) {
            $status = Mage::helper('po_pao')->__('SHIPPED');
            $lines[$currentQtyLine][] = array(
                //'text' => "{$status}: " . $item->getQtyShipped() * 1,
                'text' => $item->getQtyShipped() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        if ((int)$item->getQtyRefunded()) {
            $status = Mage::helper('po_pao')->__('REFUNDED');
            $lines[$currentQtyLine][] = array(
                //'text' => "{$status}: " . $item->getQtyRefunded() * 1,
                'text' => $item->getQtyRefunded() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
            $currentQtyLine++;
        }
        if ((int)$item->getQtyCanceled()) {
            $status = Mage::helper('po_pao')->__('CANCELED');
            $lines[$currentQtyLine][] = array(
                //'text' => "{$status}: " . $item->getQtyCanceled() * 1,
                'text' => $item->getQtyCanceled() * 1,
                'feed' => 465,
                'font' => 'bold',
            );
        }

        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 495,
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
            //echo "<pre>";
            //print_r ($options); exit;
            $c = 0;
            foreach ($options as $option) {
                // draw options label
                if($option['label'] == "Screen Print"){
                    $lines[0][] = array(
                        'text' => null,
                        'feed' => 305,
                        'align'=>'left',
                    );
                } else { 
                    $lines[$c][] = array(
                        'text' => Mage::helper('core/string')->str_split(strtoupper(htmlspecialchars_decode($option['value'])), 15, true, true),
                        'feed' => 305,
                        'align'=>'left',
                    );
                }
            $c++;
                /* if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    //echo count($values); exit;
                    $c = 0;
                    foreach ($values as $value) {
                        $lines[$c][] = array(
                            'text'  => Mage::helper('core/string')->str_split(htmlspecialchars_decode(strtoupper($option['label']).": ".strtoupper($value)), 15, true, true),
                            'feed'  => 310,
                            'align' => 'left',
                        );
                        $c++;
                    }
                    
                }*/
                
            }
        }
        
        if ($options) {
            $lineBlock = array(
                'lines'  => $lines,
                'height' => 15,
            );
        }else{
            $lineBlock = array(
                'lines'  => $lines,
                'height' => 15,
            );
        }

        $page = $pdf->drawLineBlocks(
            $page, array($lineBlock), array('table_header' => true)
        );

        $this->setPage($page);
    }
}