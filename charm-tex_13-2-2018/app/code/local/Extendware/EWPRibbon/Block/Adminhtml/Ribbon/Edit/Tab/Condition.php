<?php

class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Condition extends Extendware_EWCore_Block_Mage_Adminhtml_Template {
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('extendware/ewpribbon/ribbon/edit/tab/condition.phtml');
	}
	
	public function getRibbon() {
		return Mage::registry('ew:current_ribbon');
	}
}