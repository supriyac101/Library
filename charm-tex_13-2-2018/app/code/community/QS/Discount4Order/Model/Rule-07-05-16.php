<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */
class QS_Discount4Order_Model_Rule extends Mage_Core_Model_Abstract 
{
	/**
     * Main action (create rule + send email) 
     * @param Mage_Sales_Model_Order $order
     */
    public function createDiscount($order) 
	{
		if ($rule = $this->getCartRule($order)) {
			$this->sendEmail($order,$rule);
		}
	}
	
	public function getCartRule($order)
	{
        Mage::log("create", null, "d4o.log");
		$store_id = ($order?$order->getStoreId():Mage::app()->getStore()->getStoreId());
        if (!Mage::helper('discount4order')->isEnabled($store_id)) return null;
		$config = Mage::getStoreConfig('discount_for_order/discount_for_order',$store_id);
		$data = new Varien_Object($config);

        //accumaulative discount
        if ($order->getCustomerId()) {

            $rulesCumulative = unserialize($data->getCumulativeDiscount());

            if (count($rulesCumulative) > 0) {

                $customer = Mage::getModel("discount4order/customers")->load($order->getCustomerId(), "customer_id");

                $connection = Mage::getSingleton('core/resource')->getConnection('default_setup');
                $select = $connection->select()
                    ->from(array('p' => 'sales_flat_order'),
                        array('SUM(base_grand_total)', 'SUM(total_item_count)', 'COUNT(*)'))
                    ->where('customer_id =' . $order->getCustomerId());
                    //->where('status = "complete"');

                if ($config['reset_date']) {
                    $select->where('created_at > "' . $config['reset_date'] . '"');
                }

                if (count($rulesCumulative) == 1 && $customer->getId()) {
                    $select->where('entity_id > ' . $customer->getLastOrderId());
                }

                $_amount2next = $select->query()->fetchAll();

                foreach ($_amount2next as $_row) {
                    $by_payments = $_row['SUM(base_grand_total)'];
                    $by_quantity = $_row['SUM(total_item_count)'];
                    $by_orders = $_row['COUNT(*)'];
                }

                switch ($data->getPurchasesType()) {
                    case "by_payments": $_amount2next = $by_payments;
                        $_prev = $_amount2next - $order->getGrandTotal();
                        break;
                    case "by_orders":  $_amount2next = $by_orders;
                        $_prev = $_amount2next - 1;
                        break;
                    case "by_quantity": $_amount2next = $by_quantity;
                        $_prev = $_amount2next - $order->getTotalItemCount();
                        break;
                    default: $_prev = $_amount2next;
                }

                asort($rulesCumulative);

                $apply = false;

                foreach ($rulesCumulative as $rule) {
                    if ($rule['amount2next'] <= $_amount2next && $rule['amount2next'] > $_prev) {
                        $apply = true;
                        $data->setDiscountAmount($rule['newdiscount']);
                        $customer->setLastOrderId($order->getEntityId());
                    }
                }

                if (!$apply) {
                    return null;
                } else {
                    if (!$customer->getId()) {
                        $customer->setCustomerId($order->getCustomerId());
                    }
                    $customer->save();
                }
            }
        } else {
            if ($data->getCumulativeDiscount()) {
                Mage::getSingleton('core/session')->addSuccess('If you want to have accumulative discount - please <a href="'. Mage::getUrl('customer/account/create') .'" >register</a>.');
            }
        }

		//One order has one discount.
		$data->setRuleName($data->getRuleName() . ' #'.$order->getRealOrderId());
		return $this->createCartRule($data,$order);
	}
		
	/**
     * Creating Shopping Cart Price Rule and getting coupon code in the end
     * @param array $data
	 * @param Mage_Sales_Model_Order $order
	 * @return salesrule object
     */
    public function createCartRule($data, $order) {
        //Generating Rule
        $rules = Mage::getModel('salesrule/rule')->getCollection();
        $couponCodes = array();
		$ruleNames = array();
        foreach ($rules as $rule) {
            $couponCodes[] = $rule->getCouponCode();
			$ruleNames[]= $rule->getName();
        }
        $ruleModel = Mage::getModel('salesrule/rule');
		$ruleData = array();
		//One order has one discount.
		if(!in_array($data->getRuleName(), $ruleNames)) {
			$ruleData['name'] = $data->getRuleName();
		}else{
			return null;
		}
        $ruleData['name'] = $data->getRuleName();
        $ruleData['is_active'] = ($data->getIsActive()?$data->getIsActive():1);
        $ruleData['website_ids'] = explode(',',$data->getWebsiteIds());		
        $ruleData['customer_group_ids'] = ($data->getCustomerGroupIds()?$data->getCustomerGroupIds():(Mage::getModel('customer/group')->getCollection()->getAllIds()));

        if($data->getDiscountType() == 'percent_of_order')  {
			$data->setDiscountType('cart_fixed');
			$data->setDiscountAmount($order->getBaseSubtotal() * abs($data->getDiscountAmount()) / 100);
		}
		
		$ruleData['simple_action'] = $data->getDiscountType();
        
		if ($data->getCouponCode()) {
			if(!in_array($data->getCouponCode(), $couponCodes)) {
				$ruleData['coupon_code'] = $data->getCouponCode();
			}else{
				return null;
			}
		}else{
			if(count($couponCodes) >= 1) {
				while (!isset($ruleData['coupon_code'])) {
					if(!in_array($latestCode = $this->generateCouponCode($data->getDiscountLength()), $couponCodes)) {
						$ruleData['coupon_code'] = $latestCode;
					}
				}
			} else {
				$ruleData['coupon_code'] = $this->generateCouponCode($data->getDiscountLength());
			}
		}
        $ruleData['uses_per_coupon'] = $data->getUsesPerCode();
        $ruleData['uses_per_customer'] = $data->getUsesPerCustomer();

		$ruleData['from_date'] = Mage::app()->getLocale()->date(
                        new Zend_Date(strtotime('yesterday')),
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        null,
                        false
                    );
        $ruleData['to_date'] = Mage::app()->getLocale()->date(
                        new Zend_Date(strtotime(" +". $data->getDiscountPeriod() ." days")),
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        null,
                        false
                    );

        $ruleData['is_rss'] = ($data->getIsRss()?$data->getIsRss():0);
        $ruleData['discount_amount'] = ($data->getDiscountType() == 'by_percent') ? min(100,$data->getDiscountAmount()) : $data->getDiscountAmount();

        //Discount
        $ruleData['coupon_type'] = ($data->getCouponType()?$data->getCouponType():2);
		$ruleData['rule_simple_action'] = $data->getDiscountType();
        // No free shipping
        $ruleData['simple_free_shipping'] = ($data->getSimpleFreeShipping()?$data->getSimpleFreeShipping():0);
        // Stop further rule processing
        $ruleData['rule_stop_rules_processing'] = ($data->getRuleStopRulesProcessing()?$data->getRuleStopRulesProcessing():0);
        $ruleModel->loadPost($ruleData);
        $ruleModel->save();
		return $ruleModel;
    }
	
	/**
     * Generate Discount Code
     * @param int $length
     * @return string
     */
    public function generateCouponCode($length) {
        $code = "";
        // define possible characters
        $possible = "0123456789bcdfghjkmnpqrstvwxyz";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            if (!strstr($code, $char)) {
                $code .= $char;
                $i++;
            }
        }
        return $code;
    }
	
	/**
     * Send email
     * @param object $order
	 * @param object $rule
	 * @param string $customerEmail
     */
    public function sendEmail($order, $rule, $customerEmail = null) 
	{
		$storeId = $order->getStoreId();
		$locale = Mage::getStoreConfig('general/locale/code',$storeId);
		$ruleData['from_date'] = Mage::app()->getLocale()->date(
                        new Zend_Date(strtotime('yesterday')),
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        $locale,
                        false
                    );
		$store = Mage::getModel('core/store')->load($storeId);
		$websiteId = $store->getWebsiteId();
		if(!$customerEmail) {
			$customerEmail = $order->getCustomerEmail();
		}		
		if ($order->getCustomerIsGuest()) {
			$customerName = $order->getBillingAddress()->getName();
			$customer = new Varien_Object;
			$customer->setStoreId($storeId);
			$customer->setEmail($customerEmail);
			$customer->setName($customerName);		
		}else{
			$customerName = $order->getCustomerName();
			$customer = Mage::getSingleton('customer/customer')->setWebsiteId($websiteId)->loadByEmail($customerEmail);
			$customer->setStoreId($storeId);
		}
		$couponCode = $rule->getCouponCode();
        $customer->setCode($couponCode);
		$customer->setFromDate(
			Mage::app()->getLocale()->date(
				new Zend_Date(strtotime($rule->getFromDate())),
				Varien_Date::DATE_INTERNAL_FORMAT,
                $locale,
                false
            )
		);
		$customer->setToDate(
			Mage::app()->getLocale()->date(
				new Zend_Date(strtotime($rule->getToDate())),
				Varien_Date::DATE_INTERNAL_FORMAT,
                $locale,
                false
            )
		);
		$customer->setStoreName($store->getFrontendName()?$store->getFrontendName():$store->getName());
		$customer->setSupportEmail(Mage::getStoreConfig('trans_email/ident_support/email',$customer->getStoreId()));
		$customer->setSupportPhone(Mage::getStoreConfig('general/store_information/phone',$customer->getStoreId())?Mage::getStoreConfig('general/store_information/phone',$customer->getStoreId()):'');

        $discountAmount = $rule->getDiscountAmount();
        $typeOfDiscount = $rule->getRuleSimpleAction();
		if (($typeOfDiscount == 'by_percent') or ($typeOfDiscount == 'percent_of_order'))  {
			$discountAmountOutput = $discountAmount . '%';
		} else {
			$discountAmountOutput = Mage::helper('core')->currency($discountAmount);
		}
		$customer->setDiscountAmount($discountAmountOutput);

        try {
            $email = Mage::getModel('core/email_template');
            $email->sendTransactional(
                    Mage::getStoreConfig('discount_for_order/discount_for_order/email_template_final',$customer->getStoreId()),
                    Mage::getStoreConfig('discount_for_order/discount_for_order/email_identity',$customer->getStoreId()),
                    $customerEmail,
                    $customerName,
                    $customer->getData()
            );
			
            //add comment to order
			$status = $order->getStatus();            
            $comment = Mage::helper('discount4order')->__("Email with discount code '%s' has been sent.",$couponCode);
			$notify = false; //customer
            $order->addStatusToHistory($status, $comment, $notify);
            //if order_place event = bad idea to save order
			//so save for other events
			if ($order->getId()) {
				$order->save();
			}
			
			//add description to rule
			$comment = Mage::helper('discount4order')->__("Email with discount code '%s' has been sent to %s.",$couponCode, $customerEmail);
			$rule->setDescription($rule->getDescription() .' ' . $comment);
			$rule->save();
			
			
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('discount4order')->__('Discount code was successfully sent to customer with email: %s', $customerEmail));
		} catch (Mage_Core_Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addException($e,
				Mage::helper('discount4order')->__('Problem occured while sending Discount For Order.')
			);
		}
    }

}