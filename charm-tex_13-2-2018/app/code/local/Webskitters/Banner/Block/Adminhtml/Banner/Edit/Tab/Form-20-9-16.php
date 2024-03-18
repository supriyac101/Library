<?php

class Webskitters_Banner_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('banner_form', array('legend'=>Mage::helper('banner')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('banner')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('banner')->__('Background Image'),
          'required'  => false,
          'name'      => 'filename',
	  ));
      
      $fieldset->addField('product_image', 'image', array(
          'label'     => Mage::helper('banner')->__('Product Image'),
          'required'  => false,
          'name'      => 'product_image',
	  ));
      
      $fieldset->addField('product_sku', 'text', array(
          'label'     => Mage::helper('banner')->__('Product SKU'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'product_sku',
      ));
      
      /*$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'name');
         //here, "name" is the attribute_code
      $allOptions = $attribute->getSource()->getAllOptions(true, true);
      $fieldset->addField('sku', 'select', array(
          'label'     => Mage::helper('banner')->__('Test Sku'),
          'name'      => 'sku',
	  'required'  => false,
          'values'    => $allOptions,
      ));*/
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('banner')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('banner')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('banner')->__('Disabled'),
              ),
          ),
      ));
     $fieldset->addField('sortby', 'text', array(
          'label'     => Mage::helper('banner')->__('Sort order'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'sortby',
      ));
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('banner')->__('Content'),
          'title'     => Mage::helper('banner')->__('Content'),
          'style'     => 'width:500px; height:200px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
	  $fieldset->addField('url', 'text', array(
          'label'     => Mage::helper('banner')->__('Url'),
          'required'  => false,
          'name'      => 'url',
	  ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBannerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerData());
          Mage::getSingleton('adminhtml/session')->setBannerData(null);
      } elseif ( Mage::registry('banner_data') ) {
          $form->setValues(Mage::registry('banner_data')->getData());
      }
      return parent::_prepareForm();
  }
}