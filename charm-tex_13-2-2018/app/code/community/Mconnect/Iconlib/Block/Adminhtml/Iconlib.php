<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php
class Mconnect_Iconlib_Block_Adminhtml_Iconlib extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_iconlib';
    $this->_blockGroup = 'iconlib';
    $this->_headerText = Mage::helper('iconlib')->__('Icon Library Manager');
    $this->_addButtonLabel = Mage::helper('iconlib')->__('Add Icon');
    parent::__construct();
  }
}