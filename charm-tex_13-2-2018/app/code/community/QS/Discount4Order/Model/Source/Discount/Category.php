<?php
/**
 * Discount For Order Extension
 *
 * @category   QS
 * @package    QS_Discount4Order
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2010 Quart-soft Ltd http://quart-soft.com
 */
class QS_Discount4Order_Model_Source_Discount_Category extends Mage_Core_Model_Abstract {
    
	protected $_categories;
	
    public function toOptionArray() 
	{
        $this->_categories = array();
		$category_tree = $this->_getCategoryTree();
		$this->_getCategoryArray($category_tree['children'],0);
        $options = array();
        foreach ($this->_categories as $category)
            $options[] = array(
                    'value' => $category['id'],
                    'label' => $category['name'],
            );

        return $options;
    }
	
	protected function _nodeToArray(Varien_Data_Tree_Node $node)
	{
		$result = array();
		$result['category_id'] = $node->getId();
		$result['parent_id'] = $node->getParentId();
		$result['name'] = $node->getName();
		$result['is_active'] = $node->getIsActive();
		$result['position'] = $node->getPosition();
		$result['level'] = $node->getLevel();
		$result['children'] = array();

		foreach ($node->getChildren() as $child) {
			$result['children'][] = $this->_nodeToArray($child);
		}

		return $result;
	}

	protected function _getCategoryTree($storeId = null, $categoryId = null) 
	{
		$tree = Mage::getResourceSingleton('catalog/category_tree')
			->load();
/*		
		if ($website = Mage::app()->getRequest()->getParam('website', false)) {
			$website = Mage::getModel('core/website')->load($website);
		}
*/		
		if (!$storeId) {
			if($store = Mage::app()->getRequest()->getParam('store', $this->_getDefaultStoreId())) {
				$storeId = Mage::getModel('core/store')->load($store)->getId();
			}
//			$storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
		}

//		$website = Mage::app()->getRequest()->getParam('website', false);
//		Mage::getModel('core/website')->load('website code')
//		$store = Mage::app()->getRequest()->getParam('store', false);
		
		if (!$storeId ) {
			$storeId = $this->_getDefaultStoreId();
		}

		if(!$categoryId) {
			$categoryId = Mage::getModel('core/store')->load($storeId)->getRootCategoryId();
			$categoryId = 1;
		}

		$tree = Mage::getResourceSingleton('catalog/category_tree')
			->load();
		
		$root = $tree->getNodeById($categoryId);
		
		if($root && $root->getId() == 1) {
			$root->setName(Mage::helper('catalog')->__('Root'));
		}
		
		$collection = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect('name')
//			->addAttributeToSelect('id')
//			->addAttributeToSelect('is_active')
		;
		
		if($storeId) {
			$collection->setStoreId($storeId);
		}
		
		$tree->addCollectionData($collection, true);

		return $this->_nodeToArray($root);

	}

	protected function _getCategoryArray($tree,$level) 
	{
		$level ++;
		foreach($tree as $item) {
			$this->_categories[] = array('id' => $item['category_id'], 
										 'name' => str_repeat("..", $level).$item['name'],
									);
			$childrenArray = $this->_getCategoryArray($item['children'],$level);
			$this->_categories = $this->_categories + $childrenArray;
		}
		return $this->_categories;
	}
	
	protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

}