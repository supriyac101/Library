<?php
class Velocity_Supercheckout_Model_Observer
{
    public function emptyCart(){
//        echo 'cart empty';die;
    }
	
	
	 public function Savefield($observer){
        //get event data
        $event = $observer->getEvent();

        //get order
        $order = $event->getOrder();
		
        //set the country here
        $order->setMsg('AKYTESTMSG');
    }   
	
	
	
	
}