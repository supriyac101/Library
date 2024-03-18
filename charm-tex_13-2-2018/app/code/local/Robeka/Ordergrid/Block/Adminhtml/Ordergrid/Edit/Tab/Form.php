<?php

class Robeka_Ordergrid_Block_Adminhtml_Ordergrid_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ordergrid_form', array('legend'=>Mage::helper('ordergrid')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('ordergrid')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('ordergrid')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('ordergrid')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('ordergrid')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('ordergrid')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('ordergrid')->__('Content'),
          'title'     => Mage::helper('ordergrid')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getOrdergridData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getOrdergridData());
          Mage::getSingleton('adminhtml/session')->setOrdergridData(null);
      } elseif ( Mage::registry('ordergrid_data') ) {
          $form->setValues(Mage::registry('ordergrid_data')->getData());
      }
      return parent::_prepareForm();
  }
}