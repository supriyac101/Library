<?php

class Bcs_Dailyfeature_Block_Adminhtml_Dailyfeature_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'dailyfeature';
        $this->_controller = 'adminhtml_dailyfeature';
        
        $this->_updateButton('save', 'label', Mage::helper('dailyfeature')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('dailyfeature')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('dailyfeature_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'dailyfeature_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'dailyfeature_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('dailyfeature_data') && Mage::registry('dailyfeature_data')->getId() ) {
            return Mage::helper('dailyfeature')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('dailyfeature_data')->getTitle()));
        } else {
            return Mage::helper('dailyfeature')->__('Add Item');
        }
    }
}