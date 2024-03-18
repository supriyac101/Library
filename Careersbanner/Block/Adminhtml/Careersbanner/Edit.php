<?php

class Custom_Careersbanner_Block_Adminhtml_Careersbanner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'careersbanner';
        $this->_controller = 'adminhtml_careersbanner';
        
        $this->_updateButton('save', 'label', Mage::helper('careersbanner')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('careersbanner')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('careersbanner_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'careersbanner_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'careersbanner_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('careersbanner_data') && Mage::registry('careersbanner_data')->getId() ) {
            return Mage::helper('careersbanner')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('careersbanner_data')->getTitle()));
        } else {
            return Mage::helper('careersbanner')->__('Add Item');
        }
    }
}