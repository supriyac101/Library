<?php
class Custom_Careers_Block_View extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    /*public function getCareers()     
    { 
        if (!$this->hasData('careers')) {
            $this->setData('careers', Mage::registry('careers'));
        }
        return $this->getData('careers');
        
    }*/
}
     