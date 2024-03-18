<?php

class Custom_Careers_Block_Adminhtml_Careers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('careers_form', array('legend'=>Mage::helper('careers')->__('Item information')));
       
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('careers')->__('Job Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
        
        $fieldset->addField('location', 'text', array(
            'label'     => Mage::helper('careers')->__('Job Location'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'location',
        ));
  
        /*$fieldset->addField('filename', 'file', array(
            'label'     => Mage::helper('careers')->__('File'),
            'required'  => false,
            'name'      => 'filename',
        ));*/
          
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('careers')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('careers')->__('Enabled'),
                ),
  
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('careers')->__('Disabled'),
                ),
            ),
        ));
       
        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('careers')->__('Job Description'),
            'title'     => Mage::helper('careers')->__('Job Description'),
            'style'     => 'width:700px; height:500px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));
       
        if ( Mage::getSingleton('adminhtml/session')->getCareersData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCareersData());
            Mage::getSingleton('adminhtml/session')->setCareersData(null);
        } elseif ( Mage::registry('careers_data') ) {
            $form->setValues(Mage::registry('careers_data')->getData());
        }
        return parent::_prepareForm();
    }
}