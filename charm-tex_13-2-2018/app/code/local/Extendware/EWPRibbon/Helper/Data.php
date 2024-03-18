<?php
class Extendware_EWPRibbon_Helper_Data extends Extendware_EWCore_Helper_Data_Abstract
{
	public function getMatchingProductPageRibbons($data) {
		return $this->getMatchingRibbons('product', $data);
	}
	
	public function getMatchingCategoryPageRibbons($data) {
		return $this->getMatchingRibbons('category', $data);
	}
	
	protected function getMatchingRibbons($type, $data) {
		if (!$data) return array();
		if (!$data instanceof Varien_Data_Collection) {
			$collection = new Varien_Data_Collection();
			$data = $collection->addItem($data);
		}
		
		return $this->getMatchingRibbonsInCollection($type, $data);
	}
	
	protected function getMatchingRibbonsInCollection($type, Varien_Data_Collection $products) {
		$matchingRibbons = array();
		$productIds = $products->getColumnValues('entity_id'); // getAllIds() does not work with product collection
		$ribbons = $this->getRibbons($productIds);
		
		$positionLog = array();
		foreach ($products as $product) {
			foreach ($ribbons as $ribbon) {
				if ($ribbon->getData($type . '_status') == 'disabled') {
					continue;
				}

				if ($ribbon->appliesToProduct($product)) {
					if (isset($matchingRibbons[$product->getId()]) === false) {
						$matchingRibbons[$product->getId()] = array();
						$positionLog[$product->getId()] = array();
					}
					
					$position = $ribbon->getData($type . '_position');
					list($valign, $align) = explode('_', $position, 2);
					
					if (isset($positionLog[$product->getId()][$position])) {
						if ($ribbon->getHideStatus() == 'enabled') {
							continue;
						}
					}
					
					$refSelectors = trim($ribbon->getData($type . '_ref_selectors'));
					if ($refSelectors) $refSelectors = preg_split('/\s*(?:\r\n|\n)\s*/s', $refSelectors);
					if (is_array($refSelectors) === false) $refSelectors = array();

					array_unshift($matchingRibbons[$product->getId()], array(
						'id' => $ribbon->getId(),
						'product_id' => $product->getId(),
						'text' => $this->getRibbonText($product, $ribbon->getData($type . '_text')),
						'valign' => $valign,
						'align' => $align,
						'image_path' => $ribbon->getData($type . '_image_path'),
						'text_style' => $ribbon->getData($type . '_text_style'),
						'image_style' => $ribbon->getData($type . '_image_style'),
						'container_style' => $ribbon->getData($type . '_container_style'),
						'inner_container_style' => $ribbon->getData($type . '_inner_container_style'),
						'ref_selectors' => $refSelectors,
					));

					$positionLog[$product->getId()][$position] = true;
					
					if ($ribbon->getRuleProcessingMode() == 'stop') {
						break;
					}
				}
			}
		}

		return $matchingRibbons;
	}

	public function getRibbonText(Mage_Catalog_Model_Product $product, $text) {
		$text = nl2br($text);
		
		$attributes = $this->mHelper('adminhtml')->getTextSpecialVariables();
		if (preg_match_all('/\{\{var\s+?(.+?)\}\}/si', $text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				list($line, $string) = $match;
				@list($code, $transformations) = explode('|', $string, 2);
				if (in_array($code, $attributes) === true) {
					$text = str_replace($line, $this->getVariableForText($product, $code, $transformations), $text);
				}
			}
		}

		return $text;
	}
	
	protected function getVariableForText(Mage_Catalog_Model_Product $product, $code, $transformations = null) {
		$value = $this->mHelper('product')->getProductAttributeValue($product, $code);
		if (in_array($code, array('minimal_price', 'price', 'cost', 'special_price', 'ew:discount_amount', 'ew:price'))) {
			$value = Mage::app()->getStore()->convertPrice($value, true, false);
		}
		
		$transformations = explode('|', $transformations);
		foreach ($transformations as $transformation) {
			@list($command, $args) = explode(':', $transformation);
			if ($command == 'sprintf') {
				$value = @sprintf($args, $value);
			} elseif ($command == 'divide') {
				$value = (float)$args ? $value / (float)$args : $value;
			} elseif ($command == 'multiply') {
				$value = $value * (float)$args;
			} elseif ($command == 'floor') {
				$value = floor($value);
			} elseif ($command == 'ceil') {
				$value = ceil($value);
			} elseif ($command == 'round') {
				$value = round($value);
			} elseif ($command == 'min') {
				$value = min($value, (float)$args);
			} elseif ($command == 'max') {
				$value = max($value, (float)$args);
			}
		}
		
		return $value;
	}
	
	protected function getRibbons(array $productIds) {
		static $ribbons = null;
		if ($ribbons === null) {
			$ribbons = Mage::getModel('ewpribbon/ribbon')->getCollection();
			$ribbons->joinStoreTable(array('store_id'), 'store');
			$ribbons->joinImageTable('product_image_id', array('product_image_path' => 'path'), 'product_image', 'left');
			$ribbons->joinImageTable('category_image_id', array('category_image_path' => 'path'), 'category_image', 'left');
			$ribbons->addFieldToFilter('store.store_id', Mage::app()->getStore()->getId());
			$ribbons->addFieldToFilter('status', 'enabled');
			$ribbons->addDateToFilter('from_date', '<=', 'now()', 0, 'day', true);
			$ribbons->addDateToFilter('to_date', '>=', 'now()', 0, 'day', true);
			$ribbons->setOrder('priority', 'ASC');
			if (!$ribbons->count()) return $ribbons;
			
			$hasProducts = @max($ribbons->getColumnValues('has_products'));
			if (!$hasProducts) return $ribbons;
			
			$ribbonIds = $ribbons->getColumnValues('ribbon_id');
			
			// a more efficient way to get product IDs
			$products = Mage::getModel('ewpribbon/ribbon_product')->getCollection();
			$products->addFieldToFilter('ribbon_id', array('in' => $ribbonIds));
			$products->addFieldToFilter('product_id', array('in' => $productIds));
			$products->addDateToFilter('from_date', '<=', 'now()', 0, 'day', true);
			$products->addDateToFilter('to_date', '>=', 'now()', 0, 'day', true);
			$rows = $products->getRows(array('state', 'product_id', 'ribbon_id'));
			
			$ribbonToProductStates = array();
			foreach ($rows as $row) {
				if (isset($ribbonToProductStates[$row['ribbon_id']]) === false) {
					$ribbonToProductStates[$row['ribbon_id']] = array();
				}
				
				$ribbonToProductStates[$row['ribbon_id']][$row['product_id']] = ($row['state'] == 'included');
			}
			
			foreach ($ribbonToProductStates as $ribbonId => $productStates) {
				$ribbon = $ribbons->getItemById($ribbonId);
				if ($ribbon) $ribbon->setProductStates($productStates);
			}
		}
		
		return $ribbons;
	}
	
	public function getCategoryArray($rootId)
	{
		$tree = $pos = array();
		$collection = Mage::getModel('catalog/category')->getCollection()->addNameToResult();
		foreach ($collection as $category) {
			$path = explode('/', $category->getPath());
			if ((!$rootId || in_array($rootId, $path)) && $category->getLevel()) {
				$name = $category->getName() ? $category->getName() : $this->__('Root Category');
				$tree[$category->getId()] = array(
					'label' => str_repeat('--', $category->getLevel() - 1) . $name, 'value' => $category->getId(), 'path' => $path
				);
			}
			$pos[$category->getId()] = $category->getPosition();
		}
		
		foreach ($tree as $catId => $cat) {
			$order = array();
			foreach ($cat['path'] as $id) {
				$order[] = $pos[$id];
			}
			$tree[$catId]['order'] = $order;
		}

		usort($tree, array($this, 'compareCategoryOrder'));
		return $tree;
	}
	
	public function compareCategoryOrder($a, $b)
	{
		foreach ($a['path'] as $i => $id) {
			if (!isset($b['path'][$i])) {
				return 1;
			}
			if ($id != $b['path'][$i]) {
				return ($a['order'][$i] < $b['order'][$i]) ? -1 : 1;
			}
		}
		return ($a['value'] == $b['value']) ? 0 : -1;
	}
}