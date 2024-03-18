<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php

class Mconnect_Iconlib_Block_Adminhtml_Iconlib_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'iconlib';
        $this->_controller = 'adminhtml_iconlib';
        
        $this->_updateButton('save', 'label', Mage::helper('iconlib')->__('Save Icon Record'));
        $this->_updateButton('delete', 'label', Mage::helper('iconlib')->__('Delete Icon Record'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('iconlib_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'iconlib_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'iconlib_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('iconlib_data') && Mage::registry('iconlib_data')->getId() ) {
            return Mage::helper('iconlib')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('iconlib_data')->getIconlabel()));
        } else {
            return Mage::helper('iconlib')->__('Add Icon Record');
        }
    }
}