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
 
class Anowave_Price_Model_Resource_Price_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
    { 
        $this->_init('price/price');
    }
    
	public function getSelectCountSql()
	{
		$this->_renderFilters();
	
		$countSelect = clone $this->getSelect();
		$countSelect->reset(Zend_Db_Select::ORDER);
		$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
		$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
		$countSelect->reset(Zend_Db_Select::COLUMNS);
	
		if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) 
		{
			$countSelect->reset(Zend_Db_Select::GROUP);
			$countSelect->distinct(true);
			$group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
			
			$countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
		} 
		else 
		{
			$countSelect->columns('COUNT(*)');
		}

		return $countSelect;
	}
	
	/**
	 * Add products data
	 */
	public function addProductData()
	{

		$productAttributes = array('product_name' => 'name');
		
		foreach ($productAttributes as $alias => $attributeCode) 
		{
			$tableAlias = $attributeCode . '_table';
			
			$attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
	
			//Add eav attribute value
			$this->getSelect()->joinLeft
			(
				array($tableAlias => $attribute->getBackendTable()),"main_table.price_product_id = $tableAlias.entity_id AND $tableAlias.attribute_id={$attribute->getId()}",
				array($alias => 'value')
			);
			
			$this->getSelect()->columns('name_table.attribute_id as ' . $alias);
		}

		return $this;
	}
	
	public function addProductFilter(Mage_Core_Controller_Request_Http $request)
	{
		$filter = Mage::helper('adminhtml')->prepareFilterString($request->getParam('filter'));
		
		if (isset($filter['price_product_id']))
		{
			$this->addFieldToFilter('name_table.value', array
			(
				'like' => "%{$filter['price_product_id']}%"
			));
			
			unset($filter['price_product_id']);
			
			$request->setParam('filter', base64_encode(http_build_query($filter)));
		}
		
		return $this;
		
	}
}
