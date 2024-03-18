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
 
class Anowave_Price_Model_Api_Price extends Mage_Api_Model_Resource_Abstract
{
	/**
	* apiCreatePrice()
	* 
	* @param (int) $price_customer_id
	* @param (int) $price_product_id
	* @param (int) $price_type Allowed: (1 - Fixed price,2 - Discount %)
	* @param (decimal) $price
	* @param (decimal) $price_discount
	* @param (array) $price_category_id - Array of associative arrays e.g array(array('id' => 12));
	* @param (int) $price_tier_quantity
	* @param (string) $price_valid_from Valid date YYYY-MM-DD H:i:s
	* @param (string) $price_valid_to  Valid date YYYY-MM-DD H:i:s
	* 
	* @example $response = $this->client->apiCreatePrice($this->session, 5,8,1,12.34,10, array(12,15), 22, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
	*/
	public function createPrice($price_customer_id = null, $price_product_id = null, $price_type = 1, $price, $price_discount, $price_category_id, $price_tier_quantity = 0, $price_valid_from = null, $price_valid_to = null, $price_apply_further = 0)
	{
		$model = Mage::getModel('price/price');
		
		$model->setPriceCustomerId($price_customer_id);
		$model->setPriceProductId($price_product_id);
		$model->setPriceType($price_type);
		$model->setPrice($price);
		$model->setPriceDiscount($price_discount);
		$model->setPriceApplyFurther($price_apply_further);
		
		

		try 
		{
			$model->save();
			
			if ($price_category_id)
			{
				$categories = (array) $price_category_id;
			}
			else 
			{
				$categories = array();
			}
			
			foreach ($categories as $category_id)
			{
				$category = Mage::getModel('price/price_category');
			
				$category->setPriceId($model->getId());
				$category->setPriceCategoryId($category_id);
			
				$category->save();
			}
	
			$response = new Varien_Object();
			
			$response->setArgs(new Varien_Object(array
			(	
				'price_tier_quantity' 	=> $price_tier_quantity,
				'price_valid_from'		=> $price_valid_from,
				'price_valid_to'		=> $price_valid_to
			)));
			
			$response->setModel($model);
			
			Mage::dispatchEvent('anowave_soap_create_price', array
			(
				'response' => $response
			));
			
			return $response->getModel()->getId();
		}
		catch (Exception $e)
		{
			/* Error(s) occurred */
		}
		
		return 0;
	}
	
	/**
	* apiDeletePrice()
	* 
	* @param (int) $price_customer_id
	* @param (int) $price_product_id
	* @param (int) $price_id (Optional)
	* 
	* @example $response = $this->client->apiDeletePrice($this->session,5,8,110);
	*/
	public function deletePrice($price_customer_id = 0, $price_product_id = 0, $price_id = 0)
	{
		$model = Mage::getModel('price/price');
			
		$collection = $model->getCollection();
		
		if ($price_customer_id)
		{
			$collection->addFieldToFilter('price_customer_id', $price_customer_id);
		}
		
		if ($price_product_id)
		{
			$collection->addFieldToFilter('price_product_id', $price_product_id);
		}
		
		if ($price_id)
		{
			$collection->addFieldToFilter('price_id', $price_id);
		}
		
		foreach ($collection as $price)
		{
			$price->delete();
		}
		
		return true;
	}
	
	/**
	* Retrieve customer price(s)
	*/
	public function getPrices($price_customer_id = null)
	{
		$prices = array();
		
		$collection = Mage::getModel('price/price')->getCollection()->addFieldToSelect('*');
		
		$collection->getSelect()->joinLeft
		(
			array('c1' => Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar')), 'main_table.price_customer_id = c1.entity_id AND c1.attribute_id = ' . Mage::getModel('eav/entity_attribute')->loadByCode(1, 'firstname')->getAttributeId(), array('firstname' => 'value')
		);
		
		$collection->getSelect()->joinLeft
		(
			array('c2' => Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar')), 'main_table.price_customer_id = c2.entity_id AND c2.attribute_id = ' . Mage::getModel('eav/entity_attribute')->loadByCode(1, 'lastname')->getAttributeId(), array('lastname' => 'value')
		);
		
		$collection->getSelect()->joinLeft
		(
			array('categories' => Mage::getSingleton('core/resource')->getTableName('anowave_customerprice_category')), 'main_table.price_id = categories.price_id', array('categories.price_category_id')
		);
		
		if ($price_customer_id)
		{
			$collection->addFieldToFilter('price_customer_id', (int) $price_customer_id);
		}
		
		$collection->getSelect()->columns('GROUP_CONCAT(categories.price_category_id) AS price_category_id');
		$collection->getSelect()->group('main_table.price_id');

		Mage::dispatchEvent('anowave_price_collection_get', array
		(
			'collection' => $collection,
			'args'		 => func_get_args()
		));
		
		foreach ($collection as $price)
		{
			$prices[] = $price->getData();	
		}
		
		foreach ($prices as &$price)
		{
			$price['price_category_id'] = array_map('intval', explode(',', $price['price_category_id']));
		}
		
		unset($price);
		
		return $prices;
	}
	
	/**
	 * Create global discount 
	 * 
	 * @param int $price_global_customer_id
	 * @param float $price_global_discount
	 * @param string $price_global_valid_from
	 * @param string $price_global_valid_to
	 */
	public function createDiscount($price_global_customer_id, $price_global_discount, $price_global_valid_from, $price_global_valid_to)
	{
		$object = new Varien_Object(array
		(
			'code' 						=> 0,
			'price_global_customer_id' 	=> $price_global_customer_id,
			'price_global_discount' 	=> $price_global_discount,
			'$price_global_valid_from' 	=> $price_global_valid_from,
			'price_global_valid_to' 	=> $price_global_valid_to
		));
		
		/* Dispatch Event */
		Mage::dispatchEvent('anowave_price_create_discount', array
		(
			'object' => $object
		));
		
		return $object->getCode();
	}
	
	/**
	 * Delete global discount
	 * 
	 * @param int $price_global_customer_id
	 */
	public function deleteDiscount($price_global_customer_id)
	{
		$object = new Varien_Object(array
		(
			'code' 						=> 0,
			'price_global_customer_id' 	=> $price_global_customer_id
		));
		
		/* Dispatch Event */
		Mage::dispatchEvent('anowave_price_delete_discount', array
		(
			'object' => $object
		));
		
		return $object->getCode();
	}
	
	/**
	 * Get global discount 
	 * 
	 * @param int $price_global_customer_id
	 */
	public function getDiscount($price_global_customer_id)
	{
		$object = new Varien_Object(array
		(
			'price_global_customer_id' 	=> $price_global_customer_id
		));
		
		/* Dispatch Event */
		Mage::dispatchEvent('anowave_price_get_discount', array
		(
			'object' => $object
		));
		
		return array
		(
			'price_global_id' 			=> $object->getData('price_global_id'),
			'price_global_customer_id' 	=> $object->getData('price_global_customer_id'),
			'price_global_discount' 	=> $object->getData('price_global_discount'),
			'price_global_valid_from' 	=> $object->getData('price_global_valid_from'),
			'price_global_valid_to' 	=> $object->getData('price_global_valid_to'),
		);
	}
}