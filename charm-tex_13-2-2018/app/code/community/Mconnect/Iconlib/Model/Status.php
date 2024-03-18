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

class Mconnect_Iconlib_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('iconlib')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('iconlib')->__('Disabled')
        );
    }
}