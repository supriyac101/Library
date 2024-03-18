<?php
/** 
 * @category Brainvire 
 * @package Brainvire_Ordercomment 
 * @copyright Copyright (c) 2015 Brainvire Infotech Pvt Ltd
 */
class Brainvire_Ordercomment_Block_Onepage extends Mage_Checkout_Block_Onepage
{
    /**
     * getting the steps of Checkout page
     */
    public function getSteps()
    {
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }

        $stepCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'ordercomment', 'review');

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        
        return $steps;
    }
}