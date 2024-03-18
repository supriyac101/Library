<?php

class Webskitters_Creditsave_Block_Adminhtml_Creditsave_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('creditsave_form', array('legend'=>Mage::helper('creditsave')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('creditsave')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('creditsave')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('creditsave')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('creditsave')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('creditsave')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('creditsave')->__('Content'),
          'title'     => Mage::helper('creditsave')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCreditsaveData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCreditsaveData());
          Mage::getSingleton('adminhtml/session')->setCreditsaveData(null);
      } elseif ( Mage::registry('creditsave_data') ) {
          $form->setValues(Mage::registry('creditsave_data')->getData());
      }
      return parent::_prepareForm();
  }
}