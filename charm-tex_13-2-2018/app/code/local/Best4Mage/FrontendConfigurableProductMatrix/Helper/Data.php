<?php

class Best4Mage_FrontendConfigurableProductMatrix_Helper_Data extends Mage_Core_Helper_Abstract
{
	private function useProduct()
	{
		return intval($this->getConfig('product_level'));
	}
	
	public function isEnable($product)
	{
		if(Mage::app()->getRequest()->getControllerName() == 'cart') return 0;
		if($this->useProduct() == 1) return ($product->getFcpmEnable() == 1);
		else return ($this->getConfig('enable') == 1);
	}
	
	public function getMatrixTemplate($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmTemplate() == 1);
		else return ($this->getConfig('matrix_template','layout_options') == 1);
	}
	
	public function getMatrixPosition($product)
	{
		if($this->useProduct() == 1){
			$return = $product->getFcpmTemplatePosition();
		} else {
			$return = $this->getConfig('matrix_position','layout_options');
		}
		return $return;
	}

	public function getMatrixColumns() {
		return $this->getConfig('matrix_columns','layout_options');
	}
	
	public function isShowFDD($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmShowfdd() == 1);
		else return ($this->getConfig('showfdd','layout_options') == 1);
	}
	
	public function isOnlyCheckBox($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmCheckbox() == 1);
		else return ($this->getConfig('only_checkbox','matrix_options') == 1);
	}
	
	public function isShowLink($product)
	{
		if($this->getDisplayTabs($product) == 0) return false;
		if($this->useProduct() == 1) return ($product->getFcpmShowLink() == 1);
		else return ($this->getConfig('show_link','layout_options') == 1);
	}
	
	public function isShowStock($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmShowStock() == 1);
		else return ($this->getConfig('show_stock','matrix_options') == 1);
	}
	
	public function isShowProductPrice($product)
	{
		if(Mage::helper('core')->isModuleEnabled('ITwebexperts_Request4quote'))
		{
			return ($product->getR4qHidePrice() != 1);
		} else {
			if($this->useProduct() == 1) return ($product->getFcpmShowProductPrice() == 1);
			else return ($this->getConfig('show_product_price','matrix_options') == 1);
		}
	}
	
	public function isShowRowTotal($product)
	{
		if(Mage::helper('core')->isModuleEnabled('ITwebexperts_Request4quote'))
		{
			return ($product->getR4qHidePrice() != 1);
		} else {
			if($this->useProduct() == 1) return ($product->getFcpmShowRowtotal() == 1);
			else return ($this->getConfig('show_rowtotal','matrix_options') == 1);
		}
	}
	
	public function isShowGrandTotal($product)
	{
		if(Mage::helper('core')->isModuleEnabled('ITwebexperts_Request4quote'))
		{
			return ($product->getR4qHidePrice() != 1);
		} else {
			if($this->useProduct() == 1) return ($product->getFcpmShowGrandtotal() == 1);
			else return ($this->getConfig('show_grandtotal','matrix_options') == 1);
		}
	}
	
	public function isShowTotalColumn()
	{
		if($this->getConfig('hide_total_column','matrix_options') == 1)
			return false;
		else
			return true;
	}
	
	public function isSimplePriceEnabled($product)
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		
		if(array_key_exists('Best4Mage_ConfigurableProductsSimplePrices',$modulesArray)) {
			if($modulesArray['Best4Mage_ConfigurableProductsSimplePrices']->is('active')) {
				if($this->getConfig('product_level','settings','cpsp') == 1){
					return ($product->getCpspEnable() == 1);
				} else {
					return ($this->getConfig('enable','settings','cpsp') == 1);
				}
			}
		}
		return false;
	}
	
	public function isInEasyTab($product)
	{
		if($this->checkIfTabsUsedOrNot()){
			return true;
		}
		$tab = 'easy_tabs';
		$group = 'general';
		if($this->getConfig('enabled',$group,$tab) == 1){
			return ($this->getMatrixPosition($product) == 3);
		}
		return false;
	}
	
	public function useJquery()
	{
		return ($this->getConfig('jquery','other_options') == 1);
	}
	
	public function isShowProductImage($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmShowImage() == 1);
		else return ($this->getConfig('show_image','matrix_options') == 1);
	}
	
	public function isSecondMatrix($product)
	{
		if($this->getDisplayTabs($product) == 0) return false;
		if($this->useProduct() == 1) return ($product->getFcpmSecondAttribute() == 1);
		else return ($this->getConfig('second_attribute','layout_options') == 1);
	}
	
	public function isCptpEnable()
	{
		return $this->getConfig('tier_base','settings','cptp');	
	}
	
	public function getIaddtocart()
	{
		return intval($this->getConfig('iaddtocart','category_options'));
	}
	
	public function getDisplayTabs($product)
	{
		if($this->useProduct() == 1){
			$return = $product->getFcpmDisplayTabs();
		} else {
			$return = $this->getConfig('display_tabs','layout_options');
		}
		return $return;
	}
	
	public function isTabMultiple($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmDefaultTab() == 1);
		else return ($this->getConfig('default_tab','layout_options') == 1);
	}
	
	public function getMultipleOption($product)
	{
		if($this->useProduct() == 1){
			$return = $product->getFcpmMultipleOption();
		} else {
			$return = $this->getConfig('multiple_option','layout_options');
		}
		return $return;
	}
	
	public function isAcspEnable()
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		
		if(array_key_exists('Best4Mage_Acsp',$modulesArray)) {
			if($modulesArray['Best4Mage_Acsp']->is('active')) {
				return ($this->getConfig('enabled','colorselectorplusgeneral','acsp') == 1);
			}
		}
		return false;
	}
	
	public function isDefaultSwatchEnabled()
	{
		if(version_compare(Mage::getVersion(), '1.9.1', '>=')===true && !$this->isAcspEnable()){
			return Mage::helper('configurableswatches')->isEnabled();
		}
		return false;
	}
	
	public function isReplacedWithSwatchs($attrId)
	{
		if($this->isAcspEnable()){
			if($this->getConfig('replace_swatch','colorswatch') == 1){
				$swatch_attributes = array();
				$swatchattributes = $this->getConfig('colorattributes','colorselectorplusgeneral','acsp');
				$swatch_attributes = explode(",", $swatchattributes);
				if(in_array($attrId, $swatch_attributes)) return true;
			}
		}elseif($this->isDefaultSwatchEnabled()){
			$swatch_attributes = array();
			$swatch_attributes = Mage::helper('configurableswatches')->getSwatchAttributeIds();
			if(in_array($attrId, $swatch_attributes)) return true;
		}
		return false;
	}
	
	public function isCspImgSwatch()
	{
		return true;	
	}
	
	public function getSecondMatrixPosition($product)
	{
		if($this->useProduct() == 1){
			$return = $product->getFcpmSecondLayout();
		} else {
			$return = $this->getConfig('second_attribute_position','layout_options');
		}
		return $return;
	}
	
	public function isCustomerShowMatrix()
	{
		$roleId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		$availableRoleIds = explode(',',$this->getConfig('customer_roles','other_options'));
		if(in_array($roleId, $availableRoleIds)) return true;
		else return false;
	}
	
	public function isAdvancedCustomOptionEnabled()
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		
		if(array_key_exists('MageWorx_CustomOptions',$modulesArray))
			if($modulesArray['MageWorx_CustomOptions']->is('active') && $this->getConfig('enabled','customoptions','mageworx_catalog') == 1)
				return true;

		return false;
	}
	
	public function isRfqEnabled($product)
	{
		if(Mage::helper('core')->isModuleEnabled('ITwebexperts_Request4quote'))
		{
			return ($product->getR4qEnabled() == 1);
		} else {
			return false;
		}
	}
	
	public function isRfqOrderDisabled($product)
	{
		if(Mage::helper('core')->isModuleEnabled('ITwebexperts_Request4quote'))
		{
			return ($product->getR4qOrderDisabled() == 1);
		} else {
			return false;
		}
	}
	
	public function checkIfTabsUsedOrNot()
	{
		$tabsObject = Mage::app()->getLayout()->getBlock('product.info.tabs');
		if($tabsObject || in_array(Mage::getSingleton('core/design_package')->getPackageName(),array('hellowired'))){
			return true;	
		} else {
			return false;	
		}
	}
		
	private function getConfig($fieldName, $basic_options = 'basic_options', $fcpm = 'fcpm')
	{
		return Mage::getStoreConfig($fcpm.'/'.$basic_options.'/'.$fieldName, Mage::app()->getStore());
	}
	
	public function getProductOptionsHtml(Mage_Catalog_Model_Product $product)
	{
		$blockOptionsHtml = null;
		if($product->getTypeId()=="simple"||$product->getTypeId()=="virtual"||$product->getTypeId()=="configurable")
		{  
			$blockOption = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Options");
			$blockOption->addOptionRenderer("default","catalog/product_view_options_type_default","frontendconfigurableproductmatrix/product/list/options/type/default.phtml");
			$blockOption->addOptionRenderer("text","catalog/product_view_options_type_text","frontendconfigurableproductmatrix/product/list/options/type/text.phtml");
			$blockOption->addOptionRenderer("file","catalog/product_view_options_type_file","frontendconfigurableproductmatrix/product/list/options/type/file.phtml");
			$blockOption->addOptionRenderer("select","frontendconfigurableproductmatrix/product_list_options_type_select","frontendconfigurableproductmatrix/product/list/options/type/select.phtml");
			$blockOption->addOptionRenderer("date","catalog/product_view_options_type_date","frontendconfigurableproductmatrix/product/list/options/type/date.phtml") ;
 
			$blockOption->setProduct($product);
			if($product->getOptions())
			{  
				foreach ($product->getOptions() as $o) 
				{     
					$blockOptionsHtml .= $blockOption->getOptionHtml($o); 
				};    
			}  
		}  
 		return $blockOptionsHtml; 
	}

	public function isApplyPriceColor($product)
	{
		if($this->useProduct() == 1) return ($product->getFcpmApplyPriceColor() == 1);
		else return ($this->getConfig('apply_price_color','matrix_options') == 1);
	}
	
	public function fnGetCombinationColor($intQty, $arrPriceColors)
	{
		$strFinalColor = '';
		foreach($arrPriceColors as $key => $arrSpecQtyColors)
		{			
			switch ($arrSpecQtyColors['condition']) {
				case '1':
					if($intQty >= $arrSpecQtyColors['qty'])
					{
						$strFinalColor = $arrSpecQtyColors['color'];
					}
					break;
				
				case '2':
					if($intQty <= $arrSpecQtyColors['qty'])
					{
						$strFinalColor = $arrSpecQtyColors['color'];
					}
					break;

				default:
					
					break;
			}
		}
		return $strFinalColor;
	}
	
	public function getPriceColor($_qty) {
		$priceColors = unserialize($this->getConfig('price_colors', 'price_color_options'));
		$color = '';
		if($priceColors) {
			$color = $this->fnGetCombinationColor($_qty, $priceColors);
		}
		return $color;
	}

	public function getBackordersClass() {

		$isBackorders = $this->getConfig('backorders', 'item_options', 'cataloginventory');
		$isOutOfStockVisible = $this->getConfig('show_out_of_stock', 'options', 'cataloginventory');

		$class = '';
		if($isBackorders && $isOutOfStockVisible) {
			$class = 'is-backorders';
		}
		
		return $class;
	}
	
	public function setListTemplate() {

		if($this->isCustomerShowMatrix()) {

			return 'frontendconfigurableproductmatrix/product/list.phtml';
		}
		else {

			return 'catalog/product/list.phtml';
		}
	}
	
	public function getQtyPriceSelectors() {

		$selectors = $this->getConfig('qty_price_selector', 'other_options');

		return ($selectors != '') ? $selectors : '.product-options-bottom .price-box, .product-shop .price-box, .product-options-bottom .add-to-cart label, .product-options-bottom .add-to-cart #qty';
	}

}
