<?php

class Custom_Careers_Block_Adminhtml_Careers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'careers';
        $this->_controller = 'adminhtml_careers';
        
        $this->_updateButton('save', 'label', Mage::helper('careers')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('careers')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('careers_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'careers_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'careers_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('careers_data') && Mage::registry('careers_data')->getId() ) {
            return Mage::helper('careers')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('careers_data')->getTitle()));
        } else {
            return Mage::helper('careers')->__('Add Item');
        }
    }
}