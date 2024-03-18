<?php

class Custom_Careersbanner_Block_Adminhtml_Careersbanner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('careersbanner_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('careersbanner')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('careersbanner')->__('Item Information'),
          'title'     => Mage::helper('careersbanner')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('careersbanner/adminhtml_careersbanner_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}