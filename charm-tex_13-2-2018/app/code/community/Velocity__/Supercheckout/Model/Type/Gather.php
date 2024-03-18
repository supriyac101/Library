<?php

class Velocity_Supercheckout_Model_Type_Gather {

    const CUSTOMER = 'customer';
    const GUEST = 'guest';
    const REGISTER = 'register';

    protected $_help_obj;
    protected $_quote_obj;
    private $_em_ex_msg = '';
    protected $_cust_sess;
    protected $_check_sess;
    protected $verification_lib = 'ups';

    public function __construct() {
        $this->_help_obj = Mage::helper('supercheckout');
        $this->_em_ex_msg = $this->_help_obj->__('This email adress is already registered. Please enter another email to register account or login using this email.');
        $this->_check_sess = Mage::getSingleton('checkout/session');
        $this->_quote_obj = $this->_check_sess->getQuote();

        $this->_cust_sess = Mage::getSingleton('customer/session');
    }

    public function getQuote() {
        return $this->_quote_obj;
    }

    public function getCustomerSession() {
        return $this->_cust_sess;
    }

    public function getCheckout() {
        return $this->_check_sess;
    }

    protected function _PaymentMethodAllowed($pmnt_method) {
        if ($pmnt_method->canUseForCountry($this->getQuote()->getBillingAddress()->getCountry())) {
            $grand_total = $this->getQuote()->getBaseGrandTotal();
            $min = $pmnt_method->getConfigData('min_order_total');
            $max = $pmnt_method->getConfigData('max_order_total');

            if ((!empty($max) && ($grand_total > $max)) || (!empty($min) && ($grand_total < $min)))
                return false;

            return true;
        } else
            return false;
    }

    public function initDefaultData() {
        $base_info = $this->_baseData();

        if (!$this->getQuote()->getBillingAddress()->getCountryId()) {
            $result = $this->saveBilling(array(
                'country_id' => $base_info['billing']['country_id'],
                'region_id' => $base_info['billing']['region_id'],
                'city' => $base_info['billing']['city'],
                'postcode' => $base_info['billing']['postcode'],
                'use_for_shipping' => $base_info['equal'],
                'register_account' => 0
                    ), false, false);
        }

        if (!$this->getQuote()->getShippingAddress()->getCountryId()) {
            if (!$base_info['equal']) {
                $result = $this->saveShipping(array(
                    'country_id' => $base_info['shipping']['country_id'],
                    'region_id' => $base_info['shipping']['region_id'],
                    'city' => $base_info['shipping']['city'],
                    'postcode' => $base_info['shipping']['postcode']
                        ), false, false);
            }
        }

        $this->getQuote()->collectTotals()->save();

        $this->usePayment();
        $this->useShipping();

        return $this;
    }

    private function _baseData() {
        $quote = $this->getQuote();
        // try to get shipping data from estimate dialog
        $ship_data = $quote->getShippingAddress()->getData();

        $init_ship_data = array(
            'country_id' => !empty($ship_data['country_id']) ? $ship_data['country_id'] : null,
            'city' => !empty($ship_data['city']) ? $ship_data['city'] : null,
            'region_id' => !empty($ship_data['region_id']) ? $ship_data['region_id'] : null,
            'postcode' => !empty($ship_data['postcode']) ? $ship_data['postcode'] : null,
        );

        $init_bill_data = $init_ship_data;

        if (!empty($init_ship_data['region_id']) || !empty($init_ship_data['postcode'])) {
            $bill = $this->getQuote()->getBillingAddress()->getData();
            if (!empty($bill['country_id'])) {
                if (empty($bill['city']) && empty($bill['region_id']) && empty($bill['postcode'])) {
                    $bill['country_id'] = $init_bill_data['country_id'];
                    $bill['city'] = $init_bill_data['city'];
                    $bill['region_id'] = $init_bill_data['region_id'];
                    $bill['postcode'] = $init_bill_data['postcode'];
                    if (!isset($bill['use_for_shipping']))
                        $bill['use_for_shipping'] = true;
                    if (!isset($bill['register_account']))
                        $bill['register_account'] = 0;

                    $res = $this->saveBilling($bill, false, false);
                }
            }
        }

        $result = array(
            'shipping' => $init_ship_data,
            'billing' => $init_bill_data,
            'equal' => true
        );

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $addresses = $customer->getAddresses();

        if (!$customer || !$addresses) {
            $result['equal'] = true;

            // skip GeoIp search if user made 'Quote' on shopping cart page 
            if (empty($result['shipping']['country_id']) && empty($result['shipping']['postcode'])) {
                if (Mage::getStoreConfig('onepagecheckout/geo_ip/country')) {
                    $geoip = geoip_open(Mage::getBaseDir('lib') . DS . 'MaxMind/GeoIP/data/' . Mage::getStoreConfig('onepagecheckout/geo_ip/country_file'), GEOIP_STANDARD);
                    $country_id = geoip_country_code_by_addr($geoip, Mage::helper('core/http')->getRemoteAddr());
                    $result['shipping']['country_id'] = $country_id;
                    $result['billing']['country_id'] = $country_id;
                    geoip_close($geoip);
                }

                if (Mage::getStoreConfig('onepagecheckout/geo_ip/city')) {
                    $geoip = geoip_open(Mage::getBaseDir('lib') . DS . 'MaxMind/GeoIP/data/' . Mage::getStoreConfig('onepagecheckout/geo_ip/city_file'), GEOIP_STANDARD);
                    $record = geoip_record_by_addr($geoip, Mage::helper('core/http')->getRemoteAddr());
                    if (!empty($record)) {
                        if (isset($record->city) && !empty($record->city)) {
                            $result['shipping']['city'] = $record->city;
                            $result['billing']['city'] = $record->city;
                        }
                        if (isset($record->postal_code) && !empty($record->postal_code)) {
                            $result['shipping']['postcode'] = $record->postal_code;
                            $result['billing']['postcode'] = $record->postal_code;
                        }
                    }
                    geoip_close($geoip);
                }
            }

            if (empty($result['shipping']['country_id'])) {
                $country_id = Mage::getStoreConfig('onepagecheckout/general/country');
                $result['shipping']['country_id'] = $country_id;
                $result['billing']['country_id'] = $country_id;
            }
        } else {
            $bill_addr = $customer->getPrimaryBillingAddress();
            if (!$bill_addr) {
                foreach ($addresses as $address) {
                    $bill_addr = $address;
                    break;
                }
            }

            $ship_addr = $customer->getPrimaryShippingAddress();
            if (!$ship_addr) {
                foreach ($addresses as $address) {
                    $ship_addr = $address;
                    break;
                }
            }

            $result['shipping']['country_id'] = $ship_addr->getCountryId();
            $result['billing']['country_id'] = $bill_addr->getCountryId();
            $eq = false;
            if ($ship_addr->getId() === $bill_addr->getId())
                $eq = true;
            $result['equal'] = $eq;
        }

        $is_ee = Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && Mage::getConfig()->getModuleConfig('Enterprise_AdminGws') && Mage::getConfig()->getModuleConfig('Enterprise_Checkout') && Mage::getConfig()->getModuleConfig('Enterprise_Customer');
        if ($is_ee) {
            Mage::getConfig()->saveConfig('onepagecheckout/general/enabled', false);
            Mage::getConfig()->reinit();
        }

        return $result;
    }

    public function initCheckout() {
        $checkout = $this->getCheckout();
        $cust_sess = $this->getCustomerSession();

        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }

        $customer = $cust_sess->getCustomer();
        if ($customer)
            $this->getQuote()->assignCustomer($customer);

        return $this;
    }

    public function reinit_data() {
        $is_ee = Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && Mage::getConfig()->getModuleConfig('Enterprise_AdminGws') && Mage::getConfig()->getModuleConfig('Enterprise_Checkout') && Mage::getConfig()->getModuleConfig('Enterprise_Customer');
        if ($is_ee) {
            Mage::getConfig()->saveConfig('onepagecheckout/general/enabled', false);
            Mage::getConfig()->reinit();

            return false;
        }

        return true;
    }

    public function usePayment($method_code = null) {
        $store = null;
        if ($this->getQuote())
            $store = $this->getQuote()->getStoreId();

        $methods = Mage::helper('payment')->getStoreMethods($store, $this->getQuote());

        $payments = array();
        foreach ($methods as $method) {
            if ($this->_PaymentMethodAllowed($method))
                $payments[] = $method;
        }

        $cp = count($payments);
        if ($cp == 0) {
            $this->getQuote()->removePayment();
        } elseif ($cp == 1) {
            $payment = $this->getQuote()->getPayment();
            $payment->setMethod($payments[0]->getCode());
            $method = $payment->getMethodInstance();
            $method->assignData(array('method' => $payments[0]->getCode()));
        } else {
            $exist = false;
            if (!$method_code) {
                if ($this->getQuote()->isVirtual())
                    $method_code = $this->getQuote()->getBillingAddress()->getPaymentMethod();
                else
                    $method_code = $this->getQuote()->getShippingAddress()->getPaymentMethod();
            }

            if ($method_code) {
                foreach ($payments as $payment) {
                    if ($method_code !== $payment->getCode())
                        continue;

                    $payment = $this->getQuote()->getPayment();
                    $payment->setMethod($method_code);
                    $method = $payment->getMethodInstance();
                    $method->assignData(array('method' => $method_code));
                    $exist = true;
                    break;
                }
            }
            if (!$method_code || !$exist) {
                $method_code = Mage::getStoreConfig('onepagecheckout/general/payment_method');
                foreach ($payments as $payment) {
                    if ($method_code !== $payment->getCode())
                        continue;

                    $payment = $this->getQuote()->getPayment();
                    $payment->setMethod($method_code);
                    $method = $payment->getMethodInstance();
                    $method->assignData(array('method' => $method_code));
                    $exist = true;
                    break;
                }
            }
            if (!$exist)
                $this->getQuote()->removePayment();
        }

        return $this;
    }

    public function useShipping($method_code = null) {
        $rates = Mage::getModel('sales/quote_address_rate')->getCollection()->setAddressFilter($this->getQuote()->getShippingAddress()->getId())->toArray();

        $cr = count($rates['items']);
        if (!$cr) {
            $this->getQuote()->getShippingAddress()->setShippingMethod(false);
        } elseif ($cr == 1) {
                if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)             // Code Added by Raghubendra Singh on 06-Jan-2015 to save shipping method only if all the products are not virtual or downloadable
                        $this->getQuote()->getShippingAddress()->setShippingMethod($rates['items'][0]['code']);
        } else {
            $exist = false;
            if (!$method_code)
                $method_code = $this->getQuote()->getShippingAddress()->getShippingMethod();

            if ($method_code) {
                foreach ($rates['items'] as $rate) {
                    if ($method_code === $rate['code']) {
                                if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)             // Code Added by Raghubendra Singh on 06-Jan-2015 to save shipping method only if all the products are not virtual or downloadable
                                        $this->getQuote()->getShippingAddress()->setShippingMethod($method_code);
                        $exist = true;
                        break;
                    }
                }
            }

            if (!$exist || !$method_code) {
                $method_code = Mage::getStoreConfig('onepagecheckout/general/shipping_method');
                foreach ($rates['items'] as $rate) {
                    if ($method_code === $rate['code']) {
                                if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)             // Code Added by Raghubendra Singh on 06-Jan-2015 to save shipping method only if all the products are not virtual or downloadable
                                        $this->getQuote()->getShippingAddress()->setShippingMethod($method_code);
                        $exist = true;
                        break;
                    }
                }
            }
            if (!$exist)
                $this->getQuote()->getShippingAddress()->setShippingMethod(false);
        }
        return $this;
    }

    public function getAddress($addr_id) {
        $address = Mage::getModel('customer/address')->load((int) $addr_id);
        $address->explodeStreetAddress();
        if ($address->getRegionId())
            $address->setRegion($address->getRegionId());
        return $address;
    }

    public function getCheckoutMethod() {
        if ($this->getCustomerSession()->isLoggedIn())
            return self::CUSTOMER;

        if (!$this->getQuote()->getCheckoutMethod()) {
            if (Mage::helper('supercheckout')->isGuestCheckoutAllowed($this->getQuote()))
                $this->getQuote()->setCheckoutMethod(self::GUEST);
            else
                $this->getQuote()->setCheckoutMethod(self::REGISTER);
        }
        return $this->getQuote()->getCheckoutMethod();
    }

    public function saveCheckoutMethod($method) {
        if (empty($method))
            return array('error' => -1, 'message' => $this->_help_obj->__('Invalid data.'));

        $this->getQuote()->setCheckoutMethod($method)->save();
        return array();
    }

    // check if user session is still valid 
    public function checkValidCheckoutMethod() {
        $ch_method = $this->getCheckoutMethod();
        if ($ch_method != self::CUSTOMER &&
                $ch_method != self::GUEST &&
                $ch_method != self::REGISTER) // if methos is not Gest, Customer, Register need to skip checkout
            return false;
        return true;
    }

    public function saveShippingMethod($ship_method) {
        if (empty($ship_method))
            return array('message' => $this->_help_obj->__('Invalid shipping method.'), 'error' => -1);

        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($ship_method);
        if (!$rate)
            return array('message' => $this->_help_obj->__('Invalid shipping method.'), 'error' => -1);
        if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)             // Code Added by Raghubendra Singh on 06-Jan-2015 to save shipping method only if all the products are not virtual or downloadable
                $this->getQuote()->getShippingAddress()->setShippingMethod($ship_method);
        $this->getQuote()->collectTotals()->save();
        return array();
    }

    public function saveBilling($data, $cust_addr_id, $validate = true, $skip_save = false) {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_help_obj->__('Invalid data.'));
        }

        $address = $this->getQuote()->getBillingAddress();

        if ((!empty($cust_addr_id) || $cust_addr_id == 0) && $cust_addr_id != -1) {
            $cust_addr = Mage::getModel('customer/address')->load($cust_addr_id);

            if ($cust_addr_id == 0) {
                return array('error' => 1, 'message' => $this->_help_obj->__('Please Select Address.'));
            } else if ($cust_addr->getId()) {
                if ($cust_addr->getCustomerId() != $this->getQuote()->getCustomerId())
                    return array('error' => 1, 'message' => $this->_help_obj->__('Customer Address is not valid.'));

                $address->importCustomerAddress($cust_addr);
            }
        }
        else {
            unset($data['address_id']);
            
            $new_data = array();
            foreach($data as $key=>$value){
                if($key == 'address_1' || $key == 'address_2'){
                    $new_data['street'][] = $value;
                }else{
                    $new_data[$key] = $value;
                }
            }
            
            //Start Code Added By Raghubendra Singh on 05-Jan-2015 to Check if DOB is in correct format
            $current_year = date('Y',time());
            if ($new_data['dob_month'] != '' && $new_data['dob_day'] != '' && $new_data['dob_year'] != '')
            {
                        if(is_numeric($new_data['dob_month']) && ($new_data['dob_month'] <= 12 && $new_data['dob_month'] >=1))
                        {
                                if(is_numeric($new_data['dob_day']) && ($new_data['dob_day'] <= 31 && $new_data['dob_day'] >=1))
                                {
                                        if(is_numeric($new_data['dob_year']) && ($new_data['dob_year'] <= $current_year && $new_data['dob_year'] >=1900))
                                                $new_data['dob'] = $new_data['dob_month'].'/'.$new_data['dob_day'].'/'.$new_data['dob_year'];
                                }        
                        }
            }
            //End Code Added By Raghubendra Singh on 05-Jan-2015 to Check if DOB is in correct format
            $address->addData($new_data);
        }
        if ($validate) {
            $val_results = $this->validateAddress($address, 'billing');
            if ($val_results !== true)
                return array('error' => 1, 'message' => $val_results);
        }
        
        //Start Code Added By Raghubendra Singh on 19-Jan-2015 to Throw exception if user is not loggedin and alerady registered radio is selected
        if (isset($data['registered_customer']) && $data['registered_customer'] == 1 && !$this->getCustomerSession()->isLoggedIn())
        {
            Mage::throwException($this->_help_obj->__('customer_login_first'));                
        }
        //End Code Added By Raghubendra Singh on 19-Jan-2015 to Throw exception if user is not loggedin and alerady registered radio is selected
        
        if (isset($data['register_account']) && $data['register_account']) {
            $this->getQuote()->setCheckoutMethod(self::REGISTER);
        } else if ($this->getCustomerSession()->isLoggedIn()) {
            $this->getQuote()->setCheckoutMethod(self::CUSTOMER);
        } else {
            $this->getQuote()->setCheckoutMethod(self::GUEST);
        }
        
        

//        if (0) {
//
//            $mage_ver = Mage::helper('supercheckout')->getMagentoVersion();
//            if ($mage_ver != '1.4.1.1' && $mage_ver != '1.4.1.0' && $mage_ver != '1.4.0.1' && $mage_ver != '1.4.0.0') {
//                if (true !== ($result = $this->_validateCustomerData($data))) {
//                    return $result;
//                }
//            }
//        }

        if ($validate) {
            if (!$this->getQuote()->getCustomerId() && (self::REGISTER == $this->getQuote()->getCheckoutMethod())) {
                if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                    return array('error' => 1, 'message' => array('already_registered' => $this->_em_ex_msg));
                }
            }
        }

        $address->implodeStreetAddress();

        if (!$this->getQuote()->isVirtual()) {

            $ufs = 0;
            if (isset($data['use_for_shipping']))
                $ufs = (int) $data['use_for_shipping'];

            switch ($ufs) {
                case 0:
                    $ship = $this->getQuote()->getShippingAddress();
                    $ship->setSameAsBilling(0);
                    break;
                case 1:
                    $bill = clone $address;
                    $bill->unsAddressId()->unsAddressType();
                    $ship = $this->getQuote()->getShippingAddress();
                    $ship_method = $ship->getShippingMethod();
                    $ship->addData($bill->getData());
                    if (Mage::getSingleton('checkout/session')->getShowShipping() == 1)             // Code Added by Raghubendra Singh on 06-Jan-2015 to save shipping method only if all the products are not virtual or downloadable
                        $ship->setSameAsBilling(1)->setShippingMethod($ship_method)->setCollectShippingRates(true);
                    break;
            }
        }

        if ($validate) {
            $result = $this->_processValidateCustomer($address);
            if ($result !== true)
                return $result;
        }

        if (!$skip_save)
            $this->getQuote()->collectTotals()->save();

        return array();
    }

    public function saveShipping($data, $cust_addr_id, $validate = true, $skip_save = false) {
        
        $data = (array) $data;
        $address = $this->getQuote()->getShippingAddress();
        if ((!empty($cust_addr_id) || $cust_addr_id == 0) && $cust_addr_id != -1) {
            $cust_addr = Mage::getModel('customer/address')->load($cust_addr_id);

            if ($cust_addr_id == 0) {
                return array('error' => 1, 'message' => $this->_help_obj->__('Please Select Address.'));
            } else if ($cust_addr->getId()) {
                if ($cust_addr->getCustomerId() != $this->getQuote()->getCustomerId())
                    return array('error' => 1, 'message' => $this->_help_obj->__('Customer Address is not valid.'));

                $address->importCustomerAddress($cust_addr);
            }
        }
        else {
            unset($data['address_id']);
            
            $new_data = array();
            foreach($data as $key=>$value){
                if($key == 'address_1' || $key == 'address_2'){
                    $new_data['street'][] = $value;
                }else{
                    $new_data[$key] = $value;
                }
            }
            $address->addData($new_data);
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if ($validate) {
            $val_result = $this->validateAddress($address, 'shipping');
            if ($val_result !== true)
                return array('error' => 1, 'message' => $val_result);
        }

        // fixed by Alex Calko for saving data to define property shipping method
        if (!$skip_save) {
            $this->getQuote()->collectTotals()->save();
        }

        return array();
    }

    public function savePayment($data) {
        if (empty($data)) {
            return array('message' => $this->_help_obj->__('Invalid data.'), 'error' => -1);
        }
        if (!$this->getQuote()->isVirtual()) {
            $this->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getQuote()->getPayment();
        $payment->importData($data);

        $this->getQuote()->save();

        return array();
    }

    protected function _validateCustomerData(array $data) {
        /** @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('checkout_register')
                ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $quote = $this->getQuote();
        if ($quote->getCustomerId()) {
            $customer = $quote->getCustomer();
            $customerForm->setEntity($customer);
            $customerData = $quote->getCustomer()->getData();
        } else {
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('supercheckout/customer');
            $customerForm->setEntity($customer);
            $customerRequest = $customerForm->prepareRequest($data);
            $customerData = $customerForm->extractData($customerRequest);
        }
        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            return array(
                'error' => -1,
                'message' => implode(', ', $customerErrors)
            );
        }

        if ($quote->getCustomerId()) {
            return true;
        }

        $customerForm->compactData(
                );

        if ($quote->getCheckoutMethod() == self::REGISTER) {
            $customer->setPassword($customerRequest->getParam('customer_password'));
            $customer->setConfirmation($customerRequest->getParam('confirm_password'));
        } else {
            // spoof customer password for guest
            $password = $customer->generatePassword();
            $customer->setPassword($password);
            $customer->setConfirmation($password);
            $customer->setGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        $result = $customer->validate();
        if (true !== $result && is_array($result)) {
            return array(
                'error' => -1,
                'message' => $result
            );
        }

        if ($quote->getCheckoutMethod() == self::REGISTER) {
            // save customer encrypted password in quote
            $quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
        }

        // copy customer/guest email to address
        $quote->getBillingAddress()->setEmail($customer->getEmail());

        // copy customer data to quote
        Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $quote);

        return true;
    }

    protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address) {
        if ($address->getGender()){
            $this->getQuote()->setCustomerGender($address->getGender());
        }      
        $dob = '';
        
        if ($address->getDob()) {
            $dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
            $this->getQuote()->setCustomerDob($dob);
        }

        //Added By Raghubendra Singh on 05-01-2015                
//        $dob['day'] = $address->getData('dob_day');
//        $dob['month'] = $address->getData('dob_month');
//        $dob['year'] = $address->getData('dob_year');
//        $dob['full'] = $dob['month'].'/'.$dob['day'].'/'.$dob['year'];
//        if ($dob['full']) {
//            $bday = Mage::app()->getLocale()->date($dob['full'], null, null, false)->toString('yyyy-MM-dd');
//            $this->getQuote()->setCustomerDob($dob);
//        }
        //Added By Raghubendra Singh on 05-01-2015
        if ($address->getTaxvat()){
            $this->getQuote()->setCustomerTaxvat($address->getTaxvat());
        }

        if ($this->getQuote()->getCheckoutMethod() == self::REGISTER) {

            $customer = Mage::getModel('supercheckout/customer');
            $this->getQuote()->setPasswordHash($customer->encryptPassword($address->getCustomerPassword()));

            $cust_data = array(
                'email' => 'email',
                'password' => 'customer_password',
                'confirmation' => 'confirm_password',
                'firstname' => 'firstname',
                'lastname' => 'lastname',
                'gender' => 'gender',
                'taxvat' => 'taxvat');                    
            foreach ($cust_data as $key => $value)
                $customer->setData($key, $address->getData($value));

        if ($dob) {
                $customer->setDob($dob);
            }   
            $val_result = $customer->validate();
            
            if ($val_result !== true && is_array($val_result)) {
                return array('message' => $val_result, 'error' => -1);
            }
        } elseif ($this->getQuote()->getCheckoutMethod() == self::GUEST) {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress')){
                return array('message' => array('invalid_email' => $this->_help_obj->__('Invalid email address "%s"', $email), 'error' => -1));
            }
        }

        return true;
    }

    public function validate() {
        $quote = $this->getQuote();
        if ($quote->getIsMultiShipping()){
            Mage::throwException($this->_help_obj->__('Invalid checkout type.'));
        }

        if (!Mage::helper('supercheckout')->isGuestCheckoutAllowed($quote) && $quote->getCheckoutMethod() == self::GUEST){
            Mage::throwException($this->_help_obj->__('invalid_checkout_guest'));
        }
    }

    public function saveOrder() {
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";

        $this->validate();

        $newCustomer = false;

        switch ($this->getCheckoutMethod()) {
            case self::GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::REGISTER:
                $this->_prepareNewCustomerQuote();
                $newCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        // mark that order will be saved by OPC module

        $service_quote = Mage::getModel('supercheckout/service_quote', $this->getQuote());

       
            $order = $service_quote->submitAll();
            $order = $service_quote->getOrder();

        if ($newCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

            $this->getCheckout()->setLastQuoteId($this->getQuote()->getId())
                    ->setLastSuccessQuoteId($this->getQuote()->getId())
                    ->clearHelperData();

        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order' => $order, 'quote' => $this->getQuote()));
            $r_url = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            if (!$r_url) {
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            
                $this->getCheckout()->setLastOrderId($order->getId())->setRedirectUrl($r_url)->setLastRealOrderId($order->getIncrementId());

                $agree = $order->getPayment()->getBillingAgreement();
                if ($agree){
                    $this->getCheckout()->setLastBillingAgreementId($agree->getId());
                }
        }

        
            $profiles = $service_quote->getRecurringPaymentProfiles();
            if ($profiles) {
                $ids = array();
                foreach ($profiles as $profile)
                    $ids[] = $profile->getId();

                $this->getCheckout()->setLastRecurringProfileIds($ids);
            }

            
                Mage::dispatchEvent(
                        'checkout_submit_all_after', array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
                );
        
         try {
            Mage::getSingleton('checkout/cart')->truncate()->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $exception) {
            Mage::getSingleton('checkout/session')->addError($exception->getMessage());
        } catch (Exception $exception) {
            Mage::getSingleton('checkout/session')->addException($exception, $this->__('Cannot update shopping cart.'));
        }

        return $this;
    }

    protected function validateOrder() {
        if ($this->getQuote()->getIsMultiShipping())
            Mage::throwException($this->_help_obj->__('Invalid checkout type.'));

        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addrVal = $this->validateAddress($address, 'shipping');
            if ($addrVal !== true){
                Mage::throwException($this->_help_obj->__('Please check shipping address.'));
            }

            $method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)){
                Mage::throwException($this->_help_obj->__('Please specify a shipping method.'));
            }
        }

        $addrVal = $this->validateAddress($this->getQuote()->getBillingAddress(), 'billing');
        if ($addrVal !== true){
            Mage::throwException($this->_help_obj->__('Please check billing address.'));
        }

        if (!($this->getQuote()->getPayment()->getMethod())){
            Mage::throwException($this->_help_obj->__('Please select a valid payment method.'));
        }
    }

    public function getLastOrderId() {
        $lo = $this->getCheckout()->getLastOrderId();
        $order_id = false;
        if ($lo) {
            $order = Mage::getModel('sales/order');
            $order->load($lo);
            $order_id = $order->getIncrementId();
        }
        return $order_id;
    }

    public function validateAddress($address, $address_type) {
        if ($address_type == 'billing') {
            $address_type_validate = 'payment_address';
        } elseif ($address_type == 'shipping') {
            $address_type_validate = 'shipping_address';
        }
        $errors = array();
        $customerHelper = Mage::helper('customer');
        $supercheckoutHelper = Mage::helper('supercheckout');
        $settings = $supercheckoutHelper->getSettings();
        $address->implodeStreetAddress();
        if ($supercheckoutHelper->loggedIn()) {
            if ($settings['step'][$address_type_validate]['fields']['firstname']['require']['login'] && !Zend_Validate::is($address->getFirstname(), 'NotEmpty')) {

                $errors[$address_type]['firstname'] = $customerHelper->__('Please enter the first name.');
            }
            if ($settings['step'][$address_type_validate]['fields']['lastname']['require']['login'] && !Zend_Validate::is($address->getLastname(), 'NotEmpty')) {
                $errors[$address_type]['lastname'] = $customerHelper->__('Please enter the last name.');
            }
            if ($address_type == 'billing') {

                if ($settings['step'][$address_type_validate]['fields']['company']['require']['login'] && !Zend_Validate::is($address->getCompany(), 'NotEmpty')) {
                    $errors[$address_type]['company'] = $customerHelper->__('Please enter the company.');
                }
                if ($settings['step'][$address_type_validate]['fields']['telephone']['require']['login'] && !Zend_Validate::is($address->getTelephone(), 'NotEmpty')) {
                    $errors[$address_type]['phone'] = $customerHelper->__('Please enter the phone number.');
                }
                if ($settings['step'][$address_type_validate]['fields']['fax']['require']['login'] && !Zend_Validate::is($address->getFax(), 'NotEmpty')) {
                    $errors[$address_type]['fax'] = $customerHelper->__('Please enter the fax.');
                }
            }
            if (($settings['step'][$address_type_validate]['fields']['zone_id']['require']['login'] && $address->getCountryModel()->getRegionCollection()->getSize() && !Zend_Validate::is($address->getRegionId(), 'NotEmpty')) || ($settings['option']['logged'][$address_type_validate]['fields']['zone_id']['require']) && !Zend_Validate::is($address->getRegion(), 'NotEmpty')) {
                $errors[$address_type]['state'] = $customerHelper->__('Please enter the state/province.');
            }
            if ($settings['step'][$address_type_validate]['fields']['address_1']['require']['login'] && !Zend_Validate::is($address->getStreet(1), 'NotEmpty')) {
                $errors[$address_type]['address_1'] = $customerHelper->__('Please enter address line 1.');
            }
            if ($settings['step'][$address_type_validate]['fields']['address_2']['require']['login'] && !Zend_Validate::is($address->getStreet(2), 'NotEmpty')) {
                $errors[$address_type]['address_2'] = $customerHelper->__('Please enter address line 2.');
            }
            if ($settings['step'][$address_type_validate]['fields']['city']['require']['login'] && !Zend_Validate::is($address->getCity(), 'NotEmpty')) {
                $errors[$address_type]['city'] = $customerHelper->__('Please enter the city.');
            }
            $_opt_zip = Mage::helper('directory')->getCountriesWithOptionalZip();
            if ($settings['step'][$address_type_validate]['fields']['postcode']['require']['login'] && !in_array($address->getCountryId(), $_opt_zip) && !Zend_Validate::is($address->getPostcode(), 'NotEmpty')) {
                $errors[$address_type]['zip'] = $customerHelper->__('Please enter the zip code.');
            }
            if ($settings['step'][$address_type_validate]['fields']['country']['require']['login'] && !Zend_Validate::is($address->getCountryId(), 'NotEmpty')) {
                $errors[$address_type]['country'] = $customerHelper->__('Please choose the country.');
            }
            if (empty($errors) || $address->getShouldIgnoreValidation()) {
                return true;
            }
        } else {
            if ($settings['step'][$address_type_validate]['fields']['firstname']['require']['guest'] && !Zend_Validate::is($address->getFirstname(), 'NotEmpty')) {
                $errors[$address_type]['firstname'] = $customerHelper->__('Please enter the first name.');
            }
            if ($settings['step'][$address_type_validate]['fields']['lastname']['require']['guest'] && !Zend_Validate::is($address->getLastname(), 'NotEmpty')) {
                $errors[$address_type]['lastname'] = $customerHelper->__('Please enter the last name.');
            }
            if ($settings['step'][$address_type_validate]['fields']['zone_id']['require']['guest'] && $address->getCountryModel()->getRegionCollection()->getSize() && !Zend_Validate::is($address->getRegionId(), 'NotEmpty')) {
                $errors[$address_type]['state'] = $customerHelper->__('Please enter the state/province.');
            }
            if ($settings['step'][$address_type_validate]['fields']['address_1']['require']['guest'] && !Zend_Validate::is($address->getStreet(1), 'NotEmpty')) {
                $errors[$address_type]['address_1'] = $customerHelper->__('Please enter address line 1.');
            }
            if ($settings['step'][$address_type_validate]['fields']['address_2']['require']['guest'] && !Zend_Validate::is($address->getStreet(2), 'NotEmpty')) {
                $errors[$address_type]['address_2'] = $customerHelper->__('Please enter address line 2.');
            }
            if ($settings['step'][$address_type_validate]['fields']['city']['require']['guest'] && !Zend_Validate::is($address->getCity(), 'NotEmpty')) {
                $errors[$address_type]['city'] = $customerHelper->__('Please enter the city.');
            }
            $_opt_zip = Mage::helper('directory')->getCountriesWithOptionalZip();
            if ($settings['step'][$address_type_validate]['fields']['postcode']['require']['guest'] && !in_array($address->getCountryId(), $_opt_zip) && !Zend_Validate::is($address->getPostcode(), 'NotEmpty')) {
                $errors[$address_type]['zip'] = $customerHelper->__('Please enter the zip code.');
            }

            if ($address_type == 'billing') {
                if ($settings['step'][$address_type_validate]['fields']['company']['require']['guest'] && !Zend_Validate::is($address->getCompany(), 'NotEmpty')) {
                    $errors[$address_type]['company'] = $customerHelper->__('Please enter the company.');
                }
                if ($settings['step'][$address_type_validate]['fields']['telephone']['require']['guest'] && !Zend_Validate::is($address->getTelephone(), 'NotEmpty')) {
                    $errors[$address_type]['phone'] = $customerHelper->__('Please enter the phone number.');
                }
                if ($settings['step'][$address_type_validate]['fields']['fax']['require']['guest'] && !Zend_Validate::is($address->getFax(), 'NotEmpty')) {
                    $errors[$address_type]['fax'] = $customerHelper->__('Please enter the fax.');
                }
            }
            if (isset($settings['step'][$address_type_validate]['fields']['country']['require']['guest']) && !Zend_Validate::is($address->getCountryId(), 'NotEmpty')) {
                $errors[$address_type]['country'] = $customerHelper->__('Please choose the country.');
            }
            if (empty($errors) || $address->getShouldIgnoreValidation()) {
                return true;
            }
        }
        return $errors;
    }

    protected function _prepareNewCustomerQuote() {
        $bill = $this->getQuote()->getBillingAddress();
        $ship = null;
        if (!$this->getQuote()->isVirtual())
            $ship = $this->getQuote()->getShippingAddress();

        $customer = $this->getQuote()->getCustomer();
        $cust_bill = $bill->exportCustomerAddress();
        $customer->addAddress($cust_bill);
        $bill->setCustomerAddress($cust_bill);
        $cust_bill->setIsDefaultBilling(true);
        if ($ship) {
            if (!$ship->getSameAsBilling()) {
                $cust_ship = $ship->exportCustomerAddress();
                $customer->addAddress($cust_ship);
                $ship->setCustomerAddress($cust_ship);
                $cust_ship->setIsDefaultShipping(true);
            } else
                $cust_bill->setIsDefaultShipping(true);
        }

        if (!$bill->getCustomerGender() && $this->getQuote()->getCustomerGender()){
            $bill->setCustomerGender($this->getQuote()->getCustomerGender());
        }

        if (!$bill->getCustomerDob() && $this->getQuote()->getCustomerDob()){
            $bill->setCustomerDob($this->getQuote()->getCustomerDob());
        }

        if (!$bill->getCustomerTaxvat() && $this->getQuote()->getCustomerTaxvat()){
            $bill->setCustomerTaxvat($this->getQuote()->getCustomerTaxvat());
        }

        Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $bill, $customer);

        $customer->setPassword($customer->decryptPassword($this->getQuote()->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $this->getQuote()->setCustomer($customer)->setCustomerId(true);
    }

    protected function _prepareCustomerQuote() {
        $bill = $this->getQuote()->getBillingAddress();
        $ship = null;
        if (!$this->getQuote()->isVirtual()){
            $ship = $this->getQuote()->getShippingAddress();
        }

        $customer = $this->getCustomerSession()->getCustomer();
        if (!$bill->getCustomerId() || $bill->getSaveInAddressBook()) {
            $cust_bill = $bill->exportCustomerAddress();
            $customer->addAddress($cust_bill);
            $bill->setCustomerAddress($cust_bill);
        }
        if ($ship && (($ship->getSaveInAddressBook() && !$ship->getSameAsBilling()) || (!$ship->getSameAsBilling() && !$ship->getCustomerId()))) {
            $cust_ship = $ship->exportCustomerAddress();
            $customer->addAddress($cust_ship);
            $ship->setCustomerAddress($cust_ship);
        }

        if (isset($cust_bill) && !$customer->getDefaultBilling()){
            $cust_bill->setIsDefaultBilling(true);
        }

        if ($ship && isset($cust_ship) && !$customer->getDefaultShipping()){
            $cust_ship->setIsDefaultShipping(true);
        }elseif (isset($cust_bill) && !$customer->getDefaultShipping()){
            $cust_bill->setIsDefaultShipping(true);
        }

        $this->getQuote()->setCustomer($customer);
    }

    protected function _prepareGuestQuote() {
        $quote = $this->getQuote();
        $quote->setCustomerId(null)->setCustomerEmail($quote->getBillingAddress()->getEmail())->setCustomerIsGuest(true)->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    protected function _involveNewCustomer() {
        $customer = $this->getQuote()->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $this->getQuote()->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(Mage::helper('supercheckout')->__('Account confirmation is required. Please, check your email for confirmation link. <a href="%s">Click here</a> to resend confirmation email.', $url));
        } else {
            $customer->sendNewAccountEmail('registered', '', $this->getQuote()->getStoreId());
            $this->getCustomerSession()->loginById($customer->getId());
        }
        return $this;
    }

    protected function _customerEmailExists($email, $web_id = null) {
        $customer = Mage::getModel('supercheckout/customer');
        if ($web_id)
            $customer->setWebsiteId($web_id);

        $customer->loadByEmail($email);
        if ($customer->getId())
            return $customer;

        return false;
    }

    public function setVerificationLib($lib) {
        $this->verification_lib = $lib;
    }

    public function getVerificationLib() {
        return $this->verification_lib;
    }

    public function validate_address($type = 'Billing', $data = false) {

        $lib = $this->getVerificationLib();
        if ($lib == 'ups')
            $results = $this->ups_validate_street_address($type, $data);
        elseif ($lib == 'usps')
            $results = $this->usps_validate_street_address($type, $data);
        else
            $results = false;

        $this->getCheckout()->{"set{$type}ValidationResults"}($results);

        return $results;
    }

    public function validateCustom() {
        $customer = Mage::getModel('supercheckout/customer');
        $errors = array();


        $password = $customer->getPassword();
        if (!$customer->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $errors['empty_password'] = Mage::helper('customer')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors['password_length'] = Mage::helper('customer')->__('The minimum password length is %s', 6);
        }
        $confirmation = $customer->getConfirmation();
        if ($password != $confirmation) {
            $errors['match password'] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    public function getSessionValueAddress($type_address) {
        $returned_address = array();
        if ($type_address == 'billing') {
            $bill_address = $this->getQuote()->getBillingAddress();
            $returned_address = array(
                'email' => $bill_address->getEmail(),
                'firstname' => $bill_address->getFirstname(),
                'lastname' => $bill_address->getLastname(),
                'street' => $bill_address->getStreet(1),
                'city' => $bill_address->getCity(),
                'country_id' => $bill_address->getCountryId(),
                'region_id' => $bill_address->getRegionId(),
                'company' => $bill_address->getCompany(),
                'telephone' => $bill_address->getTelephone(),
                'use_for_shipping' => $this->getQuote()->getShippingAddress()->getSameAsBilling() ? true : false
            );
            return ($returned_address);
        }
    }

    public function getSessionMethods($type_method) {
        $returned_method = array();
        $checkout = Mage::getSingleton('checkout/session');
        if ($type_method == 'shipping') {

            return ($checkout->getQuote()->getShippingAddress()->getShippingMethod());
        }
        if ($type_method == 'payment') {

            $returned_method = array('method' => $checkout->getQuote()->getBillingAddress()->getPaymentMethod());

            return $returned_method;
        }
    }

    public function saveShippingRefresh($data, $cust_addr_id, $validate = true, $skip_save = false) {
        $data = (array) $data;
        $address = $this->getQuote()->getShippingAddress();

        if (empty($cust_addr_id) || $cust_addr_id == -1) {
            unset($data['address_id']);
            $address->addData($data);


            $address->setSameAsBilling(0);
            $this->getQuote()->getShippingAddress()->save();
            $x = $address->getCountryId();
        } else {

            $cust_addr = Mage::getModel('customer/address')->load($cust_addr_id);
            if ($cust_addr->getId()) {

                if ($this->getQuote()->getCustomerId() != $cust_addr->getCustomerId())
                    return array('message' => $this->_help_obj->__('Customer Address is not valid.'), 'error' => 1);

                $address->importCustomerAddress($cust_addr);
                $this->getQuote()->getShippingAddress()->save();
            }
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);



        $this->getQuote()->collectTotals()->save();

        return array();
    }

    public function saveBillingRefresh($data, $cust_addr_id, $validate = true, $skip_save = false) {


        $address = $this->getQuote()->getBillingAddress();
        if (!empty($cust_addr_id) && $cust_addr_id != -1) {

            $cust_addr = Mage::getModel('customer/address')->load($cust_addr_id);
            if ($cust_addr->getId()) {
                if ($cust_addr->getCustomerId() != $this->getQuote()->getCustomerId()){
                    return array('error' => 1, 'message' => $this->_help_obj->__('Customer Address is not valid.'));
                }

                $address->importCustomerAddress($cust_addr);
                $this->getQuote()->getBillingAddress()->save();
            }
        }
        else {
            unset($data['address_id']);
            $address->addData($data);
            $x = $address->getCountryId();

            $address->setSameAsBilling(0);
            $this->getQuote()->getBillingAddress()->save();
        }



        $address->implodeStreetAddress();


        $this->getQuote()->collectTotals()->save();

        return array();
    }

}
