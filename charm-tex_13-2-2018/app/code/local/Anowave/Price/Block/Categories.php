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
 
class Anowave_Price_Block_Categories extends Mage_Adminhtml_Block_Catalog_Category_Tree 
{
	public function __construct()
    {
        parent::__construct();
        
        $this->setTemplate('price/categories.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = false;
    }
    
    public function getCategoryIds()
    {
        return array();
    }
    
    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }
    
    public function getRootNode()
    {
        $root = $this->getRoot();
        
        if ($root && in_array($root->getId(), $this->getCategoryIds())) 
        {
            $root->setChecked(true);
        }
        return $root;
    }

    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) 
        {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        
        $root = Mage::registry('category_root');
        
        if (is_null($root)) 
        {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            
            $ids = $this->getSelectedCategotyPathIds($rootId);
            
            $tree = Mage::getResourceSingleton('catalog/category_tree')->loadByIds($ids, false, false);
            
            if ($this->getCategory()) 
            {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            
            $tree->addCollectionData($this->getCategoryCollection());
            
            $root = $tree->getNodeById($rootId);
            
            Mage::register('category_root', $root);
        }
        return $root;
    }
    
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);
        
        if ($this->_isParentSelectedCategory($node)) 
        {
            $item['expanded'] = true;
        }
        
        if (in_array($node->getId(), $this->getCategoryIds())) 
        {
            $item['checked'] = true;
        }
        return $item;
    }
    
    protected function _isParentSelectedCategory($node)
    {
        $result = false;
        
        // Contains string with all category IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) 
        {
            $selectedCategoryIds = $this->getCategoryIds();
            
            $allChildrenArr = explode(',', $allChildren);
            
            for ($i = 0, $cnt = count($selectedCategoryIds); $i < $cnt; $i++) 
            {
                $isSelf = $node->getId() == $selectedCategoryIds[$i];
                
                if (!$isSelf && in_array($selectedCategoryIds[$i], $allChildrenArr)) 
                {
                    $result = true;
                    
                    break;
                }
            }
        }
        return $result;
    }
    
    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) 
        {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) 
            {
                if ($root) 
                {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);
        
        if (!$node || !$node->hasChildren()) 
        {
            return '[]';
        }
        
        $children = array();
        
        foreach ($node->getChildren() as $child) 
        {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }
    
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('adminhtml/price/tree', array('_current' => true));
    }
    
    public function getSelectedCategoryPathIds($rootId = false)
    {
        $ids = array();
        
        $categories = $this->getCategoryIds();
        
        if (empty($categories)) 
        {
            return array();
        }
        
        $collection = Mage::getResourceModel('catalog/category_collection');
        
        if ($rootId) 
        {
            $collection->addFieldToFilter('parent_id', $rootId);
        }
        else 
        {
            $collection->addFieldToFilter('entity_id', array
            (
            	'in' => $categories
            ));
        }

        foreach ($collection as $item) 
        {
            if ($rootId && !in_array($rootId, $item->getPathIds())) 
            {
                continue;
            }
            
            foreach ($item->getPathIds() as $id) 
            {
                if (!in_array($id, $ids)) 
                {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }
}