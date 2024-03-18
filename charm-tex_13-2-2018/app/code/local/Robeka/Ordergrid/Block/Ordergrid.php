<?php
class Robeka_Ordergrid_Block_Ordergrid extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getOrdergrid()     
     { 
        if (!$this->hasData('ordergrid')) {
            $this->setData('ordergrid', Mage::registry('ordergrid'));
        }
        return $this->getData('ordergrid');
        
    }
}