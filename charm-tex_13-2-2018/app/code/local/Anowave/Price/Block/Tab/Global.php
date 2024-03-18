<?php
class Anowave_Price_Block_Tab_Global extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        $this->setTemplate('price/global.phtml');
    }
    
    public function getCustomtabInfo()
    {
        $customer  = Mage::registry('current_customer');
        
        $customtab = 'Global discount';
        
		return $customtab;
	}

	/**
	* Return Tab label
	*
	* @return string
	*/
    public function getTabLabel()
    {
        return $this->__('Global discount');
    }

	/**
	* Return Tab title
	*
	* @return string
	*/
    public function getTabTitle()
    {
        return $this->__('Global discount');
    }

	/**
	* Can show tab in tabs
	*
	* @return boolean
	*/
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        
        return (bool)$customer->getId() && $this->legit();
    }

	/**
	* Tab is hidden
	*
	* @return boolean
	*/
    public function isHidden()
    {
        return false;
    }

	/**
	* Defines after which tab, this tab should be rendered
	*
	* @return string
	*/
    public function getAfter()
    {
        return 'tags';
    }
    
    public function legit()
    {
    	return Mage::helper('price')->legit();
    }
}