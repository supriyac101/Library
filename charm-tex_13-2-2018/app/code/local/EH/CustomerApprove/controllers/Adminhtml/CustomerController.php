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

class EH_CustomerApprove_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{
	
	/**
	 * Approve a customer account
	 */
	public function approveAction()
	{
		// get customer id
    	$id = $this->getRequest()->getParam('customer_id');

		// attempt to load customer
		$model = Mage::getModel('customer/customer');

		if($id) {
			try {
				$model->load($id);

				if (!$model->getId()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerapprove')->__('This customer no longer exist or invalid customer id'));
				}
				else if ($model->getMpCcIsApproved()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerapprove')->__('This customer has already been approved'));
				}
				else {
					// approve customer
					$model->setMpCcIsApproved(true)
						->save();

					// send mail to customer confirming approval
					$model->sendAccountApprovalEmail($model->getStoreId());

					// add success message
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerapprove')->__('The customer has been approved'));
				}
			}
			catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
		}

		// redirect back to edit the customer
		$this->_redirect('adminhtml/customer/edit', array('id' => $id));
		return;
	}
	
	
	/**
	 * Disapprove a customer account
	 */
	public function disapproveAction()
	{		
		// get customer id
    	$id = $this->getRequest()->getParam('customer_id');

		// attempt to load customer
		$model = Mage::getModel('customer/customer');

		if($id) {
			try {
				$model->load($id);

				if (!$model->getId()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerapprove')->__('This customer no longer exist or invalid customer id'));
				}
				else if (!$model->getMpCcIsApproved()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customerapprove')->__('This customer is already unapproved'));
				}
				else {
					// approve customer
					$model->setMpCcIsApproved(false)
						->save();

					// add success message
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customerapprove')->__('The customer has been disapproved'));
				}
			}
			catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
		}

		$this->_redirect('adminhtml/customer/edit', array('id' => $id));
		return;
	}

	/**
	 * Approve multiple customer accounts at once
	 */
	public function massApproveAction()
	{
		// get customer ids
		$customersIds = $this->getRequest()->getParam('customer');

		// count of updated records
		$updatedCount = 0;

        if(!is_array($customersIds)) {
 			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
			try {
				foreach ($customersIds as $customerId) {
					// load customer
                    $customer = Mage::getModel('customer/customer')->load($customerId);

					if (!$customer->getMpCcIsApproved()) {
						// set customer as disapproved
						$customer->setMpCcIsApproved(true)
							->save();

						// send mail to customer confirming approval
						$customer->sendAccountApprovalEmail($customer->getStoreId());

						$updatedCount++;
					}
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', $updatedCount)
                );
            }
			catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/customer/index');
	}

	/**
	 * Disapprove multiple customer accounts at once
	 */
	public function massDisapproveAction()
	{
		// get customer ids
		$customersIds = $this->getRequest()->getParam('customer');

		// count of updated records
		$updatedCount = 0;

        if(!is_array($customersIds)) {
 			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
			try {
				foreach ($customersIds as $customerId) {
					// load customer
                    $customer = Mage::getModel('customer/customer')->load($customerId);

					if ($customer->getMpCcIsApproved()) {
						// set customer as disapproved
						$customer->setMpCcIsApproved(false)
							->save();

						$updatedCount++;
					}
                }
				
                Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', $updatedCount)
                );
            }
			catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
		
        $this->_redirect('adminhtml/customer/index');
	}
	
	/**
	 * User permission checkup
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/approve');
    }
	
}
