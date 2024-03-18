<?php

class Extendware_EWPRibbon_Helper_Product extends Extendware_EWCore_Helper_Data_Abstract {
	public function isConditionMet(Mage_Catalog_Model_Product $product, array $condition) {
		$value = $this->getProductAttributeValue($product, $condition['attribute']);
		
		if ($condition['attribute'] == 'ew:category_paths') {
			$bool = false;
			foreach ($value as $categoryIds) {
				if ($condition['condition'] == 'eq') $bool = in_array($condition['value'], $categoryIds) === true;
				elseif ($condition['condition'] == 'neq') $bool =(in_array($condition['value'], $categoryIds) === false);
				if ($bool === true) return $bool;
			}
			return false;
		}
		
		if (is_array($value)) {
			if ($condition['condition'] == 'eq') return in_array($condition['value'], $value) === true;
			elseif ($condition['condition'] == 'neq') return (in_array($condition['value'], $value) === false);
		} else {
			if ($condition['condition'] == 'eq') return ($value == $condition['value']);
			elseif ($condition['condition'] == 'neq') return ($value != $condition['value']);
			elseif ($condition['condition'] == 'lt') return ($value < $condition['value']);
			elseif ($condition['condition'] == 'gt') return ($value > $condition['value']);
			elseif ($condition['condition'] == 'lteq') return ($value <= $condition['value']);
			elseif ($condition['condition'] == 'gteq') return ($value >= $condition['value']);
			elseif ($condition['condition'] == 'like') return (stripos($value, $condition['value']) !== false);
			elseif ($condition['condition'] == 'nlike') return (stripos($value, $condition['value']) === false);
		}
		
		return false;
	}
	
	public function getProductAttributeValue(Mage_Catalog_Model_Product $product, $code) {
		$value = null;
		
		if ($code == 'ew:id') $code = 'entity_id';
		elseif ($code == 'ew:attribute_set_id') $code = 'attribute_set_id';
		elseif ($code == 'ew:status') $code = 'status';
		elseif ($code == 'ew:type_id') $code = 'type_id';
		
		if (strpos($code, 'ew:') === 0) {
			$value = $this->getSpecialProductAttributeValue($product, $code);
		} else if (strpos($code, 'ew:') !== 0) {
			$value = $product->getData($code);
			$frontendType = $product->getAttributeInformation($code, 'frontend_input');
			if (in_array($frontendType, array('select', 'multiselect'))) {
				$value = $this->getAttributeText($code);
			}
		}

		return $value;
	}
	
	public function getSpecialProductAttributeValue(Mage_Catalog_Model_Product $product, $code) {
		$code = preg_replace('/^ew:/', '', $code);
		
		if ($code == 'category_ids') return (array)$product->getCategoryIds();
		elseif ($code == 'is_in_stock') return (int)$product->isInStock();
		elseif ($code == 'is_saleable') return (int)$product->isSaleable();
		elseif ($code == 'qty') return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
		elseif ($code == 'seconds_since_creation') return (time() - strtotime($product->getCreatedAt()));
		elseif ($code == 'seconds_since_update') return (time() - strtotime($product->getUpdatedAt()));
		elseif ($code == 'discount_percent') return ($product->getPrice() > 0 ? (100-($product->getFinalPrice() / $product->getPrice())*100) : 0);
		elseif ($code == 'discount_amount') return ($product->getPrice() - $product->getFinalPrice());
		elseif ($code == 'price') {
			return $product->getStore()->convertPrice($product->getFinalPrice(), false, false);
		} elseif ($code == 'is_new') {
			$fromTime = @strtotime($product->getNewsFromDate());
			$toTime = @strtotime($product->getNewsToDate());
			if (!$toTime) $toTime = time() + 999999;
			return (time() >= $fromTime and time() <= $toTime);
		} elseif ($code == 'category_paths') {
			$categoryPaths = array();
			$ids = $product->getCategoryIds();
			foreach ($ids as $id) {
				$category = $this->getCategory($id);
				$categoryPaths[] = explode('/', $category->getPath());
			}
			return $categoryPaths;
		}
		return null;
	}
	
	protected function getCategory($id) {
		static $categories = array();
		if (isset($categories[$id]) === false) {
			$categories[$id] = Mage::getModel('catalog/category')->load($id);
		}
		
		return clone $categories[$id];
	}
}


