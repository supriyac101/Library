<?php

class Custom_Careers_Block_Adminhtml_Careers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('careers_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('careers')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('careers')->__('Item Information'),
          'title'     => Mage::helper('careers')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('careers/adminhtml_careers_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}