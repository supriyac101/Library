<?php

/**
 *
 * @category    Brainvire
 * @package     Brainvire_Ordercomment
 * @copyright   Copyright (c) 2015 Brainvire Infotech Pvt Ltd
 * 
 * */
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Brainvire_Ordercomment_Controller_Onepage_Abstract extends Mage_Checkout_OnepageController {

    /*
    * Saving the Payment at Checkout
    */
    public function savePaymentAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
           

            try {
                $result = $this->getOnepage()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
				$this->loadLayout('checkout_onepage_ordercomment');

                $result['goto_section'] = 'ordercomment';
            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    /*
    * Saving the order comment 
    */
    public function saveOrdercommentAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            
        
        	$_brainvire_Ordercomment = $this->getRequest()->getPost('ordercomment');
			
            Mage::getSingleton('core/session')->setBrainvireOrdercomment($_brainvire_Ordercomment);

			$result = array();
            
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (!$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');

                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );

            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }    
}
