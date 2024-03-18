<?php
class Webskitters_Mobilebanner_Block_Mobilebanner extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getMobilebanner()     
     { 
        if (!$this->hasData('mobilebanner')) {
            $this->setData('mobilebanner', Mage::registry('mobilebanner'));
        }
        return $this->getData('mobilebanner');
        
    }
}