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
 
class Anowave_Price_Model_Import extends Mage_Core_Model_Abstract
{
	const F = 'F';
	const P = 'P';
	
	protected function getPathname($file)
	{
		return "var/uploads/$file";
	}
	
	/**
	 * Import 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function import(Varien_Event_Observer $observer)
	{
		/**
		 * Get file from system config 
		 * 
		 * @var []
		 */
		$file = Mage::getStoreConfig('price/import/file');
		
		if ($file)
		{
			$this->execute($this->getPathname($file));
		}
		
		return true;
	}
	
	public function execute($file)
	{
		set_time_limit(0);
				
		$fp = fopen($file, "r");
			
		if ($fp)
		{
			fgetcsv($fp);
	
			$data = array();
	
			while($row = fgetcsv($fp))
			{
				$data[] = array
				(
					'price_customer_id'		=> @trim($row[0]),
					'price_product_id'		=> @trim($row[1]),
					'price_discount'		=> @trim($row[2]),
					'price_type'			=> @trim($row[3]),
					'price_category_id'		=> array_filter(array
						(
							@trim($row[4])
						)),
					'price_tier_quantity' 	=> @trim($row[5]),
					'price_valid_from' 		=> @trim($row[6]),
					'price_valid_to' 		=> @trim($row[7])
				);
			}
	
			fclose($fp);
	
			if (Anowave_Price_Model_System_Config_Key::KEY_SKU === (int) Mage::getStoreConfig('price/import/key'))
			{
				/**
				 * Map SKU to product id
				 */
				foreach ($data as &$item)
				{
					$item['price_product_sku'] 	= $item['price_product_id'];
					$item['price_product_id'] 	= Mage::getModel("catalog/product")->getIdBySku($item['price_product_id']);
				}
					
				unset($item);
			}
	
			/**
			 * Create transport object
			 *
			 * @var \Varien_Object
			 */
			$object = new Varien_Object();
	
			$object->setData('file_path', $path);
			$object->setData('file_data', $data);
	
			Mage::dispatchEvent('anowave_price_get_import_data', array
			(
				'data_object' => $object
			));
	
			/**
			 * Get data
			 *
			 * @var []
			*/
			$data = $object->getFileData();
	
			$customers = array();
	
			foreach ($data as $row)
			{
				$customers[$row['price_customer_id']][] = $row;
			}
	
			$model = Mage::getModel('price/api_price_V2');
	
			/**
			 * Run import
			*/
			foreach ($customers as $customer => $entity)
			{
				/**
				 * Delete customer custom price(s)
				 */
				$model->deletePrice($customer);
					
				/**
				 * Recreate new prices
				*/
				foreach ($entity as $data)
				{
					switch ($data['price_type'])
					{
						case self::F:
							$this->import_f
							(
							$model,
							$data['price_customer_id'],
							$data['price_product_id'],
							$data['price_discount'],
							$data['price_category_id'],
							$data['price_tier_quantity'],
							$data['price_valid_from'],
							$data['price_valid_to']
							);
							break;
						case self::P:
							$this->import_p
							(
							$model,
							$data['price_customer_id'],
							$data['price_product_id'],
							$data['price_discount'],
							$data['price_category_id'],
							$data['price_tier_quantity'],
							$data['price_valid_from'],
							$data['price_valid_to']
							);
							break;
					}
						
				}
			}
	
			/**
			 * Unset file value programmatically
			 */
			Mage::getConfig()->saveConfig('price/import/file', '', 'default', 0);
	
			/**
			 * Notify customer
			*/
			Mage::getSingleton('core/session')->addSuccess('All prices imported successfully!');
		}
	}
	
	/**
	 * Import fixed discount
	 */
	public function import_f
	(
		Anowave_Price_Model_Api_Price_V2 $model,
		$price_customer_id = null, 
		$price_product_id = null, 
		$price = 0, 
		$price_category_id = array(), 
		$price_tier_quantity = 0, 
		$price_valid_from = null, 
		$price_valid_to = null
	)
	{
		$model->createPrice($price_customer_id,$price_product_id,1,$price,0,$price_category_id, $price_tier_quantity, $price_valid_from, $price_valid_to);
	}
	
	/**
	 * Import percent discount
	 */
	public function import_p
	(
		Anowave_Price_Model_Api_Price_V2 $model, 
		$price_customer_id = null, 
		$price_product_id = null, 
		$price_discount = 0, 
		$price_category_id = array(),
		$price_tier_quantity = 0,
		$price_valid_from = null,
		$price_valid_to = null
	)
	{
		$model->createPrice($price_customer_id,$price_product_id,2,0,$price_discount,$price_category_id, $price_tier_quantity, $price_valid_from, $price_valid_to);
	}
}