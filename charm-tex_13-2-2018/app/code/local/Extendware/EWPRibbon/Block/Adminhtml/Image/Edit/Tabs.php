<?php

class Extendware_EWPRibbon_Block_Adminhtml_Image_Edit_Tabs extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Ribbon Image'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('general', array(
			'label' => $this->__('General'),
			'content' => $this->getLayout()->createBlock('ewpribbon/adminhtml_image_edit_tab_general')->toHtml(),
		
		));
		
		return parent::_beforeToHtml();
	}
}
