<?php
class Extendware_EWPRibbon_Block_Adminhtml_Image extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_headerText = $this->__('Ribbon Image');

		$this->_updateButton('add', 'label', $this->__('Add Ribbon Image'));
	}
}
