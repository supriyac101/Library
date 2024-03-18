<?php
class Anowave_Price_Model_Observer
{
	const PRICE_FIXED 		= 1;
	const PRICE_DISCOUNT 	= 2;
	
	public function getPrice(Varien_Event_Observer $observer)
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$product = &$observer->getEvent()->getProduct();
			
			$quantity = null;
			
			foreach (Mage::getModel('checkout/cart')->getQuote()->getAllItems() as $item) 
			{
				if ($item->getProductId() == $product->getId()) 
				{
					$quantity = (int) $item->getQty();
					
					break;
				}
			}

			/* Store final price for future reference */
			$product->setFinalPrice
			(
				$this->getFinalPrice($product, Mage::getSingleton('customer/session'), $quantity)
			);
			
			return $this;
		}
	}
	
	public function getPriceAdmin(Varien_Event_Observer $observer)
	{
		if (Mage::app()->getStore()->isAdmin()) /* Apply only within admin */
		{
			$product = &$observer->getProduct();

			/* Store final price for future reference */
			$product->setPrice
			(
				$this->getFinalPrice($product, $observer->getCustomer())
			);
			
			return $this;
		}

		return $this;
	}

	public function getFinalPrice(Mage_Catalog_Model_Product $product, Mage_Customer_Model_Session $customer, $quantity = null)
	{			
		if (Mage::getStoreConfig('price/settings/custom'))
		{
			$price = $this->getCustomerPrice($product, $customer, $quantity);
			
			if (!$product->getOriginalPrice())
			{
				$product->setOriginalPrice
				(
					$product->getFinalPrice()
				);
			}
			
			if ($price)
			{
				switch ($price['price_type'])
				{
					case self::PRICE_FIXED:
						return $price['price'];
						break;
					case self::PRICE_DISCOUNT:
						
						$discount = $price['price_discount'];
						$discount = $discount < 0 ? 0 : ($discount > 100 ? 100 : $discount);

						return $product->getOriginalPrice() - ($product->getOriginalPrice()*$discount)/100;
					
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
							return $product->getFinalPrice();
						}
					}
					elseif ($customer->getPriceGlobalValidFrom())
					{
						if ($date <= new DateTime($customer->getPriceGlobalValidFrom()))
						{
							return $product->getFinalPrice();
						}
					}
					elseif ($customer->getPriceGlobalValidTo())
					{
						if ($date >= new DateTime($customer->getPriceGlobalValidTo()))
						{
							return $product->getFinalPrice();
						}
					}
					
					return $product->getOriginalPrice() - ($product->getOriginalPrice()*$discount)/100;
				}
			}
		}
		
		return $product->getFinalPrice();
	}
	
	public function getCustomerPrice(Mage_Catalog_Model_Product $product, Mage_Customer_Model_Session $customer, $quantity = null)
	{
		$collection = Mage::getModel('price/price')->getCollection()->addFieldToSelect('*')->addFieldToFilter('price_customer_id',  (int) $customer->getId())->addFieldToFilter('price_product_id',  (int) $product->getId());

		Mage::dispatchEvent('anowave_price_collection_get', array
		(
			'collection' => $collection
		));
		
		$prices = array();
		
		foreach ($collection as $price)
		{
			$prices[] = $price->getData();
		}
		
		$model = new Varien_Object(array
		(
			'prices' => $prices
		));

		/* Allow other extensions to modify prices */
		Mage::dispatchEvent('anowave_price_list_prepare', array
		(
			'model' 	=> $model,
			'quantity' 	=> $quantity
		));
		
		if ($model->getPrices())
		{
			return reset
			(
				$model->getPrices()
			);	
		}
		else 
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
		
			foreach ($collection as $price)
			{
				$prices[] = $price->getData();
			}

			$model->setPrices($prices);
			
			
			Mage::dispatchEvent('anowave_price_list_prepare', array
			(
				'model' 	=> $model,
				'quantity' 	=> $quantity
			));
			
			return reset
			(
				$model->getPrices()
			);	
			
		}
			
		return array();
	}
	
	/**
	* Modify block
	* 
	* @param (Varien_Event_Observer) $observer
	*/
	public function modify(Varien_Event_Observer $observer)
	{	
		if ('catalog/product_price' == $observer->getBlock()->getType())
		{
			$observer->getTransport()->setHtml
			(
				$this->renderPrice($observer, $observer->getBlock())
			);
		}
	}
	
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
					$this->getFinalPrice($block->getProduct(), Mage::getSingleton('customer/session'))
				);

				return $clone->setType('catalog/product_price')->setData(array
				(
					'modified'=> true,
					'product' => $clone->getProduct()
				))->toHtml();
			}
		}
	}
}