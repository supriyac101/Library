<?php

class Bcs_Dailyfeature_Block_Adminhtml_Dailyfeature_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('dailyfeature_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('dailyfeature')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('dailyfeature')->__('Item Information'),
          'title'     => Mage::helper('dailyfeature')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('dailyfeature/adminhtml_dailyfeature_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}