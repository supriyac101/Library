<?php
class Extendware_EWPRibbon_Block_Adminhtml_Image_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $image) {
		$width = max(0, $this->getColumn()->getWidth());
		if ($width) $width .= 'px';
		else $width = '100%';

		return '<div style="overflow:auto; max-height: 150px; text-align: center; width: ' . $width . '"><img src="' . $image->getUrl() . '"></div>';
	}
}