<?php
class Anowave_Price_Block_Renderer_Discount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{	
		return $row->getData
		(
			$this->getColumn()->getIndex()
		) . '%';
	}
}