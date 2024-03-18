<?php

class Potato_PrintAnyOrder_Model_Sales_Pdf extends Zend_Pdf
{
    /**
     * Create page object, attached to the PDF document.
     * Method signatures:
     *
     * 1. Create new page with a specified pagesize.
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new Zend_Pdf_Page(string $pagesize);
     * ---------------------------------------------------------
     *
     * 2. Create new page with a specified pagesize (in default user space units).
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new Zend_Pdf_Page(numeric $width, numeric $height);
     * ---------------------------------------------------------
     *
     * @param mixed $param1
     * @param mixed $param2
     * @return Zend_Pdf_Page
     */
    public function newPage($param1, $param2 = null)
    {
        if ($param2 === null) {
            return new Potato_PrintAnyOrder_Model_Sales_Pdf_Page($param1, $this->_objFactory);
        } else {
            return new Potato_PrintAnyOrder_Model_Sales_Pdf_Page($param1, $param2, $this->_objFactory);
        }
    }
}