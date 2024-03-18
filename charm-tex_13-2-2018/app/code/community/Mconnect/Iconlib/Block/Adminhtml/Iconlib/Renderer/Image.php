<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php

class Mconnect_Iconlib_Block_Adminhtml_Iconlib_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
 
    public function render(Varien_Object $row)
    {        
        $html = '<img ';
        $html .= 'id="' . $this->getColumn()->getId() . '" ';
        if(file_exists(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$row->getIconlibId().'/resized/'.$row->getIconfilename())){
	 $html .= 'src="' .Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/admin_mconnect_iconlib_uploads/'.$row->getIconlibId().'/resized/'.$row->getIconfilename(). '"';
	 }
	 else if(file_exists(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$row->getIconlibId().'/'.$row->getIconfilename()))
	 {
        $html .= 'src="' . $row->getData($this->getColumn()->getIndex()) . '"';
        } else {
        $html .= 'src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'admin_mconnect_iconlib_uploads/PlaceHolder/ImageNotAvailable.jpg"';    
        }
        $html .= 'class="grid-image" '.$this->getColumn()->getInlinecss().'/>';
        //$html .= '<br/><p>'.$row->getData($this->getColumn()->getIndex()).'</p>';
        return $html;
    }
}
?>
