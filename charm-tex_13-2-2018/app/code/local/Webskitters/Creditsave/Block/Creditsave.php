<?php
class Webskitters_Creditsave_Block_Creditsave extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCreditsave()     
     { 
        if (!$this->hasData('creditsave')) {
            $this->setData('creditsave', Mage::registry('creditsave'));
        }
        return $this->getData('creditsave');
        
    }
}