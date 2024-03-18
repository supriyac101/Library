<?php

/** 
 * @category Brainvire 
 * @package Brainvire_Ordercomment 
 * @copyright Copyright (c) 2015 Brainvire Infotech Pvt Ltd
 */
class Brainvire_Ordercomment_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    /**
     * Initialize the Checkout processs
     */
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login'
                    || Mage::getSingleton('customer/session')->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }

        $checkout->setStepData('ordercomment', 'allow', true);

       
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }
        return $this;
    }
}