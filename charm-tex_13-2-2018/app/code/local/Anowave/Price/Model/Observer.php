<?php
/**
 * Anowave Magento Price Per Customer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Price
 * @copyright 	Copyright (c) 2016 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
class Anowave_Price_Model_Observer
{
	const PRICE_FIXED 			= 1;
	const PRICE_DISCOUNT 		= 2;
	const PRICE_FIXED_DISCOUNT 	= 3;
	
	/**
	 * @var Mage_Sales_Model_Quote
	 */
	private $quote = null;
	
	/**
	 * Load quote
	 *
	 * @var Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		if (!$this->quote)
		{
			$this->quote = Mage::getModel('sales/quote')->load
			(
				Mage::getSingleton('checkout/session')->getQuoteId()
			);
		}
		
		return $this->quote;
	}
	/**
	 * Get price 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function getPrice(Varien_Event_Observer $observer)
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn() && !Mage::app()->getStore()->isAdmin())
		{
			@$product = &$observer->getEvent()->getProduct();
			
			$quantity = null;
					
			foreach ($this->getQuote()->getAllItems() as $item) 
			{
				if ($item->getProductId() == $product->getId()) 
				{
					$quantity = (int) $item->getQty();
					
					break;
				}
			}
			
			$final = $this->getFinalPrice($product, Mage::getSingleton('customer/session')->getCustomer(), $quantity);
			
			/* Store final price for future reference */
			$product->setFinalPrice($final)->setFinalProcessedPrice($final)->setProcessed(true);
			
			return $this;
		}
		else if (Mage::app()->getStore()->isAdmin() && Mage::getSingleton('adminhtml/session_quote')->getCustomerId())
		{
			$session 	= Mage::getSingleton('customer/session');
			$product 	= &$observer->getProduct();
			$customer 	= Mage::getModel('customer/customer')->load
			(
				Mage::getSingleton('adminhtml/session_quote')->getCustomerId()
			);
			
			if (false)
			{
				$session->setCustomerAsLoggedIn($customer);
			}
			
			/* Store final price for future reference */
			
			if ($observer->getQuoteItem() && $observer->getQuoteItem()->getQty())
			{
				$quantity = (int) $observer->getQuoteItem()->getQty();
			}
			else
			{
				$quantity = null;
			}
			
			$product->setPrice
			(
				$this->getFinalPrice($product, $customer, $quantity)
			);
			
			return $this;
		}
	}
	
	/**
	 * Get admin price 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function getPriceAdmin(Varien_Event_Observer $observer)
	{
		if (Mage::app()->getStore()->isAdmin() && Mage::getSingleton('adminhtml/session_quote')->getCustomerId()) /* Apply only within admin */
		{
			$session 	= Mage::getSingleton('customer/session');
			$product 	= &$observer->getProduct();
			$customer 	= Mage::getModel('customer/customer')->load
			(
				Mage::getSingleton('adminhtml/session_quote')->getCustomerId()
			);
			
			if (false)
			{
				$session->setCustomerAsLoggedIn($customer);
			}
			
			/* Store final price for future reference */
			
			if ($observer->getQuoteItem() && $observer->getQuoteItem()->getQty())
			{
				$quantity = (int) $observer->getQuoteItem()->getQty();
			}
			else 
			{
				$quantity = null;
			}
			
			$product->setPrice
			(
				$this->getFinalPrice($product, $customer, $quantity)
			);
			
			return $this;
		}

		return $this;
	}

	/**
	 * Get final price 
	 * 
	 * @param Mage_Catalog_Model_Product $product
	 * @param Mage_Customer_Model_Customer $customer
	 * @param string $quantity
	 */
	public function getFinalPrice(Mage_Catalog_Model_Product $product, Mage_Customer_Model_Customer $customer = null, $quantity = null)
	{		
		if (!$customer || !Mage::helper('price')->legit())
		{
			return $this->getDefaultPrice($product, $quantity);
		}
		
		if (Mage::getStoreConfig('price/settings/custom'))
		{
			$price = $this->getCustomerPrice($product, $customer, $quantity);
				
			/**
			 * Get price after all rule(s)
			 * 
			 * @var float
			 */
			$original = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product->setStoreId(Mage::app()->getStore()->getStoreId())->setCustomerGroupId($customer->getGroupId()), (float) $product->getPriceModel()->getBasePrice($product));

			/**
			 * Set original price
			 */
			if (!$product->getOriginalPrice())
			{
				if ($original)
				{
					$product->setOriginalPrice
					(
						$original
					);
				}
				else 
				{
					$product->setOriginalPrice
					(
						$product->getFinalPrice($quantity)
					);
				}
			}

			if ($price)
			{
				switch ($price['price_type'])
				{
					case self::PRICE_FIXED:
						
						if (1 == (int) $price['price_apply_further'])
						{
							$customer_price = (float) $price['price'];
							
							$discount = $price['price_discount'];
							$discount = $discount < 0 ? 0 : ($discount > 100 ? 100 : $discount);
							
							/**
							 * Calculate discount 
							 * 
							 * @var float
							 */
							$discount = ($customer_price * $discount)/100;
							
		
							/**
							 * Save discount
							 */
							$this->setQuoteItemCustomerDiscount($product, $discount);
						
							return $customer_price - $discount;
						}
						else 
						{
							/**
							 * Save discount
							 */
							$product->setProductCustomerDiscount((float) $price['price']);
							
							return (float) $price['price'];
						}
						
						break;
					case self::PRICE_DISCOUNT:
						
						$discount = $price['price_discount'];
						$discount = $discount < 0 ? 0 : ($discount > 100 ? 100 : $discount);
						
						/**
						 * Calculate discount 
						 * 
						 * @var (float)
						 */
						$discount = ($product->getOriginalPrice() * $discount)/100;

						/**
						 * Save discount
						 */
						$this->setQuoteItemCustomerDiscount($product, $discount);

						return $product->getOriginalPrice() - $discount;
					
						break;
				}
			}
			else 
			{
				/* Try to apply global discount */
				$customer = Mage::getModel('price/price_global')->loadCustomer
				(
					$customer->getId()
				);
				
				$discount = (double) $customer->getPriceGlobalDiscount();
				
				if ($discount > 0)
				{
					/* Check whether date range is specified */
					$date = new DateTime();

					if ($customer->getPriceGlobalValidFrom()  && $customer->getPriceGlobalValidTo())
					{
						if ($date < new DateTime($customer->getPriceGlobalValidFrom()) || $date > new DateTime($customer->getPriceGlobalValidTo()))
						{
							return $this->getDefaultPrice($product, $quantity);
						}
					}
					elseif ($customer->getPriceGlobalValidFrom())
					{
						if ($date <= new DateTime($customer->getPriceGlobalValidFrom()))
						{
							return $this->getDefaultPrice($product, $quantity);
						}
					}
					elseif ($customer->getPriceGlobalValidTo())
					{
						if ($date >= new DateTime($customer->getPriceGlobalValidTo()))
						{
							return $this->getDefaultPrice($product, $quantity);
						}
					}
					
					$discount = ($product->getOriginalPrice() * $discount)/100;
					
					/**
					 * Save discount
					 */
					$this->setQuoteItemCustomerDiscount($product, $discount);
					
					/**
					 * Calculate price 
					 * 
					 * @var string
					 */
					$price = $product->getOriginalPrice() - $discount;
						
					/**
					 * Take care for precision
					 */
					return round($price,2, PHP_ROUND_HALF_DOWN);
				}
			}
		}

		return $this->getDefaultPrice($product, $quantity);
	}
	
	/**
	 * Get customer price 
	 * 
	 * @param Mage_Catalog_Model_Product $product
	 * @param Mage_Customer_Model_Customer $customer
	 * @param string $quantity
	 * @param string $exact
	 */
	public function getCustomerPrice(Mage_Catalog_Model_Product $product, Mage_Customer_Model_Customer $customer, $quantity = null, $exact = true)
	{
		$collection = Mage::getModel('price/price')->getCollection()->addFieldToSelect('*')->addFieldToFilter('price_customer_id',  (int) $customer->getId())->addFieldToFilter('price_product_id',  (int) $product->getId());

		Mage::dispatchEvent('anowave_price_collection_get', array
		(
			'collection' => $collection
		));
		
		$prices = array();
		
		foreach ($collection as $price)
		{
			$prices[$price->getPriceId()] = $price->getData();
		}
		
		$model = new Varien_Object(array
		(
			'prices' 	=> $prices,
			'product' 	=> $product
		));

		/* Allow other extensions to modify prices */
		Mage::dispatchEvent('anowave_price_list_prepare', array
		(
			'model' 	=> $model,
			'quantity' 	=> $quantity
		));
		
		$this->getCategoryPrice($model, $product, $customer, $quantity);
		
		return ($exact) ? $this->exactPrice($model, $quantity) : $this->arrayPrice($model);
	}
	
	/**
	 * Get category price 
	 * 
	 * @param Varien_Object $model
	 * @param Mage_Catalog_Model_Product $product
	 * @param Mage_Customer_Model_Customer $customer
	 * @param string $quantity
	 * @param string $exact
	 */
	private function getCategoryPrice(Varien_Object $model, Mage_Catalog_Model_Product $product, Mage_Customer_Model_Customer $customer, $quantity = null, $exact = true)
	{
		/* Check if category discount exists */
		$collection = Mage::getModel('price/price')->getCollection()->addFieldToSelect('*')->addFieldToFilter('price_customer_id',  (int) $customer->getId());
			
		$collection->getSelect()->joinLeft
		(
			array(Mage::getSingleton('core/resource')->getTableName('anowave_customerprice_category') => Mage::getSingleton('core/resource')->getTableName('anowave_customerprice_category')), Mage::getSingleton('core/resource')->getTableName('anowave_customerprice_category') . '.price_id = main_table.price_id', array
			(
				'price_category_id' => 'price_category_id'
			)
		);
			
		$collection->addFieldToFilter('price_category_id', array
		(
			'in' => $product->getCategoryIds()
		));
			
		Mage::dispatchEvent('anowave_price_collection_get', array
		(
			'collection' => $collection
		));
			
		$collection->getSelect()->group('main_table.price_id');
		
		$prices = $model->getPrices();
		
		foreach ($collection as $price)
		{
			$prices[$price->getPriceId()] = $price->getData();
		}
		
		$model->setPrices($prices);
		$model->setProduct($product);
			
		Mage::dispatchEvent('anowave_price_list_prepare', array
		(
			'model' 	=> $model,
			'quantity' 	=> $quantity
		));
		
		return true;
	}
	
	/**
	 * Exact price
	 * 
	 * @param Varien_Object $model
	 * @param string $quantity
	 */
	private function exactPrice(Varien_Object $model, $quantity = null)
	{
		if (!$quantity)
		{
			foreach ($model->getPrices() as $price)
			{
				if (!(int) @$price['price_tier_quantity'])
				{
					return $price;
				}
			}
		}
		else 
		{
			
			$prices = $model->getPrices();
			
			return reset($prices);
		}
	}
	
	/**
	 * Get prices as array 
	 * 
	 * @param Varien_Object $model
	 */
	private function arrayPrice(Varien_Object $model)
	{
		return $model->getPrices();
	}
	
	/**
	* Modify block
	* 
	* @param (Varien_Event_Observer) $observer
	*/
	public function modify(Varien_Event_Observer $observer)
	{	
		switch($observer->getBlock()->getNameInLayout())
		{
			case 'product.info.addtocart':
	
				if (!Mage::getSingleton('customer/session')->isLoggedIn() && 1 == (int) Mage::getStoreConfig('price/settings/status'))
				{
					$observer->getTransport()->setHtml('');
				}
				break;
		}
	
		if ('catalog/product_price' == $observer->getBlock()->getType())
		{
			$observer->getTransport()->setHtml
			(
				$this->renderPrice($observer, $observer->getBlock())
			);
		}
	}
	
	/**
	 * Save customer 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function save(Varien_Event_Observer $observer)
	{
		if (Mage::app()->getRequest()->getParam('customer_id'))
		{
			$model = Mage::getModel('price/price_global')->loadCustomer
			(
				Mage::app()->getRequest()->getParam('customer_id')
			);
	
			$model->setPriceGlobalCustomerId(Mage::app()->getRequest()->getParam('customer_id'));
			$model->setPriceGlobalDiscount
			(
				Mage::app()->getRequest()->getParam('price_global_discount')
			);
	
	
			$valid = new Varien_Object(array
			(
				'from' 	=> Mage::app()->getRequest()->getParam('price_global_valid_from'),
				'to' 	=> Mage::app()->getRequest()->getParam('price_global_valid_to')
			));
			
			
			if ($valid->from)
			{
				$model->setPriceGlobalValidFrom
				(
					date('Y-m-d H:i:s', strtotime($valid->from))
				);
			}
			else 
			{
				$model->setPriceGlobalValidFrom(null);
			}
			
			if ($valid->to)
			{
				$model->setPriceGlobalValidTo
				(
					date('Y-m-d H:i:s', strtotime($valid->to))
				);
			}
			else 
			{
				$model->setPriceGlobalValidTo(null);
			}
			
			$model->save();
		}
		
		return true;
	}
	
	/**
	 * Reset options price 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function getConfigurableTypePrice(Varien_Event_Observer $observer)
	{
		if (Mage::helper('price')->useSimplePricing())
		{
			$observer->getProduct()->setConfigurablePrice(0);
		}
	}
	
	/**
	 * Additional config options 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function getAdditionalOptions(Varien_Event_Observer $observer)
	{
		if (Mage::helper('price')->useSimplePricing())
		{
			$observer->getResponseObject()->setAdditionalOptions(array
			(
				'productPrice' 		=> 0,
				'productOldPrice' 	=> 0,
				'priceInclTax' 		=> 0,
				'priceExclTax' 		=> 0,
				'skipCalculate'		=> 1
			));
		}
	}
	
	/**
	 * Get default price 
	 * 
	 * @param Mage_Catalog_Model_Product $product
	 */
	private function getDefaultPrice(Mage_Catalog_Model_Product $product, $quantity = null)
	{
		return $product->getFinalPrice($quantity);
	}
	
	/**
	 * Render price 
	 * 
	 * @param Varien_Event_Observer $observer
	 * @param Mage_Catalog_Block_Product_Price $block
	 */
	private function renderPrice(Varien_Event_Observer $observer, Mage_Catalog_Block_Product_Price $block)
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn())
		{			
			if (Mage::getStoreConfig('price/settings/status'))
			{
				if (false !== stripos($block->getTemplate(), 'tierprices'))
				{
					return null;
				}
				else 
				{

					return Mage::getStoreConfig('price/settings/message');
				}
			}
			else 
			{
				return $observer->getTransport()->getHtml();
			}
			
		}
		else 
		{
			if (!$block->getModified()) /* Prevent recursive event bubbling */
			{
				$clone = clone $block;
				
				$clone->getProduct()->setFinalPrice
				(
					$this->getFinalPrice($block->getProduct(), Mage::getSingleton('customer/session')->getCustomer())
				);

				return $clone->setType('catalog/product_price')->setData(array
				(
					'modified'=> true,
					'product' => $clone->getProduct()
				))->toHtml();
			}
		}
	}
	
	public function productsCollectionPrices(Varien_Event_Observer $observer){}
	
	/**
	 * Set quote item discount 
	 * 
	 * @param Mage_Catalog_Model_Product $product
	 * @param number $discount
	 */
	public function setQuoteItemCustomerDiscount(Mage_Catalog_Model_Product $product, $discount = 0)
	{
		$item = $this->getQuote()->getItemByProduct($product);
		
		if ($item && $item->getId())
		{
			$amount = $item->getQty() * (float) $discount;

			$item->getProduct()->setIsSuperMode(true);
			$item->setCustomerDiscount($amount);
			$item->save();
		}
	}
	
	/**
	 * Set quote item discount 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function salesQuoteItemSetCustomerDiscount(Varien_Event_Observer $observer)
	{
		if (false)
		{
			$observer->getQuoteItem()->setCustomerDiscount($discount);
		}
    }
}