<?php
class Webskitters_Mobilebanner_Block_Adminhtml_Mobilebanner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mobilebanner';
    $this->_blockGroup = 'mobilebanner';
    $this->_headerText = Mage::helper('mobilebanner')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('mobilebanner')->__('Add Item');
    parent::__construct();
  }
}