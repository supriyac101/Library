<?php
class Velocity_Supercheckout_Block_Supercheckoutfront extends Mage_Checkout_Block_Onepage_Abstract
{
    
     public function __construct()
    {
             
            parent::__construct();
            $this->setTemplate('supercheckout/supercheckout.phtml');
            $this->getCheckout()->setStepData('shipping_method', array(
            'label'     => Mage::helper('checkout')->__('Shipping Method'),
            'is_show'   => $this->isShow()
        ));
    }    

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
    public function getCustomerBillAddr()
    {
    	return $this->buildCustomerAddress('billing');
    }
    
    public function buildCustomerAddress($addr_type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
            }

        	switch($addr_type)
        	{
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
            	if($addr_type == 'billing')
            		$obj	= $this->getBillAddress();
            	else
            		$obj	= $this->getShipAddress();

                $addressId = $obj->getId();
            }

            $select = $this->getLayout()->createBlock('core/html_select')
            							->setId("{$addr_type}_customer_address")->setName("{$addr_type}_address_id")
            							->setValue($addressId)->setOptions($options)
										->setExtraParams('onchange="'.$addr_type.'.newAddress(!this.value)"')
										->setClass('customer_address');

            $select->addOption('', Mage::helper('onepagecheckout')->__('New Address'));            
            return $select->getHtml();
        }
        return '';
    }

    public function buildCountriesSelectBox($addr_type)
    {
		if($addr_type == 'billing')
			$obj	= $this->getBillAddress();
		else
			$obj	= $this->getShipAddress();
    	
        $countryId = $obj->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }
        $select = $this->getLayout()->createBlock('core/html_select')
        							->setId("{$addr_type}:country_id")->setName("{$addr_type}[country_id]")
									->setValue($countryId)->setOptions($this->getCountryOptions())
									->setTitle(Mage::helper('onepagecheckout')->__('Country'))
									->setClass('validate-select');

		if($addr_type == 'shipping')
			$select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');

        return $select->getHtml();        
    }
    public function isCustomerLoggedin(){
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getItems()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    }

    public function getTotals()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getTotals();
    }

}