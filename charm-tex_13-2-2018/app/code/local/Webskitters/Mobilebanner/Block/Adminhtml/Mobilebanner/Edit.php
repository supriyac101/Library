<?php

class Webskitters_Mobilebanner_Block_Adminhtml_Mobilebanner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'mobilebanner';
        $this->_controller = 'adminhtml_mobilebanner';
        
        $this->_updateButton('save', 'label', Mage::helper('mobilebanner')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('mobilebanner')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('mobilebanner_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'mobilebanner_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'mobilebanner_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mobilebanner_data') && Mage::registry('mobilebanner_data')->getId() ) {
            return Mage::helper('mobilebanner')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('mobilebanner_data')->getTitle()));
        } else {
            return Mage::helper('mobilebanner')->__('Add Item');
        }
    }
}