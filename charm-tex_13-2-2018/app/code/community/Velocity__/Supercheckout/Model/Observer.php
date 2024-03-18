<?php
class Velocity_Supercheckout_Model_Observer
{
    public function emptyCart(){
//        echo 'cart empty';die;
    }
    public function applyComment($observer){
		$order = $observer->getData('order');
		$mmsg = Mage::getSingleton('core/session')->getMmsg();
		try{
			$order->setMsg($mmsg);
			$order->save();
		}catch(Exception $e){
			Mage::logException($e);
		}
	}
}