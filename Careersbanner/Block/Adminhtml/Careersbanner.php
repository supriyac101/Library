<?php
class Custom_Careersbanner_Block_Adminhtml_Careersbanner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_careersbanner';
    $this->_blockGroup = 'careersbanner';
    $this->_headerText = Mage::helper('careersbanner')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('careersbanner')->__('Add Item');
    parent::__construct();
  }
}