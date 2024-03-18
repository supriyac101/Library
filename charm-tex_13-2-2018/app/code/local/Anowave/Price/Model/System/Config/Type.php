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
 
class Anowave_Price_Model_System_Config_Type
{
	public function toOptionArray()
	{
		return array
		(
			array
			(
				'value' => Anowave_Price_Model_Import::F, 
				'label' => __('Fixed discount')
			
			),
			array
			(
				'value' => Anowave_Price_Model_Import::P, 
				'label' => __('Percentage')
			)
		);
	}
}