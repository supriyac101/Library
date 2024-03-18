<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\  Customer Approve/Disapprove  \\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   EH                            ///////
 \\\\\\\                      * @package    EH_CustomerApprove            \\\\\\\
 ///////    * @author     Suneet Kumar <suneet64@gmail.com>                ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\* @copyright  Copyright 2013 © www.extensionhut.com All right reserved\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class EH_CustomerApprove_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{
    public function __construct()
    {
		parent::__construct();
 
        if ($this->getCustomerId() &&
            Mage::getSingleton('admin/session')->isAllowed('customer/approve')) {

			// get current customer object
        	$customer = Mage::registry('current_customer');

			// add approve button
			if (!$customer->getMpCcIsApproved()) {
				$this->_addButton('approve', array(
					'label' => Mage::helper('customerapprove')->__('Approve'),
					'onclick' => 'setLocation(\'' . $this->getUrl('customerapprove/adminhtml_customer/approve', array('customer_id' => $this->getCustomerId())) . '\')',
					'class' => 'save',
				), 0);
			}
			else {
				// add disapprove button
				$this->_addButton('disapprove', array(
					'label' => Mage::helper('customerapprove')->__('Disapprove'),
					'onclick' => 'setLocation(\'' . $this->getUrl('customerapprove/adminhtml_customer/disapprove', array('customer_id' => $this->getCustomerId())) . '\')',
					'class' => 'delete',
				), 0);
			}
        }
    }

}
