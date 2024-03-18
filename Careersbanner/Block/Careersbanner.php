<?php
class Custom_Careersbanner_Block_Careersbanner extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getCareersbanner()     
    {
        if (!$this->hasData('careersbanner')) {
            $this->setData('careersbanner', Mage::registry('careersbanner'));
        }
        return $this->getData('careersbanner');
        
    }
}