<?php
    class Custom_Careersbanner_Block_Adminhtml_Careersbanner_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    {
        public function render(Varien_Object $row){
            if($row->getFilename()!='')
                return "<img src=". Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA). 
                $row->getFilename() . " width=75 height = 75 />";
            else 
                return '';
        }

}