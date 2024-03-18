<?php

class Webskitters_Mobilebanner_Block_Adminhtml_Mobilebanner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('mobilebanner_form', array('legend'=>Mage::helper('mobilebanner')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('mobilebanner')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('mobilebanner')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mobilebanner')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobilebanner')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobilebanner')->__('Disabled'),
              ),
          ),
      ));
	  if (!Mage::app()->isSingleStoreMode()) {
		  $fieldset->addField('store_id', 'multiselect', array(
			  'name' => 'stores[]',
			  'label' => Mage::helper('banner')->__('Store View'),
			  'title' => Mage::helper('banner')->__('Store View'),
			  'required' => true,
			  'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		  ));
	  } else {
		  $fieldset->addField('store_id', 'hidden', array(
			  'name' => 'stores[]',
			  'value' => Mage::app()->getStore(true)->getId(),
		  ));
	  }
      $fieldset->addField('sortby', 'text', array(
          'label'     => Mage::helper('mobilebanner')->__('Sort Order'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'sortby',
      ));
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('mobilebanner')->__('Content'),
          'title'     => Mage::helper('mobilebanner')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
	  $fieldset->addField('url', 'text', array(
          'label'     => Mage::helper('mobilebanner')->__('URL'),
          'required'  => false,
          'name'      => 'url',
	  ));
     
      if ( Mage::getSingleton('adminhtml/session')->getMobilebannerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMobilebannerData());
          Mage::getSingleton('adminhtml/session')->setMobilebannerData(null);
      } elseif ( Mage::registry('mobilebanner_data') ) {
          $form->setValues(Mage::registry('mobilebanner_data')->getData());
      }
      return parent::_prepareForm();
  }
}