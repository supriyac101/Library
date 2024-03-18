<?php

class Webskitters_Creditsave_Block_Adminhtml_Creditsave_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'creditsave';
        $this->_controller = 'adminhtml_creditsave';
        
        $this->_updateButton('save', 'label', Mage::helper('creditsave')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('creditsave')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('creditsave_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'creditsave_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'creditsave_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('creditsave_data') && Mage::registry('creditsave_data')->getId() ) {
            return Mage::helper('creditsave')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('creditsave_data')->getTitle()));
        } else {
            return Mage::helper('creditsave')->__('Add Item');
        }
    }
}