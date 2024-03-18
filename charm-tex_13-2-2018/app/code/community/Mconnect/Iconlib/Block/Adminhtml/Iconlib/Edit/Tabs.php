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

class Mconnect_Iconlib_Block_Adminhtml_Iconlib_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('iconlib_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('iconlib')->__('Icon Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('iconlib')->__('Icon Information'),
          'title'     => Mage::helper('iconlib')->__('Icon Information'),
          'content'   => $this->getLayout()->createBlock('iconlib/adminhtml_iconlib_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}