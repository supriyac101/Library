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
class Mconnect_Iconlib_Block_Iconlib extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getIconlib()     
     { 
        if (!$this->hasData('iconlib')) {
            $this->setData('iconlib', Mage::registry('iconlib'));
        }
        return $this->getData('iconlib');
        
    }
}