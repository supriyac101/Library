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
 
class Anowave_Price_Model_Observer_Customer
{
	/**
	 * Apply initial discount upon registration 
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function discount(Varien_Event_Observer $observer)
	{
		if ($observer->getCustomer() && $observer->getCustomer()->getId())
		{
			if (version_compare(phpversion(), '5.3.1', '>'))
			{
				$initial = Mage::helper('price')->getInitialDiscountConfig();
			
				if ($initial->apply)
				{
					$model = Mage::getModel('price/price');
			
					switch($initial->discount_type)
					{
						case Anowave_Price_Model_Import::F:
			
							$f = function($customer) use ($model, $initial)
							{
								$model->setPriceCustomerId($customer->getId());
								$model->setPriceProductId(null);
								$model->setPriceType(1);
								$model->setPrice($initial->discount_fixed);
								$model->setPriceDiscount(0);
								$model->setPriceApplyFurther(0);
									
								return $model;
							};
			
							break;
						case Anowave_Price_Model_Import::P:
			
							$f = function($customer) use ($model, $initial)
							{
								$model->setPriceCustomerId($customer->getId());
								$model->setPriceProductId(null);
								$model->setPriceType(2);
								$model->setPrice(0);
								$model->setPriceDiscount($initial->discount_percentage);
								$model->setPriceApplyFurther(0);
									
								return $model;
							};
			
							break;
					}
			
					$model = $f($observer->getCustomer());
			
					try
					{
						$model->save();
							
						if ($model->getId())
						{
							if ($initial->discount_categories)
							{
								foreach ($initial->discount_categories as $category_id)
								{
									$category = Mage::getModel('price/price_category');
										
									$category->setPriceId($model->getId());
									$category->setPriceCategoryId($category_id);
										
									$category->save();
								}
							}
						}
					}
					catch (\Exception $e)
					{
							
					}
				}
			}
		}
	}
}