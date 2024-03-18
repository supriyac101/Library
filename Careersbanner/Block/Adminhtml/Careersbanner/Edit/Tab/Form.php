<?php

class Custom_Careersbanner_Block_Adminhtml_Careersbanner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('careersbanner_form', array('legend'=>Mage::helper('careersbanner')->__('Item information')));
       
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('careersbanner')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
  
        $fieldset->addField('filename', 'image', array(
            'label'     => Mage::helper('careersbanner')->__('File'),
            'required'  => false,
            'name'      => 'filename',
        ));
          
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('careersbanner')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('careersbanner')->__('Enabled'),
                ),
  
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('careersbanner')->__('Disabled'),
                ),
            ),
        ));
       
        /*$fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('careersbanner')->__('Content'),
            'title'     => Mage::helper('careersbanner')->__('Content'),
            'style'     => 'width:700px; height:500px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));*/
       
        if ( Mage::getSingleton('adminhtml/session')->getCareersbannerData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCareersbannerData());
            Mage::getSingleton('adminhtml/session')->setCareersbannerData(null);
        } elseif ( Mage::registry('careersbanner_data') ) {
            $form->setValues(Mage::registry('careersbanner_data')->getData());
        }
        return parent::_prepareForm();
    }
}