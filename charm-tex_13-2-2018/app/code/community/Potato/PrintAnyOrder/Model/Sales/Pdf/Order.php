<?php

class Potato_PrintAnyOrder_Model_Sales_Pdf_Order
    extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Insert totals to pdf page
     *
     * @param  Potato_PrintAnyOrder_Model_Sales_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     *
     * @return Potato_PrintAnyOrder_Model_Sales_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $source->setOrder($source);
        return parent::insertTotals($page, $source);
    }

    /**
     * Draw header for item table
     *
     * @param Potato_PrintAnyOrder_Model_Sales_Pdf_Page $page
     *
     * @return void
     */
    protected function _drawHeader(Potato_PrintAnyOrder_Model_Sales_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('po_pao')->__('SKU'),
            'feed' => 35,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('po_pao')->__('PRODUCTS'),
            'feed'  => 200,
            'align' => 'right'
        );
        
        $lines[0][] = array(
            'text'  => Mage::helper('po_pao')->__('OPTIONS'),
            'feed'  => 350,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('po_pao')->__('QTY'),
            'feed'  => 435,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('po_pao')->__('PRICE'),
            'feed'  => 495,
            'align' => 'right'
        );

        /*$lines[0][] = array(
            'text'  => Mage::helper('po_pao')->__('Tax'),
            'feed'  => 470,
        );*/

        $lines[0][] = array(
            'text'  => Mage::helper('po_pao')->__('SUBTOTAL'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5,
        );

        $this->drawLineBlocks(
            $page, array($lineBlock), array('table_header' => true)
        );
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
    
    public function insertFacilityName(Potato_PrintAnyOrder_Model_Sales_Pdf_Page $page, $orderModel){
        /* Custom code */
        //echo "----------".$orderModel->getId();
        $fnorder = Mage::getModel('sales/order')->load($orderModel->getId());
        /*echo "<pre>";
        print_r($fnorder->getData()); exit;*/
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
    
    public function insertOrderComments(&$page, $orderModel){
        /*************************** This Is The Invoice Comments ***********************************/
        $this->_setFontRegular($page, 10);
        $_tempY = $this->y;
        
        $fnorder = Mage::getModel('sales/order')->load($orderModel->getId());
        
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
    
    public function insertCustomerComments(&$page, $orderModel){
        /*************************** This Is The Invoice Comments ***********************************/
        $this->_setFontRegular($page, 10);
        $_tempY = $this->y;
        
        $fnorder = Mage::getModel('sales/order')->load($orderModel->getId());
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
    public function insertCouponcode(&$page, $orderModel){
    }
    /**
     * Return PDF document
     *
     * @param  array $orderList
     *
     * @return Zend_Pdf
     */
    public function getPdf($orderList = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Potato_PrintAnyOrder_Model_Sales_Pdf();

        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($orderList as $orderModel) {
            if ($orderModel->getStoreId()) {
                Mage::app()->getLocale()->emulate($orderModel->getStoreId());
                Mage::app()->setCurrentStore($orderModel->getStoreId());
            }
            $page = $this->newPage();
            /* Add image */
            //$this->insertLogo($page, $orderModel->getStore());
            /* Add address */
            $this->insertAddress($page, $orderModel->getStore());
            /* Add head */
            $this->insertOrder($page, $orderModel, true);
            
            
            
            /* Add facility name */
            //$this->insertFacilityName($page, $orderModel);
            
            /* Add order comment */
            //$this->insertOrderComments($page, $orderModel);
            
            /* Add customer comment */
            $this->insertCustomerComments($page, $orderModel);
            
            
            
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($orderModel->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $startTable=$this->y + 35;
                $item->setOrderItem($item);
                $page = $this->_drawItem($item, $page, $orderModel);
                
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawLine(25, $this->y + 12.5, 570, $this->y + 12.5);
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                
                $page->drawLine(25, $startTable,25,$this->y + 12.5);
                $page->drawLine(140, $startTable,140, $this->y+12.5);
                $page->drawLine(300, $startTable,300, $this->y+12.5);
                $page->drawLine(400, $startTable,400, $this->y+12.5);
                $page->drawLine(437, $startTable,437,$this->y+12.5);
                $page->drawLine(497, $startTable,497,$this->y+12.5);
                $page->drawLine(570, $startTable,570,$this->y + 12.5);
                
                if ($this->y < 180) {
                    $page = $this->newPage(array('table_header' => true));
                }
                
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $orderModel);
            if ($orderModel->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
            if ($this->_getConfig()->getTextAlign() == Potato_PrintAnyOrder_Model_Source_TextAlign::RIGHT_VALUE) {
                //flush cache
                $page->flushDrawText();
            }
            /* Add couponcode */
            $this->insertCouponcode($page, $orderModel);
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     *
     * @return Potato_PrintAnyOrder_Model_Sales_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Potato_PrintAnyOrder_Model_Sales_Pdf_Page::SIZE_A4);

        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }

    /**
     * Set font as regular
     *
     * @param  Potato_PrintAnyOrder_Model_Sales_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $regularFont = $this->_getConfig()->getRegularFont();
        if ($regularFont === null) {
            return parent::_setFontRegular($object, $size);
        }
        $font = Zend_Pdf_Font::fontWithPath($regularFont, Zend_Pdf_Font::EMBED_SUPPRESS_EMBED_EXCEPTION);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Potato_PrintAnyOrder_Model_Sales_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $boldFont = $this->_getConfig()->getBoldFont();
        if ($boldFont === null) {
            return parent::_setFontBold($object, $size);
        }
        $font = Zend_Pdf_Font::fontWithPath($boldFont, Zend_Pdf_Font::EMBED_SUPPRESS_EMBED_EXCEPTION);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Potato_PrintAnyOrder_Model_Sales_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $italicFont = $this->_getConfig()->getItalicFont();
        if ($italicFont === null) {
            return parent::_setFontItalic($object, $size);
        }
        $font = Zend_Pdf_Font::fontWithPath($italicFont, Zend_Pdf_Font::EMBED_SUPPRESS_EMBED_EXCEPTION);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * @return Potato_PrintAnyOrder_Helper_Config
     */
    protected function _getConfig()
    {
        return Mage::helper('po_pao/config');
    }
}