<?php

class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tabs extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Product Ribbon'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('general', array(
			'label' => $this->__('General'),
			'content' => $this->getLayout()->createBlock('ewpribbon/adminhtml_ribbon_edit_tab_general')->toHtml(),
		
		));
		
		$this->addTab('category_ribbon', array(
			'label' => $this->__('Category Page Ribbon'),
			'content' => $this->getLayout()->createBlock('ewpribbon/adminhtml_ribbon_edit_tab_category_ribbon')->toHtml(),
		
		));
		
		$this->addTab('product_ribbon', array(
			'label' => $this->__('Product Page Ribbon'),
			'content' => $this->getLayout()->createBlock('ewpribbon/adminhtml_ribbon_edit_tab_product_ribbon')->toHtml(),
		
		));
		
		$this->addTab('condition', array(
			'label' => $this->__('Conditions'),
			'content' => $this->getLayout()->createBlock('ewpribbon/adminhtml_ribbon_edit_tab_condition')->toHtml(),
		
		));
		
		$this->addTab('products', array(
			'label' => $this->__('Product Overrides'),
			'url'       => $this->getUrl('*/*/productGrid', array('_current'=>true)),
            'class'     => 'ajax',
		));
		
		return parent::_beforeToHtml();
	}
}
