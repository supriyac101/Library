<?php
class Bcs_Dailyfeature_Block_Adminhtml_Dailyfeature extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_dailyfeature';
    $this->_blockGroup = 'dailyfeature';
    $this->_headerText = Mage::helper('dailyfeature')->__('Weekly Deal Items');
    $this->_addButtonLabel = Mage::helper('dailyfeature')->__('Add Item');
    parent::__construct();
  }
}