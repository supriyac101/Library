<?php

class Robeka_Ordergrid_Block_Adminhtml_Ordergrid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ordergrid';
        $this->_controller = 'adminhtml_ordergrid';
        
        $this->_updateButton('save', 'label', Mage::helper('ordergrid')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('ordergrid')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ordergrid_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ordergrid_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ordergrid_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ordergrid_data') && Mage::registry('ordergrid_data')->getId() ) {
            return Mage::helper('ordergrid')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('ordergrid_data')->getTitle()));
        } else {
            return Mage::helper('ordergrid')->__('Add Item');
        }
    }
}