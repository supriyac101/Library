<?php
class Custom_Careers_Block_Adminhtml_Careers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_careers';
    $this->_blockGroup = 'careers';
    $this->_headerText = Mage::helper('careers')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('careers')->__('Add Item');
    parent::__construct();
  }
}