<?php

class Webskitters_Creditsave_Block_Adminhtml_Creditsave_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('creditsave_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('creditsave')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('creditsave')->__('Item Information'),
          'title'     => Mage::helper('creditsave')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('creditsave/adminhtml_creditsave_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}