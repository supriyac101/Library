<?php
include_once 'Velocity/simple_html_dom.php';
class Velocity_Supercheckout_Block_Payment_Methods extends Mage_Payment_Block_Form_Container
{
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract|null
     * @return bool
     */
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if ($methods === null) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = array();
            foreach ($this->helper('payment')->getStoreMethods($store, $quote) as $method) {
                 {
                    $this->_assignMethod($method);
                    $methods[] = $method;
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseCheckout() && parent::_canUseMethod($method);
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode(){
        if ($method = $this->getQuote()->getPayment()->getMethod()) {
            return $method;
        }
        return false;
    }

    /**
     * Payment method form html getter
     * @param Mage_Payment_Model_Method_Abstract $method
     */
    public function getPaymentMethodFormHtml(Mage_Payment_Model_Method_Abstract $method)
    {
        $html = $this->getChildHtml('payment.method.' . $method->getCode());
        $html_dom = str_get_html($html);
        $str = "";
        if($html_dom){
            foreach($html_dom->find('ul') as $form) {       
                $form->removeAttribute('style');
                $x = $form->attr['ng-show']= 'payment_form_show'."=='". $method->getCode() ."'" ;
                //uncomment the follwing line 
                //$x = $form->attr['ng-if']= 'payment_form_show'."=='". $method->getCode() ."'" ;
            }
            $domelements = array('input[type=text]','select','textarea','input[type=radio]','input[type=checkbox]','input[type=hidden]');
            foreach($domelements as $dom){
                foreach($html_dom->find($dom) as $text) {                    
                        $name = $text->getAttribute('name');
                        if($name != ""){
                            $name_new = explode('[',$name);
                            $actual_name = substr($name_new[1],0,-1);
                            $text->removeAttribute('name');
                            $text->attr['ng-model']='data.payment.'.$actual_name;
                            $text->attr['ng-init']="data.payment.".$actual_name."=''";
                            
                            
                        }                        
                }
            }
            
            $str = $html_dom->save();
            
        }
         return $str;
         
    }

    /**
     * Return method title for payment selection page
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     */
    public function getMethodTitle(Mage_Payment_Model_Method_Abstract $method)
    {
        $form = $this->getChild('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodTitle()) {
            return $form->getMethodTitle();
        }
        return $method->getTitle();
    }

    /**
     * Payment method additional label part getter
     * @param Mage_Payment_Model_Method_Abstract $method
     */
    public function getMethodLabelAfterHtml(Mage_Payment_Model_Method_Abstract $method)
    {
        if ($form = $this->getChild('payment.method.' . $method->getCode())) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}
