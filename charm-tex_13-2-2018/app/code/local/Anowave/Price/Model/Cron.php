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
 * @copyright 	Copyright (c) 2017 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
class Anowave_Price_Model_Cron extends Anowave_Price_Model_Import
{
	/**
	 * CSV file
	 * 
	 * @var string
	 */
	const CSV = 'import/import.csv';
	
	/**
	 * Update prices from CSV file
	 */
	public function update()
	{
		/**
		 * Get pathname
		 * 
		 * @var string
		 */
		$file = join(DIRECTORY_SEPARATOR,array
		(
			Mage::getBaseDir(),'import','import.csv')
		);
		
		/**
		 * Check if file exists and can be read
		 */
		if (file_exists($file) && is_readable($file))
		{
			$this->execute($file);
		}
		
		return $this;
	}
}