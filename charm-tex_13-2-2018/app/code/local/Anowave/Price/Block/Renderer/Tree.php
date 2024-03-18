<?php
class Anowave_Price_Block_Renderer_Tree extends Varien_Data_Form_Element_Abstract 
{
	public function getElementHtml()
	{	
		return Mage::app()->getLayout()->createBlock('price/categories')->toHtml();
	}
}