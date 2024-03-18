<?php
/** 
 * @category Brainvire 
 * @package Brainvire_Ordercomment 
 * @copyright Copyright (c) 2015 Brainvire Infotech Pvt Ltd
 */
class Brainvire_Ordercomment_Model_Observer
{
	
    /**
     * 
     * Comment is saved from agreement form to order
     * @param $observer
     */
    public function saveOrderComment($observer)
    {
        $orderComment = Mage::getSingleton('core/session')->getBrainvireOrdercomment();
       
            if (!empty($orderComment)) {
                $order = $observer->getEvent()->getOrder(); 
                $order->setCustomerComment($orderComment);
                $order->setCustomerNoteNotify(true);
                $order->setCustomerNote($orderComment);
            }
    }
}