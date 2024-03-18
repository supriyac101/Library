<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/16/12
 * Time: 5:47 PM
 * To change this template use File | Settings | File Templates.
 */

class QS_Discount4Order_Block_System_Config_Method extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {

        $this->addColumn('amount2next', array(
            'label' => Mage::helper('adminhtml')->__('Amount to next discount'),
            'style' => 'width:140px'
        ));

        $this->addColumn('newdiscount', array(
            'label' => Mage::helper('adminhtml')->__('New discount'),
            'style' => 'width:70px'
        ));



     /*   $this->addColumn('free', array(
            'label' => Mage::helper('adminhtml')->__('Allow for Free'),
            'style' => 'width:50px',
            'renderer' => Mage::getBlockSingleton('iceship/system_config_renderer_yesno')
        ));*/

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Discount');
        parent::__construct();
    }
}
 
