<?php
/////////////////////////////////////////////////
//There is a field in admin's checkout setting through which loging in can be made compulsary
//    ////////////////////////////////////////
class Velocity_Supercheckout_Adminhtml_SupercheckoutController extends Mage_Adminhtml_Controller_Action {
    public function preDispatch() {
       if ($this->getRequest()->getActionName() == 'resetDefaultSettings')
           Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
       parent::preDispatch();
   }
    public function indexAction(){
        
        // getting model
        $model = Mage::getModel('supercheckout/supercheckout'); // first part getModel(1st/2nd) 1st should be tagname given in config.xml file for model
        
        /********************************* getting stores ******************************/
        if (strlen($code = Mage::app()->getRequest()->getParam('store'))) {
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } else if (strlen($code = Mage::app()->getRequest()->getParam('website'))) {
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else {
            $store_id = 0;
        }

        $store_data = array();
        if (!isset($code)) {
            $settings = Mage::getStoreConfig('checkout/supercheckout/settings', $store_id);
            $store_data['scope'] = 'default';
            $store_data['scope_id'] = 0;
        } else if (isset($website_id)) {
            $settings = Mage::app()->getWebsite($website_id)->getConfig('checkout/supercheckout/settings') ;
            $store_data['scope'] = 'websites';
            $store_data['scope_id'] = $website_id;
        } else if (isset($store_id)) {
            $settings = Mage::getStoreConfig('checkout/supercheckout/settings', $store_id);
            $store_data['scope'] = 'stores';
            $store_data['scope_id'] = $store_id;
        } else {
            $settings = Mage::getConfig('checkout/supercheckout/settings');
            $store_data['scope'] = 'default';
            $store_data['scope_id'] = 0;
        }
        Mage::getSingleton('supercheckout/supercheckout')->setData('store_data', $store_data);
        /********************************* getting stores ******************************/
        $use_settings_for_controller = array();
        $postData = Mage::app()->getRequest()->getPost(); // getting posted data
        
        $browser = ($_SERVER['HTTP_USER_AGENT']);        
        if (preg_match('/(?i)msie [1-8]/', $browser)) {
            Mage::getSingleton('supercheckout/supercheckout')->setData('browser_used_ie',true);
        } else {
            Mage::getSingleton('supercheckout/supercheckout')->setData('browser_used_ie',false);
        }
        if(Mage::getSingleton('adminhtml/session')->getData('success')){
            Mage::getSingleton('supercheckout/supercheckout')->setData('success_msg', Mage::getSingleton('adminhtml/session')->getData('success'));
        }else{            
            Mage::getSingleton('supercheckout/supercheckout')->setData('success_msg', '');
        }
        
        if(!empty($postData)){ 
            try {
                
                Mage::app()->getCacheInstance()->cleanType('config');
                
                
                $flagimage = false;
                if(isset($postData['supercheckout']['shopping_cart']['image'])){
                    foreach ($postData['supercheckout']['shopping_cart']['image'] as $key => $val) {
                        if (trim($val) == '') { // If no image is selected.
                            $flagimage = true;
                            break;
                        }
                    }
                }
                if ($flagimage) {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Warning! Please select banner image.'));
                    $this->_redirectReferer();
                } else {
                    if(isset($_FILES['banner'])){
                        if (count($_FILES['banner']['name']) > 0) {
                            for ($i = 0; $i < count($_FILES['banner']['name']); $i++) {
                                $name = trim($_FILES['banner']['name'][$i]);
                                if (!empty($name)) {
                                    if ($_FILES['banner']['size'][$i] == 0) {
                                        Mage::getSingleton('adminhtml/session')->addError('Banner-' . $i . ' (' . $name . ') image size is zero. Please choose different file to upload.');
                                    } else if (!$_FILES['banner']['type'][$i] == "image/gif" && $_FILES['banner']['type'][$i] != "image/jpg" && $_FILES['banner']['type'][$i] != "image/jpeg" && $_FILES['banner']['type'][$i] != "image/pjpeg" && $_FILES['banner']['type'][$i] != "image/png") {
                                        Mage::getSingleton('adminhtml/session')->addError('Please upload only jpg/gif/png file.');
                                    } else {
                                        $resultupload = move_uploaded_file($_FILES['banner']['tmp_name'][$i], Mage::getBaseDir('media') . '/supercheckout/shopping_cart/banners/' . $name);
                                        if ($resultupload) {
                                            // return urls of image
                                        } else {
                                            Mage::getSingleton('adminhtml/session')->addError('Problem with uploading banner-' . $i . ' (' . $name . ') image. Please try again or choose different file to upload.');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $serialized_settings= serialize($postData); //serialising the values
                $configModel = Mage::getModel('core/config'); // loading config model
                $configModel->saveConfig('checkout/supercheckout/settings',$serialized_settings, $postData['scope'], $postData['scope_id']); //saving the value in datbase
                $configModel->saveConfig('checkout/supercheckout/enabled',$postData['supercheckout']['general']['enable'], $postData['scope'], $postData['scope_id']);
                $configModel->saveConfig('checkout/shoppingcart/enabled',$postData['supercheckout']['shopping_cart']['enable'], $postData['scope'], $postData['scope_id']);
                
                
                Mage::getSingleton('adminhtml/session')->setData('success','Successfully saved!');
                $this->_redirect('*/*/');
            }catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setdemoData($this->getRequest()->getPost());
                $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }else{
            Mage::getSingleton('adminhtml/session')->setData('success','');
        }
        //////////////////////////////////// Setting all default setting ///////////////////////////////////////////
        $check_settings= unserialize($settings);
        if(!isset($check_settings['supercheckout'])){
            $default_settings=$this->getDefaultSettings();
            Mage::getSingleton('supercheckout/supercheckout')->setData('settings', $default_settings['default_supercheckout']);
            $use_settings_for_controller = $default_settings['default_supercheckout'];
        }else{
            
            $unserialized_settings= unserialize($settings);
            Mage::getSingleton('supercheckout/supercheckout')->setData('settings', $unserialized_settings['supercheckout']);
            $use_settings_for_controller = $unserialized_settings['supercheckout'];
        }
        //////////////////////////////////// Setting all default setting ///////////////////////////////////////////
        //////////////////////////////////// getting value from cookie //////////////////////////////////////////
        $rightMenu = Mage::getModel('core/cookie')->get('rightMenu');
        if(isset($rightMenu) && $rightMenu != ""){
            Mage::getSingleton('core/session')->setData('rightMenu',$rightMenu);
        }
        //////////////////////////////////// getting value from cookie //////////////////////////////////////////
                
//        -------------------------------------------------------------------------------------------               //
        //////////////////////////////////// Getting all default setting ///////////////////////////////////////////
        $get_data_layout =  Mage::app()->getRequest()->getParam('layout');
        if(!empty($get_data_layout)){
            Mage::getSingleton('supercheckout/supercheckout')->setData('layout', $get_data_layout);
        }else{
            if(isset($use_settings_for_controller) && !empty($use_settings_for_controller)){
                Mage::getSingleton('supercheckout/supercheckout')->setData('layout', $use_settings_for_controller['general']['layout']);
            }else{
                Mage::getSingleton('supercheckout/supercheckout')->setData('layout', '3-Column');
            }
        }
        $magentoCurrentUrl = Mage::helper('core/url')->getCurrentUrl();
        
        $guest_checkout_enable = Mage::getStoreConfig('checkout/options/guest_checkout');
        Mage::getSingleton('supercheckout/supercheckout')->setData('guest_checkout_enable',$guest_checkout_enable);
        $agreements_enable = Mage::getStoreConfig('checkout/options/enable_agreements');
        Mage::getSingleton('supercheckout/supercheckout')->setData('agreements_enable',$agreements_enable);
        
       /************************************************** START - getting all active payment methods ********************************************/
       $active_payments_methods = Mage::getSingleton('payment/config')->getActiveMethods();
       $payment_methods = array();
       foreach ($active_payments_methods as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $payment_methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        Mage::getSingleton('supercheckout/supercheckout')->setData('payment_methods',$payment_methods);
        /************************************************** END - getting all active payment methods ********************************************/
        /************************************************** START - getting all active shipping methods ********************************************/
       $active_shipping_methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $shipping_methods = array();
        foreach($active_shipping_methods as $_ccode => $_carrier)
        {
            $_methodOptions = array();
            if($_methods = $_carrier->getAllowedMethods())
            {
                foreach($_methods as $_mcode => $_method)
                {
                    $_code = $_ccode . '_' . $_mcode;
//                    $_methodOptions[] = array('value' => $_code, 'label' => $_method);
                }

                if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $shipping_methods[$_ccode] = array('value' => $_code, 'label' => $_title);
            }
        }


        Mage::getSingleton('supercheckout/supercheckout')->setData('shipping_methods',$shipping_methods);
        /************************************************** END - getting all active shipping methods ********************************************/
        
        ///////////////////////////////////// Getting all default setting ////////////////////////////////////////////
        $this->loadLayout(); // or we can write $this->loadLayout(); and then in next line $this->renderLayout();
        
        $this->getLayout()->getBlock('supercheckout')->setFormAction( Mage::getUrl('*/*/index') );  
        $this->getLayout()->getBlock('head')->setTitle($this->__('SuperCheckout'));
        $this->renderLayout();
        
//        Mage::getSingleton('adminhtml/session')->setData('success','');
        
    }
    public function resetDefaultSettingsAction(){
        $default_settings = $this->getDefaultSettings();
//        var_dump($default_settings);die;
        $serialized_settings['supercheckout']= serialize($default_settings['default_supercheckout']); //serialising the values
        $configModel = Mage::getModel('core/config'); // loading config model
        $configModel->saveConfig('checkout/supercheckout/settings',$serialized_settings, 'default', 0); //saving the value in datbase
        $configModel->saveConfig('checkout/supercheckout/enabled',0,'default', 0);
        $configModel->saveConfig('checkout/shoppingcart/enabled',0, 'default', 0);


        Mage::getSingleton('adminhtml/session')->setData('success','Successfully restored!');
        
        
    }
    // STORE DEFAULT SETTINGS
    public function getDefaultSettings(){
        return array('default_supercheckout' => array('general' => array(
                                'enable' => 0,
                                'guestenable' => 0,
                                'guest_manual'=> 0,
                                'layout' => '3-Column',
                                'main_checkout' => 1,
                                'column_width' => array(
                                        'one-column'=>array(
                                                1 => '100',2=>'0',3=>'0','inside'=>array(1 => '0', 2 => '0')),
                                        'three-column'=>array(
                                                1 => '30', 2 => '25', 3 => '45','inside'=>array(1 => '0', 2 => '0')),
                                        'two-column'=>array(1 => '30', 2 => '70',3=>'0','inside'=>array(1 => '50', 2 => '50'))
                                    ),
                                                        
                                                        
                                'default_option' => 'guest',
                                'custom_style' => '',
                                'store_id' => 0,
                                'settings' => array('value' => 0, 'bulk' => '')
                        ),
                        'step' => array(
                                'login' => array(
                                        'sort_order' => 1,
                                        'three-column'=>array('column' => 1, 'row' => 0,'column-inside' => 0),
                                        'two-column'=>array('column' => 1, 'row' => 0,'column-inside' => 1),
                                        'one-column'=>array('column' => 0, 'row' => 0,'column-inside' => 0),
                                        'width' => '50',
                                        'option' => array(
                                                'guest' => array('title' => 'supercheckout_text_guest',
                                                        'description' => 'step_option_guest_desciption',
                                                        'display' => 1
                                                ),
                                                'login' => array('display' => 1
                                                )
                                        ),
                                    'enable_slider'=>0
                                ),
                                'payment_address' => array(
                                        'sort_order' => '2',
                                        'three-column'=>array('column' => 1, 'row' => 1,'column-inside' => 0),
                                        'two-column'=>array('column' => 1, 'row' => 1,'column-inside' => 1),
                                        'one-column'=>array('column' => 0,'row' => 1,'column-inside' => 0),
                                        'width' => '50',
                                        'fields' => array(
                                                'firstname' => array(
                                                        'id' => 'firstname',
                                                        'title' => 'First Name:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 1,
                                                        'class' => ''
                                                ),
                                                'lastname' => array(
                                                        'id' => 'lastname',
                                                        'title' => 'Last Name:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 2,
                                                        'class' => ''
                                                ),
                                                'telephone' => array(
                                                        'id' => 'telephone',
                                                        'title' => 'Telephone:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 3,
                                                        'class' => ''
                                                ),
                                                'fax' => array(
                                                        'id' => 'fax',
                                                        'title' => 'Fax:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 4,
                                                        'class' => ''
                                                ),
                                                'company' => array(
                                                        'id' => 'company',
                                                        'title' => 'Company:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 5,
                                                        'class' => ''
                                                ),
                                                'address_1' => array(
                                                        'id' => 'address_1',
                                                        'title' => 'Address Line 1:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 6,
                                                        'class' => ''
                                                ),
                                                'address_2' => array(
                                                        'id' => 'address_2',
                                                        'title' => 'Address Line 2:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 7,
                                                        'class' => ''
                                                ),
                                                'city' => array(
                                                        'id' => 'city',
                                                        'title' => 'City:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 8,
                                                        'class' => ''
                                                ),
                                                'postcode' => array(
                                                        'id' => 'postcode',
                                                        'title' => 'Postcode:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 9,
                                                        'class' => ''
                                                ),
                                                'country_id' => array(
                                                        'id' => 'country_id',
                                                        'title' => 'Country:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 10,
                                                        'class' => ''
                                                ),
                                                'zone_id' => array(
                                                        'id' => 'zone_id',
                                                        'title' => 'State/Province',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 11,
                                                        'class' => ''
                                                ),
                                                
                                        )
                                ),
                                'shipping_address' => array(
                                        'sort_order' => '3',
                                        'three-column'=>array('column' => 1, 'row' => 2,'column-inside' => 0),
                                        'two-column'=>array('column' => 1, 'row' => 2,'column-inside' => 1),
                                        'one-column'=>array('column' => 0,'row' => 2,'column-inside' => 0),
                                        'width' => '30',
                                        'fields' => array(
                                                'firstname' => array(
                                                        'id' => 'firstname',
                                                        'title' => 'First Name:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 1,
                                                        'class' => ''
                                                ),
                                                'lastname' => array(
                                                        'id' => 'lastname',
                                                        'title' => 'Last Name:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 2,
                                                        'class' => ''
                                                ),
                                                'address_1' => array(
                                                        'id' => 'address_1',
                                                        'title' => 'Address Line 1:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 3,
                                                        'class' => ''
                                                ),
                                                'address_2' => array(
                                                        'id' => 'address_2',
                                                        'title' => 'Address Line 2:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 4,
                                                        'class' => ''
                                                ),
                                                'city' => array(
                                                        'id' => 'city',
                                                        'title' => 'City:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 5,
                                                        'class' => ''
                                                ),
                                                'postcode' => array(
                                                        'id' => 'postcode',
                                                        'title' => 'Postcode:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 6,
                                                        'class' => ''
                                                ),
                                                'country_id' => array(
                                                        'id' => 'country_id',
                                                        'title' => 'Country:',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 7,
                                                        'class' => ''
                                                ),
                                                'zone_id' => array(
                                                        'id' => 'zone_id',
                                                        'title' => 'State/Province',
                                                        'custom' => 0,
                                                        'display' => array('login'=>1,'guest'=>1),
                                                        'require' => array('login'=>1,'guest'=>1),
                                                        'sort_order' => 8,
                                                        'class' => ''
                                                )
                                        )
                                ),
                                'shipping_method' => array(
                                        'sort_order' => 4,
                                        'three-column'=>array('column' => 2, 'row' => 0,'column-inside' => 0),
                                        'two-column'=>array('column' => 1, 'row' => 0,'column-inside' => 3),
                                        'one-column'=>array('column' => 0,'row' => 3,'column-inside' => 0),
                                        'display' => 1,
                                        'display_title' => 1,
                                        'display_options' => 1,
                                        'width' => '30'
                                ),
                                'payment_method' => array(
                                        'sort_order' => 5,
                                        'three-column'=>array('column' => 2, 'row' => 1,'column-inside' => 0),
                                        'two-column'=>array('column' => 2, 'row' => 0,'column-inside' => 5),
                                        'one-column'=>array('column' => 0,'row' => 4,'column-inside' => 0),
                                        'display' => 1,
                                        'display_options' => 1,
                                        'width' => '30'
                                ),
                                'cart' => array(
                                        'sort_order' => 6,
                                        'three-column'=>array('column' => 3, 'row' => 0,'column-inside' => 0),
                                        'two-column'=>array('column' => 2, 'row' => 0,'column-inside' => 2),
                                        'one-column'=>array('column' => 0,'row' => 5,'column-inside' => 0),
                                        'image_width' => 230,
                                        'image_height' => 230,
                                        'width' => '50',
                                        'option' => array(
                                                'voucher' => array(
                                                        'id' => 'voucher',
                                                        'title' => array(1 => 'voucher'),
                                                        'tooltip' => array(1 => 'voucher'),
                                                        'type' => 'text',
                                                        'refresh' => '3',
                                                        'custom' => 0,
                                                        'class' => ''
                                                ),
                                                'coupon' => array(
                                                        'id' => 'coupon',
                                                        'title' => array(1 => 'coupon'),
                                                        'tooltip' => array(1 => 'coupon'),
                                                        'type' => 'text',
                                                        'refresh' => '3',
                                                        'custom' => 0,
                                                        'class' => ''
                                                )
                                        ),
                                ),
                                'confirm' => array(
                                        'sort_order' => 7,
                                        'three-column'=>array('column' => 3, 'row' => 1,'column-inside' => 0),
                                        'two-column'=>array('column' => 2, 'row' => 1,'column-inside' => 4),
                                        'one-column'=>array('column' => 0,'row' => 6,'column-inside' => 0),
                                        'width' => '50',
                                        'fields' => array( 
                                                'comment' => array(
                                                        'id' => 'comment',
                                                        'title' => 'Order Comment',
                                                        'custom' => 0,
                                                        'class' => ''
                                                ),
                                                'agree' => array(
                                                        'id' => 'agree',
                                                        'title' => 'I agree to the conditions',
                                                        'value' => 0,
                                                        'custom' => 0,
                                                        'class' => ''
                                                )
                                        )
                                ),                                
                                'html'=>array(
                                    '0_0'=>array(
                                        'sort_order' => 8,
                                        'three-column'=>array('column' => 3, 'row' => 4,'column-inside' => 1),
                                        'two-column'=>array('column' => 2, 'row' => 1,'column-inside' => 4),
                                        'one-column'=>array('column' => 0,'row' => 7,'column-inside' => 1),
                                        'value'=>""
                                        )                                    
                                ),
                                'modal_value'=>1
                        ),
                        'option' => array(
                                'guest' => array(
                                        'display' => 1,
                                        'login' => array(),
                                        'payment_address' => array(
                                                'title' => 'supercheckout_text_your_details',
                                                'description' => 'option_guest_payment_address_description',
                                                'display' => 1,
                                                'fields' => array(
                                                        'firstname' => array('display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'lastname' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'telephone' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'company' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'company_id' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'customer_group_id' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'tax_id' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'address_1' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'address_2' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'city' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'postcode' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'country_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'zone_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
//                                                        'shipping' => array(
//                                                                'display' => 1,
//                                                                'value' => '0'
//                                                        )
                                                )
                                        ),
                                        'shipping_address' => array( 
                                                'display' => 1,
                                                'title' => 'option_guest_shipping_address_title',
                                                'description' => 'option_guest_shipping_address_description',
                                                'fields' => array(
                                                        'firstname' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'lastname' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'company' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'address_1' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'address_2' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'city' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'postcode' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'country_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'zone_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        )
                                                )
                                        ),
                                        'shipping_method' => array(
                                                'title' => 'option_guest_shipping_method_title',
                                                'description' => 'supercheckout_text_shipping_method',
                                        ),
                                        'payment_method' => array(
                                                'title' => 'option_guest_payment_method_title',
                                                'description' => 'supercheckout_text_payment_method',
                                        ),
                                        'cart' => array(
                                                'display' => 1,
                                                'option' => array(
                                                        'voucher' => array(
                                                                'display' => 1
                                                        ),
                                                        'coupon' => array(
                                                                'display' => 1
                                                        ),
                                                        'reward' => array(
                                                                'display' => 1
                                                        )
                                                ),
                                                'columns' => array(
                                                        'image' => 1,
                                                        'name' => 1,
                                                        'model' => 1,
                                                        'quantity' => 1,
                                                        'price' => 1,
                                                        'total' => 1,
                                                        'coupon'=> 1,
                                                )
                                        ),
                                        'confirm' => array(
                                                'display' => 1,
                                                'fields' => array(
                                                        'comment' => array(
                                                                'display' => 1
                                                        ),
                                                        'agree' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        )
                                                )
                                        )
                                ),
                                'logged' => array(
                                        'login' => array(),
                                        'payment_address' => array(
                                                'display' => 1,
                                                'title' => 'option_logged_payment_address_title',
                                                'description' => 'option_logged_payment_address_description',
                                                'fields' => array(
                                                        'firstname' => array('display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'lastname' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'telephone' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'company' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'company_id' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'customer_group_id' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'tax_id' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'address_1' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'address_2' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'city' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'postcode' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'country_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'zone_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'address_id' => array()
                                                )
                                        ),
                                        'shipping_address' => array(
                                                'display' => 1,
                                                'title' => 'option_logged_shipping_address_title',
                                                'description' => 'option_logged_shipping_address_description',
                                                'fields' => array(
                                                        'firstname' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'lastname' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'company' => array(
                                                                'display' => 1,
                                                                'require' => 0
                                                        ),
                                                        'address_1' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'address_2' => array(
                                                                'display' => 0,
                                                                'require' => 0
                                                        ),
                                                        'city' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'postcode' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'country_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        ),
                                                        'zone_id' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        )
                                                )
                                        ),
                                        'shipping_method' => array(
                                                'title' => 'option_logged_shipping_method_title',
                                                'description' => 'supercheckout_text_shipping_method',
                                        ),
                                        'payment_method' => array(
                                                'title' => 'option_logged_payment_method_title',
                                                'description' => 'supercheckout_text_payment_method',
                                        ),
                                        'cart' => array(
                                                'display' => 1,
                                                'option' => array(
                                                        'voucher' => array(
                                                                'display' => 1
                                                        ),
                                                        'coupon' => array(
                                                                'display' => 1
                                                        ),
                                                        'reward' => array(
                                                                'display' => 1
                                                        )
                                                ),
                                                'columns' => array(
                                                        'image' => 1,
                                                        'name' => 1,
                                                        'model' => 1,
                                                        'quantity' => 1,
                                                        'price' => 1,
                                                        'total' => 1,
                                                        'coupon' => 1,
                                                )
                                        ),
                                        'confirm' => array(
                                                'display' => 1,
                                                'fields' => array(
                                                        'comment' => array(
                                                                'display' => 1
                                                        ),
                                                        'agree' => array(
                                                                'display' => 1,
                                                                'require' => 1
                                                        )
                                                )
                                        )
                                )
                        )
        ));
    }
}