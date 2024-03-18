<?php
class Robeka_Ordergrid_Block_Adminhtml_Ordergrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ordergrid';
    $this->_blockGroup = 'ordergrid';
    $this->_headerText = Mage::helper('ordergrid')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('ordergrid')->__('Add Item');
    parent::__construct();
  }
}