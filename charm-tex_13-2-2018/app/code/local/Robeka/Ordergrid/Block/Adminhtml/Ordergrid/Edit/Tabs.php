<?php

class Robeka_Ordergrid_Block_Adminhtml_Ordergrid_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ordergrid_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ordergrid')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ordergrid')->__('Item Information'),
          'title'     => Mage::helper('ordergrid')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('ordergrid/adminhtml_ordergrid_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}