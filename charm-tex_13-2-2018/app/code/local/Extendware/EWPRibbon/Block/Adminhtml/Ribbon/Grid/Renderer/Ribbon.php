<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Grid_Renderer_Ribbon extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $ribbon) {
	    $imagePath = null;
		$imageId = $ribbon->getData($this->getColumn()->getIndex());
		if ($imageId > 0) {
			$image = Mage::getModel('ewpribbon/image')->load($imageId);
			if ($image->getId() > 0) {
				$imagePath = $image->getPath();
			}
		}
		
		$width = max(0, $this->getColumn()->getWidth());
		if ($width) $width .= 'px';
		else $width = '100%';
		
		$html = '';
		$html .= '<div style="overflow:auto; max-height: 150px; text-align: center; width: ' . $width . '">';
		if ($imagePath) $html .= '<img src="' . Mage::helper('ewpribbon/internal_api')->getMediaUrl() . '/' . $imagePath . '">';
		else $html .= '[' . Mage::helper('ewpribbon')->__('No Image') . ']';
		$html .= '</div>';
		
		return $html;
	}
}