<?php

class Webskitters_Mobilebanner_Block_Adminhtml_Mobilebanner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mobilebanner_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mobilebanner')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('mobilebanner')->__('Item Information'),
          'title'     => Mage::helper('mobilebanner')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('mobilebanner/adminhtml_mobilebanner_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}