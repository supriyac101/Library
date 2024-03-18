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
 ///////    * @author     ExtensionHut <info@extensionhut.com>                ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\* @copyright  Copyright 2015 © www.extensionhut.com All right reserved\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class EH_CustomerApprove_Model_Observer
{

	/**
	 * Retrieve customer session
	 *
	 * @return Mage_Customer_Model_Session
	 */
	public function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * Check if a customer is approved before signing in
	 *
	 * Executed on: customer_login
	 *
	 * @param object $event
	 */
	public function checkApproveStatus($event)
	{
		// skip this checkup for the account creation / confirmation process
		$actionName = strtolower(Mage::app()->getRequest()->getActionName());

		if ($actionName == 'createpost' || $actionName == 'create' || $actionName == 'confirm' || $actionName == 'confirmation') {
			return;
		}

		// make sure extension is enabled before we continue
		if (!Mage::helper('customerapprove')->getIsEnabled()) {
			return;
		}

		// do redirect
		$this->redirectCustomer($event->getCustomer());
	}

	/**
	 * Set new customer to be approved if auto approve option selected Yes
	 *
	 * Executed on: customer_register_success
	 *
	 * @param object $observer
	 */	
	public function setNewCustomerStatus(Varien_Event_Observer $observer){
		$customer = $observer->getCustomer();
		if(Mage::helper('customerapprove')->getIsAutoApprove()){
			$customer->setMpCcIsApproved(true)->save();
		}
		else{
			$customer->setMpCcIsApproved(false)->save();
		}
	}
	
	/**
	 * Check if a customer is approved before signing in after account is created
	 *
	 * Executed on: customer_new_account_email_sent
	 *
	 * @param object $customer
	 */
	public function customerNewAccountCompleted($customer)
	{
		// make sure extension is enabled before we continue
		if (!Mage::helper('customerapprove')->getIsEnabled()) {
			return;
		}

		// do redirect
		$this->redirectCustomer($customer);
	}

	/**
	 * Redirect customer to another page
	 *
	 * @param $customer Mage_Customer_Model_Customer
	 * @return void
	 */
	public function redirectCustomer($customer)
	{
		// get helper object
		$helper = Mage::helper('customerapprove');

		// in some cases the $customer variable will be an event including a customer object
		if (!($customer instanceof Mage_Customer_Model_Customer)) {
			if ($customer->getCustomer()) {
				$customer = $customer->getCustomer();
			}
		}

		// now check if the customer who just logged in is approved
		$approved = (int) $customer->getMpCcIsApproved() == 1 ? true : false;

		// customer wasn't approved
		if (!$approved) {
			// logout customer
			$this->_getCustomerSession()->logout()
            	->setBeforeAuthUrl(Mage::getUrl());

			if ($helper->getRedirectEnabled()) {
				// get redirect URL
				$redirectURL = $helper->getRedirectUrl();

				// redirect customer
				header("Status: 301");
				header('Location: '.$redirectURL);
				exit;
			}
			else if ($helper->getErrorMsgEnabled()) {
				$this->_getCustomerSession()->addError($helper->getErrorMsgText());
				return;
			}

		}
	}

}
