<?php
class Anowave_Price_Block_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{	
		return $this->getLayout()->createBlock('price/title')->setData
		(
			array
			(
				'row' 		=> $row,
				'column' 	=> $this->getColumn()
			)	
		)->toHtml();
	}
}