<?php

class Velocity_Supercheckout_Block_Billship extends Mage_Checkout_Block_Onepage_Billing
{
    public function getBillAddress(){
        return $this->getQuote()->getBillingAddress();
    }
    
    public function getShipAddress(){
        return $this->getQuote()->getShippingAddress();
    }

    public function getCustomerBillAddr(){
    	return $this->buildCustomerAddress('billing');
    }
    
    public function getBillingCountriesSelectBox(){
    	return $this->buildCountriesSelectBox('billing');
    }

    public function getCustomerShipAddr(){
    	return $this->buildCustomerAddress('shipping');
    }

    public function getShippingCountriesSelectBox(){
    	return $this->buildCountriesSelectBox('shipping');
    }

    public function buildCustomerAddress($addr_type){
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
            }

            switch($addr_type){
                    case 'billing':
                            $address = $this->getCustomer()->getPrimaryBillingAddress();
                            break;
                    case 'shipping':
                            $address = $this->getCustomer()->getPrimaryShippingAddress();
                            break;
            } 

            if ($address) {
                $addressId = $address->getId();
            } else {
            	if($addr_type == 'billing'){
            		$obj	= $this->getBillAddress();
                }else{
            		$obj	= $this->getShipAddress();
                }
                $addressId = $obj->getId();
            }
            if($addr_type == 'billing'){
                $address_selectbox = '<select ng-init="data.billing_address_id = " ng-model="data.billing_address_id" ng-change="setExistingAddress()" style="width:97%;">';
                $i=0;                
                foreach($options as $opt){
                    
                        if($i==0){
                            $address_selectbox  .= '<option style="" value="">Please select a billing address</option>';
                            $i++;
                        }
                        $address_selectbox  .= '<option value="'.$opt['value'].'">'.$opt['label'].'</option>';
                        
                }
                $address_selectbox .= '</select>';
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

    public function buildCountriesSelectBox($addr_type){
        if($addr_type == 'billing'){
                $obj	= $this->getBillAddress();
        }else{
                $obj	= $this->getShipAddress();
        }
        $countryId = $obj->getCountryId();
        
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }
        $select = $this->getLayout()->createBlock('core/html_select')
        							->setId("country_id")->setName("country_id")
									->setValue($countryId)->setOptions($this->getCountryOptions())
									->setTitle(Mage::helper('onepagecheckout')->__('Country'))
									->setClass('validate-select');

        if($addr_type == 'shipping'){
                $select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();        
    }
    public function getCountriesSelectBox($addr_type){
        if($addr_type == 'billing'){
                $obj	= $this->getBillAddress();
        }else{
                $obj	= $this->getShipAddress();
        }
        $countryId = $obj->getCountryId();        
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }
        $country = '<select id="" style="width:98%;" ng-model="data.billing.country_id" ng-change="getregion()">';
        $i = 0;
        foreach($this->getCountryOptions() as $ctr){ 
            if($i==0){
                $country .= '<option value="">Please Select</option>';
                $i++;
            }
            $country .= '<option value="'.$ctr['value'].'">'.$ctr['label'].'</option>';
            
        }
        $country .= '</select>';
        
        return $country;        
    }
    public function getDefaultZone($addr_type){
        if($addr_type == 'billing'){
            $obj = $this->getBillAddress();
        }else{
            $obj = $this->getShipAddress();
        }
        $countrycode = $obj->getCountryId();
        
        if (is_null($countrycode)) {
            $countrycode = Mage::getStoreConfig('general/country/default');
        }
        $getCountryId = $this->getRequest()->getParam('country');
        if(isset($getCountryId)){
            $countrycode = $this->getRequest()->getParam('country');
        }
        if ($countrycode != '') {
            $state = "<select  style='width:98%;'><option value=''>Please Select</option>";
            $statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
            if(count($statearray)>0){
                foreach ($statearray as $_state){
                        $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
                }                
                $state .= '</select>';
            }else{
                $state = '<input type="text">';
            }
        }
        
        return $state;
    }
}
