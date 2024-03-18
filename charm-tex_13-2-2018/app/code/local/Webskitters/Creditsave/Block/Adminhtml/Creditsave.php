<?php
class Webskitters_Creditsave_Block_Adminhtml_Creditsave extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_creditsave';
    $this->_blockGroup = 'creditsave';
    $this->_headerText = Mage::helper('creditsave')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('creditsave')->__('Add Item');
    parent::__construct();
  }
}