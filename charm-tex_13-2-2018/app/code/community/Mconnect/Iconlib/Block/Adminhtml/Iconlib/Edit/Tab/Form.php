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

class Mconnect_Iconlib_Block_Adminhtml_Iconlib_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('iconlib_form', array('legend'=>Mage::helper('iconlib')->__('Icon information')));
     
      $fieldset->addField('iconlabel', 'text', array(
          'label'     => Mage::helper('iconlib')->__('Icon Label'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'iconlabel',
      ));

	  if($this->getRequest()->getParam('id') != '')
	  {
		  $fieldset->addField('icon', 'file', array(
			  'label'     => Mage::helper('iconlib')->__('Icon File Upload'),
			  'name'      => 'icon',
		  ));
	  }
	  else
	  {
		  $fieldset->addField('icon', 'file', array(
			  'label'     => Mage::helper('iconlib')->__('Icon File Upload'),
			  'class'     => 'required-entry',
			  'required'  => true,
			  'name'      => 'icon',
		  ));	  
	  }
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('iconlib')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('iconlib')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('iconlib')->__('Disabled'),
              ),
          ),
      ));
     
      /*$fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('iconlib')->__('Content'),
          'title'     => Mage::helper('iconlib')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));*/
     
      if ( Mage::getSingleton('adminhtml/session')->getIconlibData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getIconlibData());
          Mage::getSingleton('adminhtml/session')->setIconlibData(null);
      } elseif ( Mage::registry('iconlib_data') ) {
          $form->setValues(Mage::registry('iconlib_data')->getData());
      }
      return parent::_prepareForm();
  }
}