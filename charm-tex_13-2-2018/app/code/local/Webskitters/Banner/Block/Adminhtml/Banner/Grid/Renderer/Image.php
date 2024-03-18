<?php
class Webskitters_Banner_Block_Adminhtml_Banner_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    } 
    protected function _getValue(Varien_Object $row)
    {       
        $val = $row->getData('product_image');
        if($val){
            $val = str_replace("no_selection", "", $val);
            $url = Mage::getBaseUrl('media').$val; 
            $out = "<img src=". $url ." width='60px'/>"; 
        } else{
            $out = "<img src='".Mage::getBaseUrl('media')."no-mage.gif' width='60px'/>"; 
        }
        return $out;
    }
}