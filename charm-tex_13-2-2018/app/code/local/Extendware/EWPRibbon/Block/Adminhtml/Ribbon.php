<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_headerText = $this->__('Product Ribbons');

		$this->_updateButton('add', 'label', $this->__('Add Product Ribbon'));
	}
}
