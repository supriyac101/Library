<?php
class Bcs_Dailyfeature_Block_Dailyfeature extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getDailyfeature()     
     { 
        if (!$this->hasData('dailyfeature')) {
            $this->setData('dailyfeature', Mage::registry('dailyfeature'));
        }
        return $this->getData('dailyfeature');
        
    }
}