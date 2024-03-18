<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('SKU'),
            'feed' => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Products'),
            'feed'  => 200,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 435,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Price'),
            'feed'  => 495,
            'align' => 'right'
        );

        /*$lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Tax'),
            'feed'  => 495,
            'align' => 'right'
        );*/

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Subtotal'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
    
    public function insertFacilityName(Zend_Pdf_Page $page, $order){
        /* Custom code */
        $fnorder = Mage::getModel('sales/order')->load($order->getId());
        //If they have no customer id, they're a guest.
        if($fnorder->getCustomerId() === NULL){
            $fncustomer = "Facility Name: Guest";
        } else {
            //else, they're a normal registered user.
            $customer = Mage::getModel('customer/customer')->load($fnorder->getCustomerId());
            $fncustomer = $customer->getTaxvat();
        }
        /* Custom code end */
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText("Facility name: ".$fncustomer, 375, $docHeader[1] - 15, 'UTF-8');
    }
    
    public function insertOrderComments(&$page, $order){
        /*************************** This Is The Invoice Comments ***********************************/
        $this->_setFontRegular($page, 10);
        $_tempY = $this->y;
        
        $fnorder = Mage::getModel('sales/order')->load($order->getId());
        
        if(!$fnorder->getStatusHistoryCollection(true)){return;}
        $commentsCollection = $fnorder->getStatusHistoryCollection(true);
        /*echo "<pre>";
        print_r($commentsCollection->getData());
        exit;*/
        $internalcomments = "Order Comments:";
        
        
        $this->y -= 15;
        
        $flag = false;
        foreach($commentsCollection as $comm)
        {
            $_comment = $comm->getData('comment');
            $textChunk = wordwrap($_comment, 120, "\n");
            foreach(explode("\n", $textChunk) as $textLine){
                if ($textLine!=='') {
                    $flag = true;
                }
            }
        }
        
        if($flag == true){
            // Begin table header
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y -15);
            $this->y -= 10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
            $page->drawText($internalcomments, 35, $this->y, 'UTF-8');
            // end table header
        }
        
        foreach($commentsCollection as $comm)
        {
            $_comment = $comm->getData('comment');
            $textChunk = wordwrap($_comment, 120, "\n");
            foreach(explode("\n", $textChunk) as $textLine){
                if ($textLine!=='') {
                    $page->drawText(strip_tags(ltrim($textLine)), 35, $this->y -15, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }
        $this->y -= 15;
        
        /*************************** End Invoice Comments ***********************************/
    }
    
    public function insertCustomerComments(&$page, $order){
        /*************************** This Is The Invoice Comments ***********************************/
        $this->_setFontRegular($page, 10);
        $_tempY = $this->y;
        
        $fnorder = Mage::getModel('sales/order')->load($order->getId());
        /*echo "<pre>";
        print_r($fnorder->getData());
        exit;*/
        $_customerComments = $fnorder->getData('onestepcheckout_customercomment');
                
        $_commentsHeading = "Customer Comments:";
        
        $this->y -= 15;
        
        
        
        if ($_customerComments !='') {
            // Begin table header
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y -15);
            $this->y -= 10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
            $page->drawText($_commentsHeading, 35, $this->y, 'UTF-8');
            // end table header
            $page->drawText($_customerComments, 35, $this->y -15, 'UTF-8');
            $this->y -= 15;
        }
            
        $this->y -= 15;
        
        /*************************** End Invoice Comments ***********************************/
    }
    
    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            //$this->insertLogo($page, $invoice->getStore());
            
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            );
            
            /* Add facility name */
            //$this->insertFacilityName($page, $order);
            
            /* Add order comment */
            $this->insertOrderComments($page, $order);
            
            /* Add customer comment */
            $this->insertCustomerComments($page, $order);
            
            /* Add table */
            
            $this->_drawHeader($page);
            
            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                
                /* Draw item */
                $startTable = $this->y + 35;
                $page = $this->_drawItem($item, $page, $order);
                
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawLine(25, $this->y + 12.5, 570, $this->y + 12.5);
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                
                
                $page->drawLine(25, $startTable,25,$this->y+12.5);
                $page->drawLine(140, $startTable,140, $this->y+12.5);
                $page->drawLine(380, $startTable,380, $this->y+12.5);
                $page->drawLine(437, $startTable,437,$this->y+12.5);
                $page->drawLine(497, $startTable,497,$this->y+12.5);
                $page->drawLine(570, $startTable,570,$this->y+12.5);
                
                if ($this->y < 55) {
                    $page = $this->newPage(array('table_header' => true));
                }
                
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            
            /* Add couponcode */
            $this->insertCouponcode($page, $invoice);
            
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
            
            
            /*$startTable = $this->y + 15;
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawLine(25, $this->y + 12.5, 570, $this->y + 12.5);
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            
            
            $page->drawLine(25, $startTable,25,$this->y+12.5);
            $page->drawLine(265, $startTable,265, $this->y+12.5);
            $page->drawLine(367, $startTable,367, $this->y+12.5);
            $page->drawLine(437, $startTable,437,$this->y+12.5);
            $page->drawLine(497, $startTable,497,$this->y+12.5);
            $page->drawLine(570, $startTable,570,$this->y+12.5);*/
            //$this->y -=20;
        }
        return $page;
    }
}
