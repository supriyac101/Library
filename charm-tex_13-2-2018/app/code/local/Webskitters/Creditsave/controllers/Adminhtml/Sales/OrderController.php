<?php
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class Webskitters_Creditsave_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{

	public function deleteccsaveAction()
    {
		
		//echo "here we go"; die();
		
        if ($order = $this->_initOrder()) {
            
			$collection = Mage::getModel('creditsave/creditsave')->getCollection()->addFieldToFilter('order_entiry_id', $order->getId())->getData();
			
			$creditsave_id = $collection[0]['creditsave_id'];
			$model = Mage::getModel('creditsave/creditsave');
			$delete = $model->setId($creditsave_id)->delete();
			if($creditsave_id) {
			
				$this->_getSession()->addSuccess(
                    $this->__('Payment information has been deleted successfully.')
                );
				
			} else {
				
				$this->_getSession()->addError('No Payment information found');
	
			}
			
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }
	
	
}