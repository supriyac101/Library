<?php
class Extendware_EWPribbon_Block_Adminhtml_Config_Form_Field_Fallbackposition extends Extendware_EWCore_Block_Mage_Adminhtml_System_Config_Form_Field_Array_Abstract
{
	protected $_addAfter = false;
    public function __construct()
    {
    	$this->addColumn('selector', array(
            'label' => $this->__('CSS Selector'),
    		'style'	=> 'width: 99%',
    		'class' => 'required-entry',
        ));
        
        $this->_addButtonLabel = $this->__('Add CSS Selector');
        parent::__construct();
    }
}