<?php
/**/
include_once 'Velocity/setup.php';

//include('../simple_html_dom.php');
class Velocity_Supercheckout_IndexController extends Mage_Checkout_Controller_Action {
    const XML_PATH_PAYMENT_GROUPS = 'global/payment/groups';
    
   
    public function indexAction(){
        if(Mage::getSingleton('customer/session')->isLoggedIn() && !(Mage::getSingleton('core/session')->getData('shipping_reset'))){
            Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setShippingMethod(NULL);
             Mage::getSingleton('core/session')->setData('shipping_reset',1);
        }
        $mk = $this->updateSupercheckoutController();
        if($mk == true){
        
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $x = $quote->getShippingAddress()->getShippingMethod();
        Mage::helper('supercheckout')->isSupercheckoutEnabled();
//        Mage::helper('supercheckout')->setSelectedShippingMethod();
        
        /*************** Loading Model ****************/
        $model = Mage::getModel('supercheckout/supercheckout');
//        /*********** getting settings **********************/
        $settings = Mage::helper('supercheckout')->getSettings() ;
        /************** settings for view **********************/
        Mage::getSingleton('supercheckout/supercheckout')->setData('settings',$settings);
        /******** BEGIN setting default data for view use ******/
        Mage::getSingleton('supercheckout/supercheckout')->setData('account',$settings['general']['default_option']);
        /******** END setting default data for view use ******/
        
        //if supercheckout is enabled -> if not redirect to cart page
        if (!Mage::helper('supercheckout')->isSupercheckoutEnabled()){
            Mage::getSingleton('checkout/session')->addError(Mage::helper('supercheckout')->__('Supercheckout checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        //getting quote -> if not redirect to cart page
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        // checking for minimum value -> if not redirect to cart page
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        
//       google aCTION
        $this->googleAction();
        $setcart= array();
        $shipping_method_details= array();
        
        $shipping_method_details = $this->getShippingMethodAction();
        
        $countries_details = $this->getCountriesAction();
        
        $payment_method_details = $this->getPaymentMethodsSupercheckoutAction();
        $billing_address_session = $this->getSupercheckout()->getSessionValueAddress('billing');
        $shipping_method_session = $this->getSupercheckout()->getSessionMethods('shipping');
        
        
        $payment_method_session = $this->getSupercheckout()->getSessionMethods('payment');
        //var_dump($shipping_method_session);echo 'ethe waj';
        $payment_address_select_option = $this->getCustomerBillingAddress();
        $agreement_array = $this->getAgreements();
        
        Mage::getSingleton('supercheckout/supercheckout')->setData('agreement_array',$agreement_array);
        Mage::getSingleton('supercheckout/supercheckout')->setData('shipping_method_details',$shipping_method_details);
        Mage::getSingleton('supercheckout/supercheckout')->setData('payment_method_details',$payment_method_details);
        Mage::getSingleton('supercheckout/supercheckout')->setData('countries_details',$countries_details);
        Mage::getSingleton('supercheckout/supercheckout')->setData('billing_address_session',$billing_address_session);
        Mage::getSingleton('supercheckout/supercheckout')->setData('shipping_method_session',$shipping_method_session);
        Mage::getSingleton('supercheckout/supercheckout')->setData('payment_method_session',$payment_method_session);
        Mage::getSingleton('supercheckout/supercheckout')->setData('payment_address_select_option',$payment_address_select_option);
        
        $x = Mage::helper('supercheckout')->getSelectedShippingMethod();
        
        $quote->getShippingAddress()->setShippingMethod($x)->save();
        $setcart = $this->getitemdetailsAction();
        Mage::getSingleton('supercheckout/supercheckout')->setData('cart',$setcart);
        $y = Mage::helper('supercheckout')->getSelectedShippingMethod();
        
        
        //billing address
        $this->loadLayout()->getLayout()->getBlock('supercheckout'); // or we can write $this->loadLayout(); and then in next line $this->renderLayout();
        $this->renderLayout();
        $y = Mage::helper('supercheckout')->getSelectedShippingMethod();
        }
    }
    public function loginAction(){
        $data = json_decode(file_get_contents("php://input"));
        $ajax = $data->ajax;
        $_POST['email'] = $data->email;
        $_POST['password'] = $data->password;
        if ($data->ajax == 'login' && Mage::helper('customer')->isLoggedIn() != true)
            {
                $login = Mage::getSingleton('supercheckout/login');
                echo $login->getResult();
            }
    }
    public function checkuserAction() {   //check for registered users
        if (Mage::helper('customer')->isLoggedIn()) {
            echo "loggedin";
        } else {
            $email = $this->getRequest()->getParam('email');
            $customer = Mage::getModel('customer/customer');
        
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($email);

            if($customer->getId())
            {
                echo 'registered';
            }else{
                echo 'notregistered';
            }
        }
    }
    public function dologinAction() {// logging into store
        $email = $this->getRequest()->getParam('emailLogin');

        try
        {
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($email);
            $session = Mage::getSingleton("customer/session");
            $session->loginById($customer->getId());
            $session->setCustomerAsLoggedIn($customer);
            echo 'supercheckout';
        }
        catch(Exception $ex)
        {
            echo "account";
        }
        //for loging in the customer registered through facebook

    }

    public function getvalueAction($data = array()) {//for facebook registration
        
        $checkpoint_email =$this->getRequest()->getParam('useremail');
        
        if(isset($checkpoint_email) && $checkpoint_email !=""){
            $customer = Mage::getModel('customer/customer');

            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());

            $customer->setEmail($this->getRequest()->getParam('useremail'));
            $customer->setPassword(substr(md5(uniqid(rand(), true)), 0, 9));
            $customer->setFirstname($this->getRequest()->getParam('firstname'));
            $customer->setLastname($this->getRequest()->getParam('last_name'));

            try
            {
                $customer->save();
                $customer->setConfirmation('1'); // or it should be null
                $customer->save();
                $storeId = $customer->getSendemailStoreId();
                $customer->sendNewAccountEmail('registered', '', $storeId);
                Mage::getSingleton('customer/session')->loginById($customer->getId());
                echo "supercheckout";
                die();
            }
            catch (Exception $ex)
            {
                echo "account";
            }
        } else {
            echo 'Something Went Wrong ! :(';
        }
    }
    public function googleAction(){
        
//        //google login settings
        $settings = Mage::helper('supercheckout')->getSettings() ;
        curl_setopt($ch, CURLOPT_SSLVERSION, 3); 
        $client = new apiClient();

        $redirect_url = Mage::getUrl('*');        
        $client->setClientId($settings['step']['google_login']['client_id']);
        $client->setClientSecret($settings['step']['google_login']['app_secret']);
        $client->setDeveloperKey($settings['step']['google_login']['app_id']);
        
        $client->setRedirectUri($redirect_url);
        $client->setApprovalPrompt(false);

        $oauth2 = new apiOauth2Service($client);

        Mage::getSingleton('supercheckout/supercheckout')->setData('client',$client);
        $url = ($client->createAuthUrl());
        Mage::getSingleton('supercheckout/supercheckout')->setData('googleurl',$url);
        if (($this->getRequest()->getParam('code'))) {

            $client->authenticate();
            $info = $oauth2->userinfo->get();
            if (isset($info['given_name']) && $info['given_name'] != "") {

                $name = $info['given_name'];

            } else {

                $name = $info['name'];

            }

            $customer = Mage::getModel('customer/customer');
        
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($info['email']);

            if($customer->getId())
            {
                try
                {
                    $customer = Mage::getModel("customer/customer");
                    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                    $customer->loadByEmail($info['email']);
                    $session = Mage::getSingleton("customer/session");
                    $session->loginById($customer->getId());
                    $session->setCustomerAsLoggedIn($customer);
                    echo'<script>window.opener.location.href ="' . $redirect_url . '"; window.close();</script>';
                }
                catch(Exception $ex)
                {
                    echo "Oops! Something Went Wrong!";
                }
            }else{
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());

                $customer->setEmail($info['email']);
                $customer->setPassword(substr(md5(uniqid(rand(), true)), 0, 9));
                $customer->setFirstname($name);
                $customer->setLastname($info['family_name']);

                try
                {
                    $customer->save();
                    $customer->setConfirmation('1'); // or it should be null
                    $customer->save();

                    $storeId = $customer->getSendemailStoreId();
                    $customer->sendNewAccountEmail('registered', '', $storeId);

                    Mage::getSingleton('customer/session')->loginById($customer->getId());


                    echo'<script>window.opener.location.href ="' . $redirect_url . '"; window.close();</script>';
                    die();
                }
                catch (Exception $ex)
                {
                    echo "Oops! Something Went Wrong!";
                }
            }        
            
        }
    }
    public function getDefaultZoneAction(){
        $getCountryId = $this->getRequest()->getParam('country_id');
        if(isset($getCountryId)){
            $countrycode = $this->getRequest()->getParam('country_id');
        }
        if ($countrycode != '') {
            $state = "<select ng-model='data.billing.zone' style='width:98%;'><option value=''>Please Select</option>";
            $statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
            if(count($statearray)>0){
                foreach ($statearray as $_state) {
                        $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
                    }
                
                $state .= '</select>';
            }else{
                $state = '<input type="text">';
            }
        }
        
        echo $state;
    }
    public function updatecartAction(){
        $item_id = $this->getRequest()->getParam('item_id');
        $cart = Mage::getSingleton('checkout/cart'); 
        $qty = $this->getRequest()->getParam('qty');
        $items = $cart->getItems();
        foreach ($items as $item) {   // LOOP
            if($item->getId()==$item_id){  // IS THIS THE ITEM WE ARE CHANGING? IF IT IS:
                    $item->setQty($qty); // UPDATE ONLY THE QTY, NOTHING ELSE!
                    $cart->save();  // SAVE                    
                    break;
            }
        }
        
        $x = $this->getitemdetailsAction();
        
        echo json_encode($x);
    }
    public function deleteitemAction(){
        $item_id = $this->getRequest()->getParam('item_id');
        $cart = Mage::getSingleton('checkout/cart'); 
        
        $items = $cart->getItems();        
        foreach ($items as $item) 
        {
                $itemId = $item->getItemId();
                if($itemId == $item_id){
                    $cart->removeItem($itemId)->save();
                }
        } 
        $x = $this->getitemdetailsAction();
        
        echo json_encode($x);
    }
    public function addcouponAction(){
        $cart = Mage::getSingleton('checkout/cart');
        $couponCode = $this->getRequest()->getParam('coupon_code');
        try {
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= 255;

            $cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $cart->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $cart->getQuote()->getCouponCode()) {
                    
                    $coupon_error['success']= $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode));
                    
                } else {
                    
                    $coupon_error['error'] =    $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
                }
            } else {
                $coupon_error['error'] =  $this->__('Coupon code was canceled.');
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }
        echo json_encode($coupon_error);       
        
    }
    
    public function removecouponAction(){
        
        $cart = Mage::getSingleton('checkout/cart');
        $cart->getQuote()->setCouponCode('')->collectTotals()->save();
        $x = $this->getitemdetailsAction();
        
        echo json_encode($x);
        
    }
    public function getitemdetailsAction(){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $_rates = Mage::helper('checkout')->getQuote()->getShippingAddress()->getShippingRatesCollection();
        $selected_shipping_method = $this->getSelectedShippingMethod();
        $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
        $quote->collectTotals()->save();
        $current_shipping_method = Mage::helper('checkout')->getQuote()->getShippingAddress()->getShippingMethod();

        $shippingRates = array();
        foreach ($_rates as $_rate){
                if($_rate->getPrice() > -1) {
                    if($_rate->getCode() == $current_shipping_method){
                        $shippingRates =  array("title" => $_rate->getMethodTitle(), "price" => Mage::helper('core')->currency(number_format($_rate->getPrice(),2,'.',''),true,false));
                    }                
                }
        }

        $checkoutSession = Mage::getSingleton('checkout/session');
        $checkoutquote = Mage::getSingleton('checkout/session')->getQuote();
        $totalsquote =  $checkoutquote->getTotals();
        $settings = Mage::helper('supercheckout')->getSettings() ;
        foreach ($checkoutSession->getQuote()->getAllVisibleItems() as $item) {
        $_item = Mage::getModel('catalog/product')->load($item->getProductId());
       
	$helper = Mage::helper('catalog/product_configuration');
       $var=$helper->getCustomOptions($item);
       
    $optionstr='';   
	if($var) {
    foreach ($var as $o) {
	// $optionstr.='<span>'.$o['label'].': </span>'.$o['print_value'].'<br>'; 
	$optionstr.=$o['label'].':'.$o['print_value'].' '; 
    }

    
}
        $products[] = array(
                        'image' => isset($settings['step']['cart']['image_width']) && isset($settings['step']['cart']['image_height'])?$_item->getSmallImageUrl($settings['step']['cart']['image_width'],$settings['step']['cart']['image_height']):$_item->getSmallImageUrl(180,180),
                        'id'=>$item->getId(),
                        'sku'=>$item->getSku(),
                        'name'  => $_item->getName(),
                        'qty'   => $item->getQty(),
                        'price' => Mage::helper('core')->currency(number_format($item->getBaseCalculationPrice(), 2, '.', ''),true,false),
                        'total' => Mage::helper('core')->currency($item->getRowTotal(),true,false),
			'custom_options' => $optionstr,
			    
                        );
        }
        $totals =  array();
        $i = 'a';
        foreach($totalsquote as $key=>$value){
            if($key == 'discount'){
                $totals[] = array('title'=>$value['title'],'value'=>Mage::helper('core')->currency(number_format($value['value'],2,'.',''),true,false),'remove'=>true,'sort'=>$i);
            }else{
                $totals[] = array('title'=>$value['title'],'value'=>Mage::helper('core')->currency(number_format($value['value'],2,'.',''),true,false),'remove'=>false,'sort'=>$i);
            }
            $i++;
            
        }
        
        $cart_details = array('product_details'=> $products,'total_details'=>$totals);
        
        if($this->getRequest()->getParam('refresh') && $this->getRequest()->getParam('refresh') == 3){
            if (!$quote->hasItems() || $quote->getHasError()) {
                echo json_encode(array('redirect'=>true));
            }else{
                echo json_encode($cart_details);
            }
        }else{
            if (!$quote->hasItems() || $quote->getHasError()) {
                return array('redirect'=>true);
            }
            return $cart_details;
        }
    }
    public function getShippingMethodAction(){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $shipping_method = array();
        $address = $quote->getShippingAddress();
        $defaultCountryId = Mage::getStoreConfig('general/country/default');
        $country = $address->getCountryId() ? $address->getCountryId() : $defaultCountryId;
        $address->setCountryId($country)->setCollectShippingRates(true)->collectShippingRates();
        $_shippingRateGroups = $quote->getShippingAddress()->getGroupedAllShippingRates();
        
        $quote->save();
            foreach ($_shippingRateGroups as $code => $_rates){
                
                $shipping_method_title = $this->getCarrierName($code);
                
                foreach ($_rates as $_rate){
                    if ($_rate->getErrorMessage()){
                        $shipping_method_error = $_rate->getErrorMessage();
                    }else{
                        
                        $helper_tax = Mage::helper('tax');
                        $_excl = $this->getShippingPrice($_rate->getPrice(), $helper_tax->displayShippingPriceIncludingTax());
                        $_incl = $this->getShippingPrice($_rate->getPrice(), true);
                        if($helper_tax->displayShippingBothPrices() && ($_incl != $_excl)){
                            $incl_string = '('.$this->__('Incl. Tax');echo $_incl.')';
                        }
                        $shipping_method[] = array(
                            'error'=>$shipping_method_error,
                            'title'=>$shipping_method_title,
                            'value'=>$_rate->getCode(),
                            'label'=>$_rate->getMethodTitle(),
                            'exclusive'=>  strip_tags($_excl),
                            'inclusive'=> strip_tags($incl_string),
                            );
                    }
                }
            }
            if($this->getRequest()->getParam('refresh') && $this->getRequest()->getParam('refresh') == 1){
                echo json_encode($shipping_method);
            }else{
                return ($shipping_method);
            }
    }
    public function getCarrierName($carrierCode){
        
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }
    public function getShippingPrice($price, $flag){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        return $quote->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $quote->getAddress()), true);
    }
    public function shippingMethodClickAction(){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $selected_shipping_method = $this->getRequest()->getParam('shipping_method');
        $quote->getShippingAddress()->setShippingMethod($selected_shipping_method)->save();
        $quote->collectTotals()->save();
    }
    public function paymentMethodClickAction(){
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $selected_payment_method = $this->getRequest()->getParam('payment_method');
        //check for why not shipping address and if shipping address to then when ?
        echo 'set at default not working';
        $quote->getBillingAddress()->setPaymentMethod($selected_payment_method)->save();
        $quote->collectTotals()->save();
    }
    public function cartRefreshAction() {
        $cart_json = array();
        $cart_json = $this->getitemdetailsAction();
        echo json_encode($cart_json);
    }
    public function shippingMethodRefreshAction() {
        $shipping_method_reload = array();
        $shipping_method_reload = $this->getShippingMethodAction();
        echo json_encode($shipping_method_reload);
    }
    public function getPaymentMethodsSupercheckoutAction(){

        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $address = $quote->getBillingAddress();
        $country = $address->getCountryId();        
        $address->setCountryId($country);
        $quote->save();
        
        $methods = null;//$this->getData('methods');
        if ($methods === null) {
            $quote = $session->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = array();
            foreach ($this->getStoreMethods($store, $quote) as $method) {                
                if ($this->_canUseMethod($method,$method->getCode())) {
                    $this->_assignMethod($method);
                    $methods[] = $method;
                }
            }
        }
        
        $payment_methods = array();
        foreach ($methods as $_method){ 
            $_code = $_method->getCode();
            if( sizeof($methods) > 1 ){
                $payment_methods[] = array('code'=>$_code,'title'=>$_method->getTitle());
            }
        }
        if($this->getRequest()->getParam('refresh') && $this->getRequest()->getParam('refresh') == 2){
            echo json_encode($payment_methods);
        }else{
            return $payment_methods;
        }
    }
    protected function _canUseMethod($method,$code){
        $session = Mage::getSingleton('checkout/session');
        return $this->isApplicableToQuote($session->getQuote(),$code);
    }
    public function isApplicableToQuote($quote,$code){
            if (!$this->canUseForCountry($quote->getBillingAddress()->getCountry(),$code)) {
                return false;
            }else{
                return true;
            }
        
    }
    public function canUseForCountry($country,$code){
        /*
        for specific country, the flag will set up as 1
        */
        if($this->getConfigData('allowspecific',$code)==1){
            $availableCountries = explode(',', $this->getConfigData('specificcountry',$code));
            if(!in_array($country, $availableCountries)){
                return false;
            }

        }
        return true;
    }
    public function getConfigData($field,$code, $storeId = null){
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (null === $storeId) {
            $storeId = $quote->getStoreId(); //$this->getStore();
        }
        $path = 'payment/'.$code.'/'.$field;
        return Mage::getStoreConfig($path);
    }
    protected function _assignMethod($method){
        $session = Mage::getSingleton('checkout/session'); 
        $method->setInfoInstance($session->getQuote()->getPayment());
        return $this;
    }
    public function getStoreMethods($store = null, $quote = null){
        $res = array();
        foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
            $prefix = 'payment' . '/' . $code . '/';
            if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                continue;
            }
            $methodInstance = Mage::getModel($model);
            if (!$methodInstance) {
                continue;
            }
            $methodInstance->setStore($store);
            if (!$methodInstance->isAvailable($quote)) {
                /* if the payment method cannot be used at this time */
                continue;
            }
            $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
            $methodInstance->setSortOrder($sortOrder);
            $res[] = $methodInstance;
        }
        usort($res, array($this, '_sortMethods'));
        return $res;
    }
    public function getPaymentMethods($store = null){
        return Mage::getStoreConfig('payment', $store);
    }
    public function getSelectedMethodCode(){
        $methods = $this->getMethods();
        if (!empty($methods)) {
            reset($methods);
            return current($methods)->getCode();
        }
        return false;
    }
    public function getCode(){
        if (empty($this->_code)) {
            Mage::throwException(Mage::helper('payment')->__('Cannot retrieve the payment method code.'));
        }
        return $this->_code;
    }
    public function saveOrderAction(){

        $validation_enabled	= 1;//Mage::helper('onepagecheckout')->isAddressVerificationEnabled();
        $result = array();
        $settings = Mage::helper('supercheckout')->getSettings() ;
        
        try {
            #### GETTING BILLLING ADDRESS DETAILS  ########
            $data = json_decode(file_get_contents("php://input"));
            $_POST['billing'] = $data->billing;
            $_POST['shipping'] = $data->shipping;
            $_POST['shipping_method'] = $data->shipping_method;
            $_POST['orderComment'] = $data->orderComment;
            $_POST['billing_address_id'] = $data->billing_address_id;
            $_POST['shipping_address_id'] = $data->shipping_address_id;
            $_POST['paymentAddressRadio'] = $data->paymentAddressRadio;
            $_POST['shippingAddressRadio'] = $data->shippingAddressRadio;
            $_POST['agreement'] = (array)$data->agreement;
            $bill_data = (array)($this->getRequest()->getPost('billing', array()));
            $bill_addr_id = $this->getRequest()->getPost('billing_address_id', false);
	        // need for verification
        	$ship_updated = false;
        	$shipping_address_changed	= false;

        	// get prev shipping data.
			$prev_ship = $this->getSupercheckout()->getQuote()->getShippingAddress();
                        ### THIS IS TO FILL THE TEXTBOXES IN CASE "use_of_shipping" IS NOT USED
			$prev_same_as_bill = $prev_ship->getSameAsBilling();
                        

	        $billing_address_changed	= false;
                #### CHECKING WHETHER ADDRESS WAS CHANGED FROM WHAT IT WAS BEFORE #####
	        if($this->_checkChangedAddress($bill_data, 'Billing', $bill_addr_id, $validation_enabled)){
                    ### IF CHANGED, THEN VALIDATION OF BILLING ADDRESS WILL AGAIN TAKE PLACE #####
	        	$billing_address_changed	= true;
	        	$this->getSupercheckout()->getCheckout()->setBillingWasValidated(false);
	        }
                
                ##### SAVES THE BILLING ADDRESS ### 
                if($this->getRequest()->getPost('paymentAddressRadio',false) && $this->getRequest()->getPost('paymentAddressRadio') == 'existing'){
                    if($settings['general']['guestenable']){
                        $result = $this->getSupercheckout()->saveBilling($bill_data,$bill_addr_id,false,true);
                    }else{
                        $result = $this->getSupercheckout()->saveBilling($bill_data,$bill_addr_id,false,false);
                    }
                }else{
                    
                    $bill_addr_id = -1;
                    if($settings['general']['guestenable']){
                        $result = $this->getSupercheckout()->saveBilling($bill_data,$bill_addr_id,true,true);
                    }else{
                        $result = $this->getSupercheckout()->saveBilling($bill_data,$bill_addr_id,true,false);
                    }
                }
                
                if ($result){

                    #### IF ERROR IS FOUND JSON RESPONSE IS GIVEN ######
                    $result['error_messages'] = $result['message'];
                    $result['error'] = true;
                    $result['success'] = false;
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;#### IF ERROR IS FOUND THE PAGE IS RETURNED FROM HERE
                }
                
                #### IF "USE FOR SHIPPING" IS TRUE ###
                if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == true && !$this->getSupercheckout()->getQuote()->isVirtual()){
                        $ship_updated = true;
                        if($billing_address_changed){ ## IF BILLING ADDRESS WAS CHANGES SO WILL BE THE SHIPPNG ADDRESS
                            $shipping_address_changed = true;
                        }
                        ### THUS WE WILL SKIP THE VALIDATION OF SHPPING ADDRESS ###
                        $this->getSupercheckout()->getCheckout()->setShippingWasValidated(true);
                }
                
            #### IF "use_for_shipping" IS NOT USED ####
            if ((!$bill_data['use_for_shipping'] || !isset($bill_data['use_for_shipping'])) && !$this->getSupercheckout()->getQuote()->isVirtual()){
                                
		        $ship_data		= $this->_filterPostData($this->getRequest()->getPost('shipping', array()));
                        
		        $ship_addr_id	= $this->getRequest()->getPost('shipping_address_id', false);

		        if (!$ship_updated){
                            
		        	if ($this->_checkChangedAddress($ship_data, 'Shipping', $ship_addr_id, $validation_enabled))
		        	{
		        		$shipping_address_changed = true;
		        		$this->getSupercheckout()->getCheckout()->setShippingWasValidated(false);
		        	}
		        	else
		        	{
		        		// check if 'use for shipping' has been changed
		        		if($prev_same_as_bill == 1)
		        		{
			        		$shipping_address_changed = true;
			        		$this->getSupercheckout()->getCheckout()->setShippingWasValidated(false);
		        		}
		        	}
		        }
                        if($this->getRequest()->getPost('shippingAddressRadio',false) && $this->getRequest()->getPost('shippingAddressRadio') == 'existing'){
                        
                            $result = $this->getSupercheckout()->saveShipping($ship_data,$ship_addr_id, false, true);
                        }else{

                            $ship_addr_id = -1;
                            $result = $this->getSupercheckout()->saveShipping($ship_data,$ship_addr_id, true, true);
                        }  
                    
                if ($result)
                {
                	$result['error_messages'] = $result['message'];
                	$result['error'] = true;
                    $result['success'] = false;
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
                
            }

        // check if user session is expired (user open checkout page, and wait too long before clicking on palce order button)
        ### IN ADDITION TO THAT IT ALSO TELL WHETHER USER IS CHECKING OUT AS GUEST OR LOGGED IN CUSTOMER ###    
                
        if(!$this->getSupercheckout()->checkValidCheckoutMethod()){
        	$err = Mage::helper('supercheckout')->__('Session is expired.');
        	$redirectUrl = Mage::getUrl('checkout/cart/index', array('_secure'=>true));
			$result['error'] = true;
			$result['success'] = false;
			$result['redirect'] = $redirectUrl;
			Mage::getSingleton('checkout/session')->addError($err);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
        }

    /*************** Address Verification ***********/
        
    // check if need to validate address 
	if($validation_enabled)
	{
            
		// setup library for validation
		$this->getSupercheckout()->setVerificationLib($validation_enabled);
		
		if(0){ // if not valid addresses allowed for checkout
			$bill_was_validated	= $this->getSupercheckout()->getCheckout()->getBillingWasValidated();
                }else{
			$bill_was_validated	= false;
                }

        if(!$bill_was_validated)
        {
	        $bill_validate	= $this->getSupercheckout()->validate_address('Billing');
	        if($bill_validate){
	        	$this->getSupercheckout()->getCheckout()->setBillingWasValidated(true);
                }else{
	        	$this->getSupercheckout()->getCheckout()->setBillingWasValidated(false);
                }
        }
        
        if(0){ // if not valid addresses allowed for checkout
        	$ship_was_validated	= $this->getSupercheckout()->getCheckout()->getShippingWasValidated();
        }else{
        	$ship_was_validated	= false;
        }
        
        if(!$this->getSupercheckout()->getQuote()->isVirtual())
        {
	        if(!$ship_was_validated)
	        {
	        	// check if shipping is the same as billing
				if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == 1)
					$this->getSupercheckout()->getCheckout()->setShippingWasValidated(true);
				else
				{
		        	$ship_validate	= $this->getSupercheckout()->validate_address('Shipping');
			        if($ship_validate)
			        	$this->getSupercheckout()->getCheckout()->setShippingWasValidated(true);
			        else
			        	$this->getSupercheckout()->getCheckout()->setShippingWasValidated(false);
				}
	        }
        }

        // check if exist validation results for any address
        if((isset($bill_validate) && is_array($bill_validate)) || (isset($ship_validate) && is_array($ship_validate)))
        {
        	if((isset($bill_validate) && isset($bill_validate['error']) && !empty($bill_validate['error'])) ||
        	   (isset($ship_validate) && isset($ship_validate['error']) && !empty($ship_validate['error'])) 
			)
			{
				$result['update_section']['address-candidates'] = $this->_getAddressCandidatesHtml();
	        	if(isset($bill_validate) && isset($bill_validate['error']))
	        	{
	        		if(!empty($bill_validate['error']))
	        			$result['not_valid_address'] = true;
	        		else
	        			$result['billing_valid'] = true;
	        	}
        	
	        	if(isset($ship_validate) && isset($ship_validate['error']))
	        	{
	        		if(!empty($ship_validate['error']))
	        			$result['not_valid_address'] = true;
	        		else
	        			$result['shipping_valid'] = true;
	        	}

		        // clear validation results
		        $this->getSupercheckout()->getCheckout()->setBillingValidationResults(false);
		        $this->getSupercheckout()->getCheckout()->setShippingValidationResults(false);
	        	
                $result['error'] = true;
				$result['success'] = false;
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
				return;
			}
        }
        
        // clear validation results
        $this->getSupercheckout()->getCheckout()->setBillingValidationResults(false);
        $this->getSupercheckout()->getCheckout()->setShippingValidationResults(false);
	}
    /*************** End Address Verification ***********/
            
            
            $agreements = Mage::helper('supercheckout')->getAgreeIds();
            if($agreements && ((Mage::getSingleton('customer/session')->isLoggedIn() && $settings['option']['logged']['confirm']['fields']['agree']['display'] )||(!Mage::getSingleton('customer/session')->isLoggedIn() && $settings['option']['guest']['confirm']['fields']['agree']['display']))  ){
                
				$post_agree = array_keys((array)$this->getRequest()->getPost('agreement', array()));
                                $post_agree_true = $this->getRequest()->getPost('agreement', array());
				$is_different = array_diff($agreements, $post_agree);
                if (!$is_different){
                    foreach($post_agree_true as $post_ag){
                        if($post_ag == true){
                            
                        }else{
                            $result['message'] = array('agree_to_terms'=>Mage::helper('supercheckout')->__('Please agree to all the terms and conditions.'));
                            $result['error'] = true;
                            $result['success'] = false;
                            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                            return;
                        }
                    }
                    
                }elseif($is_different){
                    $result['message'] = array('agree_to_terms'=>Mage::helper('supercheckout')->__('Please agree to all the terms and conditions.'));
                            $result['error'] = true;
                            $result['success'] = false;
                            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                            return;
                }
            }
            #### SAVE ORDER ###
            $result = $this->_saveOrderPurchase();
            
            if($result && !isset($result['redirect'])){
                $result['error_messages'] = $result['error'];
            }
            ### IF THERE IS NO ERROR ###
            if(!isset($result['error'])){
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getSupercheckout()->getQuote()));
                $this->_subscribeNews();
            }
            
            ##### GET COMMENT FROM THE USER AND SAVING ####
            Mage::getSingleton('customer/session')->setOrderCustomerComment($this->getRequest()->getPost('orderComment'));
            ##### SETTING NOTE FROM THE COMMENT BY USER #####
            $this->getSupercheckout()->getQuote()->setCustomerNote($this->getRequest()->getPost('orderComment'));
            
            ### IF NO REDIRECT COMMANT IS SET AND ALSO NO ERROR --->>> save order ####
            
            if (!isset($result['redirect']) && !isset($result['error'])){
                
                ### GET PAYMENT METHOD ####
            	$pmnt_data =(array) $this->getRequest()->getPost('payment', false);
                
                if($pmnt_data){          
                    
                    $this->getSupercheckout()->getQuote()->getPayment()->importData($pmnt_data);
                }
                
                $this->getSupercheckout()->saveOrder();
                
                $redirectUrl = $this->getSupercheckout()->getCheckout()->getRedirectUrl();
            
                $result['success'] = true;
                $result['error']   = false;
                $result['order_created'] = true;
            }
        }
        catch (Mage_Core_Exception $e){
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getSupercheckout()->getQuote(), $e->getMessage());
            
            if($e->getMessage() === "shipping_method_required_error"){
                $result['error'] = true;
                $result['message'] = array('shipping_method_required_error'=>$this->__('Please specify a shipping method.')) ;
                $result['success'] = false;
            }elseif($e->getMessage() === "payment_method_required_error"){
                $result['error'] = true;
                $result['message'] = array('payment_method_required_error'=>$this->__('Please specify a payment method.')) ;
                $result['success'] = false;
            }elseif($e->getMessage() === "invalid_checkout_guest"){
                $result['error'] = true;
                $result['message'] = array('invalid_guest_checkout'=>$this->__('Sorry, guest checkout is not allowed, please contact support.')) ;
                $result['success'] = false;
            }else{
                $result['error_messages'] = $e->getMessage();
                $result['error'] = true;
                $result['success'] = false;
            }

            $goto_section = $this->getSupercheckout()->getCheckout()->getGotoSection();
            if ($goto_section){
            	$this->getSupercheckout()->getCheckout()->setGotoSection(null);
                $result['goto_section'] = $goto_section;
            }

            $update_section = $this->getSupercheckout()->getCheckout()->getUpdateSection();
            if ($update_section){
                if (isset($this->_sectionUpdateFunctions[$update_section])){
                    $layout = $this->_getUpdatedLayout();

                    $updateSectionFunction = $this->_sectionUpdateFunctions[$update_section];
                    $result['update_section'] = array(
                        'name' => $update_section,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getSupercheckout()->getCheckout()->setUpdateSection(null);
            }

            $this->getSupercheckout()->getQuote()->save();
        } 
        catch (Exception $e){
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getSupercheckout()->getQuote(), $e->getMessage());
            $result['error_messages'] = Mage::helper('supercheckout')->__('There was an error processing your order. Please contact support or try again later.');
            $result['error']    = true;
            $result['success']  = false;
            
            $this->getSupercheckout()->getQuote()->save();
        }

        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _saveOrderPurchase(){
    	$result = array();
    	
        try{
            $data = json_decode(file_get_contents("php://input"));
            $_POST['payment'] = $data->payment;
            $pmnt_data = (array)$this->getRequest()->getPost('payment', array());
            ######SAVES PAYMENT LIKE METHOD AND CREDIT CART DETAILS DATA #####
            $result = $this->getSupercheckout()->savePayment($pmnt_data);
            ## THIS WILL GET REDIRECT URL FOR THE PAYMENT METHOD IF THERE IS ANY REDIRECT URI
            $redirectUrl = $this->getSupercheckout()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl){
                $result['redirect'] = $redirectUrl;
            }
        }
        catch (Mage_Payment_Exception $e){
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = true;
            $result['message'] = array('payment_method_error'=>$e->getMessage());
        }
        catch (Mage_Core_Exception $e){
            $result['error'] = true;
            $result['message'] = array('payment_method_error'=>$e->getMessage());
        }
        catch (Exception $e){
            Mage::logException($e);
            $result['error'] = Mage::helper('supercheckout')->__('Unable to set Payment Method.');
        }
        return $result;
    }
    public function getSupercheckout(){
        return Mage::getSingleton('supercheckout/type_gather');
    }
    protected function _checkChangedAddress($data, $addr_type = 'Billing', $addr_id = false, $check_city_street = false){
        $data = (array)$data;
    	$method	= "get{$addr_type}Address";
        $address = $this->getSupercheckout()->getQuote()->{$method}();

        if(!$addr_id)
        {
        	if(($address->getRegionId()	!= $data['region_id']) || ($address->getPostcode() != $data['postcode']) || ($address->getCountryId() != $data['country_id']))
        		return true;

        	// if need to compare street and city
        	if($check_city_street)
        	{
        		// check street address
        		$street1	= $address->getStreet();
        		$street2	= $data['street'];

        		if(is_array($street1))
        		{
        			if(is_array($street2))
        			{
        				if(trim(strtolower($street1[0])) != trim(strtolower($street2[0])))
        				{
        					return true;
        				}
        				if(isset($street1[1]))
        				{
        					if(isset($street2[1]))
        					{
        						if(trim(strtolower($street1[1])) != trim(strtolower($street2[1])))
        							return true;        						
        					}
        					else
        					{
        						if(!empty($street1[1]))
        							return true;
        					}
        				}
        				else
        				{
        					$s21	= trim($street2[1]);
        					if(isset($street2[1]) && !empty($s21))
        						return true;
        				}
        			}
        			else
        			{
        				if(trim(strtolower($street1[0])) != trim(strtolower($street2)))
        					return true;
        			}
        		}
        		else
        		{
        			if(is_array($street2))
        			{
        				if(trim(strtolower($street1)) != trim(strtolower($street2[0])))
        					return true;
        			}
        	else
        			{
        				if(trim(strtolower($street1)) != trim(strtolower($street2)))
        					return true;
        			}
        		}
        		
        		// check city
        		$add_city	= $address->getCity();
        		$add_city	= trim(strtolower($add_city));
        		if( $add_city	!= trim(strtolower($data['city'])))
        			return true;
        	}
        	
        		return false;
        }
        else{
        	if($addr_id != $address->getCustomerAddressId())
        		return true;
        	else
        		return false;
        }
    }
    protected function _subscribeNews(){
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('newsletter'))
        {
            $customerSession = Mage::getSingleton('customer/session');

            if($customerSession->isLoggedIn())
            	$email = $customerSession->getCustomer()->getEmail();
            else
            {
            	$bill_data = $this->getRequest()->getPost('billing');
            	$email = $bill_data['email'];
            }

            try {
                if (!$customerSession->isLoggedIn() && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1)
                    Mage::throwException(Mage::helper('supercheckout')->__('Sorry, subscription for guests is not allowed. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/')));

                $ownerId = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email)->getId();
                
                if ($ownerId !== null && $ownerId != $customerSession->getId())
                    Mage::throwException(Mage::helper('supercheckout')->__('Sorry, you are trying to subscribe email assigned to another user.'));

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
            }
            catch (Mage_Core_Exception $e) {
            }
            catch (Exception $e) {
            }
        }
    }
    protected function _filterPostData($data){
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }
    public function successAction(){
        $session = $this->getSupercheckout()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');

        // mark that order will be saved by OPC module        
        $session->setProcessedOPC('opc');
        
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }
    public function getCountriesAction(){
        $countries = Mage::getModel('directory/country_api')->items();
        $countries_array = array();
        foreach($countries as $ctr){
            $countries_array[] =array('country_id'=>$ctr['country_id'],'name'=>$ctr['name']) ;
        }
        $session_country_billing = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getCountryId();
        $defaultCountryId = isset($session_country_billing)?$session_country_billing:Mage::getStoreConfig('general/country/default');        
        $defaultRegions = $this->getRegionsAction($defaultCountryId);
        Mage::getSingleton('supercheckout/supercheckout')->setData('default_country',$defaultCountryId);
        Mage::getSingleton('supercheckout/supercheckout')->setData('default_regions',$defaultRegions);
        return $countries_array;
    }
    public function getRegionsAction($countryID = null){
        $country_id = $this->getRequest()->getParam('country_id');
        if(isset($country_id)){
            if($country_id != ""){
                $regions = Mage::getModel('directory/region_api')->items($country_id);
                echo json_encode($regions);
            }
        }elseif($countryID !=null){
            if($countryID != ""){
                $regions = Mage::getModel('directory/region_api')->items($countryID);
                return $regions;
            }
        }
        
    }
    public function getPaymentMethodFormHtml($method){
        $className = Mage::getConfig()->getBlockClassName('checkout/onepage_payment_methods');
        $block = new $className();
        $x = $block->getPaymentMethodFormHtml($method);
        echo $this->getResponse()->setBody($block->toHtml());
    }
    public function getCustomerBillingAddress(){
        $customer = Mage::getSingleton('customer/session');
        if ($customer->isLoggedIn()) {
            $options = array();
            $customeraddresss = $customer->getCustomer()->getAddresses();
            if(!empty($customeraddresss)){
                foreach ($customer->getCustomer()->getAddresses() as $address) {
                    $options[] = array(
                        'value'=>$address->getId(),
                        'label'=>$address->format('oneline')
                    );
                }
                $address_billing = $customer->getCustomer()->getPrimaryBillingAddress();


                if ($address_billing) {
                    $billaddressId = $address_billing->getId();
                } else {
                    $obj	= $customer->getBillAddress();

                    $billaddressId = $obj->getId();
                }
                $address_shipping = $customer->getCustomer()->getPrimaryShippingAddress();

                if ($address_shipping) {
                    $shipaddressId = $address_shipping->getId();
                } else {

                            $obj	= $customer->getShipAddress();
                            $shipaddressId = $obj->getId();
                }
                Mage::getSingleton('supercheckout/supercheckout')->setData('default_bill_address_id',$billaddressId);
                Mage::getSingleton('supercheckout/supercheckout')->setData('default_ship_address_id',$shipaddressId);
                return $options;
            }else{
                return '';
            }
        }
        return '';
    }
    public function getCustomerShippingAddress(){
        $customer = Mage::getSingleton('customer/session');
        if ($customer->isLoggedIn()) {
            $options = array();
            foreach ($customer->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
            }
        	
            $address = $customer->getCustomer()->getPrimaryShippingAddress();
        	
            if ($address) {
                $addressId = $address->getId();
            } else {
            	
            		$obj	= $customer->getShipAddress();
                        $addressId = $obj->getId();
            }
           
            if($addr_type == 'shipping'){
                $address_selectbox = '<select ng-model="data.shipping_address_id" ng-change="setExistingAddress()" style="width:97%;">';
                $i=0;
                foreach($options as $opt){
                    
                        if($i==0){
                            $address_selectbox  .= '<option value="">Please Select a shipping address</option>';
                            $i++;
                        }
                        $address_selectbox  .= '<option value="'.$opt['value'].'">'.$opt['label'].'</option>';
                        
                }
                $address_selectbox .= '</select>';
            }
            
            return $address_selectbox;
        }
        return '';
    }
    public function getSelectedShippingMethod(){
        $settings = Mage::helper('supercheckout')->getSettings() ;
        $shipping_method_details = $this->getShippingMethodAction();
        $shipping_method_string = json_encode($shipping_method_details);
        $shipping_method_session = $this->getSupercheckout()->getSessionMethods('shipping');

        $shipping_method_default = isset($settings['step']['shipping_method']['default_option'])?$settings['step']['shipping_method']['default_option']:"";
        if($shipping_method_session != ""){
            if(strstr($shipping_method_string, $shipping_method_session)){
                $selected_shipping_method = $shipping_method_session;
            }else{
                $selected_shipping_method = isset($shipping_method_details[0]['value'])?$shipping_method_details[0]['value']:"";
            }
        }elseif($shipping_method_default != ""){
            if(strstr($shipping_method_string, $shipping_method_default)){
                $selected_shipping_method = $shipping_method_default;
            }else{
                $selected_shipping_method = isset($shipping_method_details[0]['value'])?$shipping_method_details[0]['value']:"";
            }
        }else{
            $selected_shipping_method = ""; //you can disable shipping method div->hide here
        }
        return $selected_shipping_method;
    }
    public function updateSupercheckoutAction(){      
        
        $data = json_decode(file_get_contents("php://input"));
        $_POST['billing'] = $data->billing;
        $_POST['shipping'] = $data->shipping;
        $_POST['billing_address_id'] = $data->billing_address_id;
        $_POST['shipping_address_id'] = $data->shipping_address_id;
        $_POST['paymentAddressRadio'] = $data->paymentAddressRadio;
        $_POST['shippingAddressRadio'] = $data->shippingAddressRadio;
        $bill_data = (array)($this->getRequest()->getPost('billing', array()));
        $bill_addr_id = $this->getRequest()->getPost('billing_address_id', false);
        $ship_data = (array)($this->getRequest()->getPost('shipping', array()));
        $ship_addr_id = $this->getRequest()->getPost('shipping_address_id', false);
        $ship_updated = false;
        	$shipping_address_changed	= false;

        	// get prev shipping data.
			$prev_ship = $this->getSupercheckout()->getQuote()->getShippingAddress();
                        ### THIS IS TO FILL THE TEXTBOXES IN CASE "use_of_shipping" IS NOT USED
			$prev_same_as_bill = $prev_ship->getSameAsBilling();
                        

	        $billing_address_changed	= false;
                #### CHECKING WHETHER ADDRESS WAS CHANGED FROM WHAT IT WAS BEFORE #####
	        if($this->_checkChangedAddress($bill_data, 'Billing', $bill_addr_id, $validation_enabled)){
                    ### IF CHANGED, THEN VALIDATION OF BILLING ADDRESS WILL AGAIN TAKE PLACE #####
	        	$billing_address_changed	= true;
	        	$this->getSupercheckout()->getCheckout()->setBillingWasValidated(false);
	        }
                if($this->getRequest()->getPost('paymentAddressRadio',false) && $this->getRequest()->getPost('paymentAddressRadio') == 'existing'){
                    $result = $this->getSupercheckout()->saveBillingRefresh($bill_data,$bill_addr_id,false,true);
                }else{
                    
                    $bill_addr_id = -1;
                    $result = $this->getSupercheckout()->saveBillingRefresh($bill_data,$bill_addr_id,false,true);
                }
                if (isset($bill_data['use_for_shipping']) && $bill_data['use_for_shipping'] == true && !$this->getSupercheckout()->getQuote()->isVirtual()){
                        $result = $this->getSupercheckout()->saveShippingRefresh($bill_data,$bill_addr_id, false, true);
                }
                
                if ((!$bill_data['use_for_shipping'] || !isset($bill_data['use_for_shipping'])) && !$this->getSupercheckout()->getQuote()->isVirtual()){
                                
		        $ship_data		= $this->_filterPostData($this->getRequest()->getPost('shipping', array()));
                        
		        $ship_addr_id	= $this->getRequest()->getPost('shipping_address_id', false);

		        if (!$ship_updated){
                            
		        	if ($this->_checkChangedAddress($ship_data, 'Shipping', $ship_addr_id, $validation_enabled))
		        	{
		        		$shipping_address_changed = true;
		        		$this->getSupercheckout()->getCheckout()->setShippingWasValidated(false);
		        	}
		        	else
		        	{
		        		// check if 'use for shipping' has been changed
		        		if($prev_same_as_bill == 1)
		        		{
			        		$shipping_address_changed = true;
			        		$this->getSupercheckout()->getCheckout()->setShippingWasValidated(false);
		        		}
		        	}
		        }
		        
                    if($this->getRequest()->getPost('shippingAddressRadio',false) && $this->getRequest()->getPost('shippingAddressRadio') == 'existing'){
                        
                        $result = $this->getSupercheckout()->saveShippingRefresh($ship_data,$ship_addr_id, false, true);
                    }else{
                        
                        $ship_addr_id = -1;
                        $result = $this->getSupercheckout()->saveShippingRefresh($ship_data,$ship_addr_id, true, true);
                    }  
                if ($result)
                {
                	$result['error_messages'] = $result['message'];
                	$result['error'] = true;
                    $result['success'] = false;
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
                
            }

        
    }
    public function updateSupercheckoutController(){
        //to set default values for country, addressid, region
        $defaultCountryId = Mage::getStoreConfig('general/country/default');        
        $bill_data = array('country_id'=>$defaultCountryId,'use_for_shipping'=>true);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
             $customerData = Mage::getSingleton('customer/session')->getCustomer();
             $this->getCustomerBillingAddress();
             $default_bill_address_id = Mage::getSingleton('supercheckout/supercheckout')->getData('default_bill_address_id');
             $default_ship_address_id = Mage::getSingleton('supercheckout/supercheckout')->getData('default_ship_address_id');
              $bill_addr_id = $default_bill_address_id;//$customerData->getId();
              $ship_addr_id = $default_bill_address_id;//$customerData->getId();
         }
        $ship_data = array('country_id'=>$defaultCountryId);
        $this->getSupercheckout()->saveShippingRefresh($ship_data,$ship_addr_id, true, true);
        $this->getSupercheckout()->saveBillingRefresh($bill_data,$bill_addr_id, true, true);
            return true;
            
    }
    public function getAgreements(){
        $settings = Mage::helper('supercheckout')->getSettings();
        $string = '';
        if ((Mage::getSingleton('customer/session')->isLoggedIn() && $settings['option']['logged']['confirm']['fields']['agree']['display']) || (!Mage::getSingleton('customer/session')->isLoggedIn() && $settings['option']['guest']['confirm']['fields']['agree']['display'])) {
            if ($settings['option']['guest']['confirm']['fields']['agree']['display'])
                $agre = array();
            
                $agre = Mage::getModel('checkout/agreement')->getCollection()
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addFieldToFilter('is_active', 1);
                        
            foreach ($agre as $_a) {

                $string .= '<ol class="checkout-agreements">';
                $string .= '<li>';
                $string .= '<div id="onepagecheckout-agreement-' . $_a->getId() . '-window"';
                $string .= 'class="agreement-content"';
                $string .= 'style="height:' . $_a->getContentHeight() . ';display: none;">';
                $string .= '<div class="page-title">';
                $string .= '<span>' . $_a->getName() . '</span>';
                $string .= '</div>';
                $string .= $_a->getIsHtml() ? $_a->getContent() : nl2br($_a->getContent());
                $string .= '</div>';
                $string .= '<p class="agree">';
                $string .= '<input type="checkbox" id="agreement-' . $_a->getId() . '"  ng-model="data.agreement.' . $_a->getId() . '" title="' . $_a->getCheckboxText() . '" class="checkbox" />';

                $string .= '<label for="agreement-' . $_a->getId() . '">';
                $string .= '<a popover-trigger="click" popover-placement="top" popover="' . $_a->getContent() . '" popover-title="' . $_a->getName() . '"  class="onepagecheckout-agreement-' . $_a->getId() . '">';
                $string .= $_a->getCheckboxText();
                $string .= '</a>';
                $string .= '</label>';
                $string .= '</p>';
                $string .= '</li>';
                $string .= '</ol>';
            }            
            return $string;
        }
    }

}


