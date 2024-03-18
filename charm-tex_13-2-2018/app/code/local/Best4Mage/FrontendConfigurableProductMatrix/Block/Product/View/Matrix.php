<?php 
class Best4Mage_FrontendConfigurableProductMatrix_Block_Product_View_Matrix extends Mage_Catalog_Block_Product_View
{
	private $_isSimple = false;
	
	public $_showImage = 0;
	
	public function getMatrixHelper()
	{
		return Mage::helper('frontendconfigurableproductmatrix');	
	}
	
	public function isSimple()
	{
		return $this->_isSimple;	
	}
	
	protected function _toHtml()
    {
		$_product = $this->getProduct();
		$helper = $this->getMatrixHelper();
		if($helper->isEnable($_product)) {
			if($this->hasSuperAttrThird()){
				return parent::_toHtml();	
			}
			$this->_isSimple = $helper->isSimplePriceEnabled($_product);
			$alias = $this->getBlockAlias();
			if($helper->isSecondMatrix($_product) != 1 && $alias != 'dcpm'){
				if($helper->isCustomerShowMatrix()) {
					$mPosition = $helper->getMatrixPosition($_product);
					$displyTab = $helper->getDisplayTabs($_product);
					if((($mPosition == 0 || $mPosition == 2) && $displyTab != 1 && $alias == 'matrix') || (($mPosition == 1 || $mPosition == 3) && $displyTab == 1 && $alias != 'fmatrix')) return '';
					
					if((($mPosition == 1 || $mPosition == 3) && $alias == 'matrix') || ($helper->isShowLink($_product)) && $displyTab != 0) {
						if($helper->getMatrixTemplate($_product)){
							$this->setTemplate('frontendconfigurableproductmatrix/configurable2.phtml');
						} else {
							$this->setTemplate('frontendconfigurableproductmatrix/configurable1.phtml');	
						}
					} else {
						if($helper->getMatrixTemplate($_product)){
							$this->setTemplate('frontendconfigurableproductmatrix/configurable4.phtml');
						} else {
							$this->setTemplate('frontendconfigurableproductmatrix/configurable3.phtml');	
						}	
					}
				} else {
					return '';
				}
			}
			return parent::_toHtml();
		}
		return '';
	}
	
	protected function _prepareLayout()
    {
		$_product = $this->getProduct();
		$helper = $this->getMatrixHelper();
		if($helper->isEnable($_product)) {
			if($helper->useJquery())
			{
				$this->getLayout()->getBlock('head')->addJs('fcpm/jquery.js');
				$this->getLayout()->getBlock('head')->addJs('fcpm/noConflict.js');
			}
			$this->getLayout()->getBlock('head')->addJs('fcpm/fcpm-validation.js');
			$this->getLayout()->getBlock('head')->addCss('css/fcpm.css');
			
			$cssJsHtml = $this->getLayout()->getBlock('head')->getCssJsHtml();
			if(!strpos($cssJsHtml,'owl.carousel.js')) {
				$this->getLayout()->getBlock('head')->addJs('fcpm/owl.carousel.js');
			}

			if($this->hasSuperAttrThird()){
				return parent::_prepareLayout();
			}
			
			if($helper->isShowLink($_product)){
				$this->getLayout()->getBlock('head')->addJs('fcpm/popup.min.js');
			}

			if($helper->isInEasyTab($_product)){
				$customOptionBlock = $this->getLayout()->createBlock('catalog/product_view_options')->setTemplate('frontendconfigurableproductmatrix/options.phtml');
				$customOptionBlock->addOptionRenderer(
					'default',
					'catalog/product_view_options_type_default',
					'catalog/product/view/options/type/default.phtml'
				);
				$customOptionBlock->addOptionRenderer(
					'text',
					'catalog/product_view_options_type_text',
					'frontendconfigurableproductmatrix/options/text.phtml'
				);
				$customOptionBlock->addOptionRenderer(
					'file',
					'catalog/product_view_options_type_file',
					'frontendconfigurableproductmatrix/options/file.phtml'
				);
				$customOptionBlock->addOptionRenderer(
					'date',
					'catalog/product_view_options_type_date',
					'catalog/product/view/options/type/date.phtml'
				);
				$customOptionBlock->addOptionRenderer(
					'select',
					'frontendconfigurableproductmatrix/product_view_options_select',
					'frontendconfigurableproductmatrix/options/select.phtml'
				);
				
				$this->setChild('configurable_matrix_options',$customOptionBlock);
			}
			$this->_showImage = $helper->isShowProductImage($_product);
		}
		return parent::_prepareLayout();
    }
	
	public function getSubmitUrl($product, $additional = array())
    {
        return str_replace('/checkout/','/frontendconfigurableproductmatrix/',parent::getSubmitUrl($product, $additional));
    }
    
    public function getRequest4quoteUrl($product, $all = false, $additional = array())
    {
		if($all){
			return str_replace('/checkout/cart/add/','/frontendconfigurableproductmatrix/quote/addall/',parent::getSubmitUrl($product, $additional));
		} else {
			return str_replace('/checkout/cart/','/frontendconfigurableproductmatrix/quote/',parent::getSubmitUrl($product, $additional));
		}
    }
	
	private function isPriceIncludeTax()
	{
		return Mage::helper('tax')->priceIncludesTax();
	}
	
	private function getTaxCalculator()
	{
		return Mage::getSingleton('tax/calculation');
	}
	
	protected $_read;
	
	protected $_tbl_eav_attribute_option;
	
	protected function _sortOptions($options)
    {
        if (count($options)) {
            if (!$this->_read || !$this->_tbl_eav_attribute_option) {
                $resource = Mage::getSingleton('core/resource');

                $this->_read = $resource->getConnection('core_read');
                $this->_tbl_eav_attribute_option = $resource->getTableName('eav_attribute_option');
            }

            // Gather the option_id for all our current options
            $option_ids = array();
            foreach ($options as $optionId => $option) {
                $option_ids[] = $optionId;

                $var_name  = 'option_id_'.$optionId;
                $$var_name = $option;
            }

            $sql    = "SELECT `option_id` FROM `{$this->_tbl_eav_attribute_option}` WHERE `option_id` IN('".implode('\',\'', $option_ids)."') ORDER BY `sort_order`";
            $result = $this->_read->fetchCol($sql);

            $options = array();
            foreach ($result as $option_id) {
                $var_name  = 'option_id_'.$option_id;
                $options[$option_id] = $$var_name;
            }
        }

        return $options;
    }
	
	public function getSuperAttribute()
	{
		if(!$this->hasData('matrix_super_attribute'))
		{
			$_product    = $this->getProduct();
			$_attributes = $_product->getTypeInstance(true)->getConfigurableAttributes($_product);
			foreach($_attributes as $_attr)
			{
				$super_attr[$_attr->getAttributeId()] = array( 
					'code' => $_attr->getProductAttribute()->getAttributeCode(),
					'label' => $_attr->getProductAttribute()->getStoreLabel(),
					'position' => $_attr->getPosition()
				);
				foreach($_attr->getPrices() as $value){
					$store_label = explode('#',$value['store_label']);
					if($this->_isSimple && $this->getMatrixHelper()->isCptpEnable() != 1) {
						$super_attr[$_attr->getAttributeId()]['option'][$value['value_index']] = array('label' => $store_label[0]);
					} else {
						$super_attr[$_attr->getAttributeId()]['option'][$value['value_index']] = array(
							'label' 	=>	$store_label[0],
							'price' 	=>	$this->_preparePrice($value['pricing_value'], $value['is_percent']),
							'oldPrice' 	=>	$this->_prepareOldPrice($value['pricing_value'], $value['is_percent'])
						);
					}
					$super_attr[$_attr->getAttributeId()]['option'] = $this->_sortOptions($super_attr[$_attr->getAttributeId()]['option']);
				}
				
			}
			$this->setData('matrix_super_attribute', $super_attr);
		}
		return $this->getData('matrix_super_attribute');
	}
	
	
	
	public function getAllChildProducts()
	{
		if(!$this->hasData('matrix_super_attribute_product'))
		{
			$_product = $this->getProduct();
			$_allChilds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($_product->getId());
			$attributes = $this->getSuperAttribute();
			$_isShowStock = $this->getMatrixHelper()->isShowStock($_product);
			$_taxHelper = Mage::helper('tax');
			$_coreHelper = Mage::helper('core');
			$_imgHelper = $this->helper('catalog/image');
			$_outputHelper = $this->helper('catalog/output');
			$_mTemplate = $this->getMatrixHelper()->getMatrixTemplate($_product);
			$arrSimple = array();
			if($this->getMatrixHelper()->isDefaultSwatchEnabled())
			{
				$dimHelper = Mage::helper('configurableswatches/swatchdimensions');
				$this->setSwatchInnerWidth(
					$dimHelper->getInnerWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
				$this->setSwatchInnerHeight(
					$dimHelper->getInnerHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
				$this->setSwatchOuterWidth(
					$dimHelper->getOuterWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
				$this->setSwatchOuterHeight(
					$dimHelper->getOuterHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_DETAIL));
			}
			$orgPriceConfig = $_taxHelper->getPrice($_product,$_product->getFinalPrice());
			$isConfigTier = false;
			if($this->_isSimple){
				$isConfigTier = Mage::helper('configurableproductssimpleprices')->isTierConfigurable($_product);
			}
			
			if (count($_allChilds))
			{
				foreach($_allChilds[0] as $spd)
				{
					$confSimleProduct = Mage::getModel('catalog/product')->load($spd);
					if(!$confSimleProduct->isSaleable()) continue;
					$arrSimple[$spd]['name'] = $this->helper('catalog/output')->productAttribute($confSimleProduct, $confSimleProduct->getName(), 'name');
					$arrSimple[$spd]['sku'] = $confSimleProduct->getSku();
					$arrSimple[$spd]['weight'] = $confSimleProduct->getWeight();
					if($_isShowStock) $arrSimple[$spd]['stock'] = (int)$confSimleProduct->getStockItem()->getQty();
					
					$arrSimple[$spd]['maxQty'] = 'false'; 
					if($confSimleProduct->getStockItem()->getManageStock()){
						$arrSimple[$spd]['maxQty'] = (int)$confSimleProduct->getStockItem()->getQty();
					}
					/*---------------------------------*/
					$arrSimple[$spd]['priceColor'] = $this->getMatrixHelper()->getPriceColor((int)$confSimleProduct->getStockItem()->getQty());
					/*---------------------------------*/
					$arrSimple[$spd]['thumb'] = $confSimleProduct->getThumbnail();
					if($this->_showImage == 1) {
						$_img = '<img id="image'.$spd.'" src="'.$_imgHelper->init($confSimleProduct, 'image')->resize(80).'" alt="'.addslashes($this->escapeHtml($this->getImageLabel())).'" title="'.addslashes($this->escapeHtml($this->getImageLabel())).'" />';
						$arrSimple[$spd]['image'] = $_outputHelper->productAttribute($confSimleProduct, $_img, 'image');
					}
					
					if($confSimleProduct->getImage() == '' || $confSimleProduct->getImage() == 'no_selection'){
						$arrSimple[$spd]['has_img'] = 0;
					} else {
						$arrSimple[$spd]['has_img'] = 1;
						$arrSimple[$spd]['media'] = $this->getLayout()->createBlock('catalog/product_view_media')->setProduct($confSimleProduct)->setTemplate('catalog/product/view/media.phtml')->toHtml();
					}
					
					$price = $oldPrice = 0;
					$_swatchReplaced = true;
					foreach($attributes as $key => $attr)
					{
						$swatch = '';
						if($this->getMatrixHelper()->isReplacedWithSwatchs($key)) {
							$swatch = $this->generateSwatches($key,$confSimleProduct->getData($attr['code']),$confSimleProduct->getAttributeText($attr['code']),$attr['code'],$confSimleProduct->getId(), $_product);
							$arrSimple[$spd][$attr['code']] = $swatch;
						} else {
							$_myTitle = array();
							$_myTitle = explode('#',$confSimleProduct->getAttributeText($attr['code']));
							$arrSimple[$spd][$attr['code']] = $_myTitle[0];
						}
						if($this->_showImage == 0 && $_mTemplate && $_swatchReplaced){
							$arrSimple[$spd]['image'] = $swatch;
							$arrSimple[$spd]['has_img'] = 1;
							$_swatchReplaced = false;
						}
						$arrSimple[$spd][$attr['code'].'_value'] = $confSimleProduct->getData($attr['code']);
						if(!$this->_isSimple)
						{
							$tempConfigPrice = 0;
							$tempConfigPrice = $attr['option'][$confSimleProduct->getData($attr['code'])]['price'];
							$_product->setConfigurablePrice($tempConfigPrice);
                   	 		$_product->setParentId(true);
							Mage::dispatchEvent(
								'catalog_product_type_configurable_price',
								array('product' => $_product)
							);
							$price += $_product->getConfigurablePrice();
							$oldPrice += $attr['option'][$confSimleProduct->getData($attr['code'])]['oldPrice'];
						}
					}
					if($this->_isSimple)
					{
						$arrSimple[$spd]['exTaxPrice'] = $confSimleProduct->getFinalPrice();
						$arrSimple[$spd]['price'] = $_taxHelper->getPrice($confSimleProduct,$confSimleProduct->getFinalPrice());
						$arrSimple[$spd]['fprice'] = $_coreHelper->currency($arrSimple[$spd]['price'],true);
						$arrSimple[$spd]['oldPrice'] = $_taxHelper->getPrice($confSimleProduct,$confSimleProduct->getPrice());
						$arrSimple[$spd]['foldPrice'] = $_coreHelper->currency($arrSimple[$spd]['oldPrice'],true);
						if($isConfigTier){
							$arrSimple[$spd]['tier'] = $this->generateTier($_product,$orgPriceConfig,$price);
						} else {
							$arrSimple[$spd]['tier'] = $this->generateTier($confSimleProduct,$arrSimple[$spd]['price'],$price);
						}
						$arrSimple[$spd]['tier_qty'] = array_keys($arrSimple[$spd]['tier']);
					} else {
						$arrSimple[$spd]['price'] = $orgPriceConfig+$price;
						$arrSimple[$spd]['fprice'] = $_coreHelper->currency($arrSimple[$spd]['price'],true);
						$arrSimple[$spd]['oldPrice'] = $_taxHelper->getPrice($_product,$_product->getPrice())+$oldPrice;
						$arrSimple[$spd]['foldPrice'] = $_coreHelper->currency($arrSimple[$spd]['oldPrice'],true);
						$arrSimple[$spd]['tier'] = $this->generateTier($_product,$orgPriceConfig,$price);
						$arrSimple[$spd]['tier_qty'] = array_keys($arrSimple[$spd]['tier']);
					}
				}
			}
			$this->setData('matrix_super_attribute_product', $arrSimple);
		}
		return $this->getData('matrix_super_attribute_product');
	}
	
	public function generateSwatches($atid, $atvId, $altText, $attributeCode, $direction = 'row', $product = null)
	{
		$_option_vals = array();
		$adminLbl = $html = '';
		
		if($this->getMatrixHelper()->isDefaultSwatchEnabled())
		{
			$_swatchInnerWidth = $this->getSwatchInnerWidth();
			$_swatchInnerHeight = $this->getSwatchInnerHeight();
			$_swatchOuterWidth = $this->getSwatchOuterWidth();
			$_swatchOuterHeight = $this->getSwatchOuterHeight();
			
			$_swatchUrl = Mage::helper('configurableswatches/productimg')->getSwatchUrl($product, $altText, $_swatchInnerWidth, $_swatchInnerHeight, $_swatchType);
			$_hasImage = !empty($_swatchUrl);
			$_aClass = 'swatch-link swatch-link-' . $atid;
			$_liClass = '';
			if ($_hasImage) {
				$_aClass .= ' has-image';
			} elseif (strlen($altText) > 3) {
				$_liClass .= ' wide-swatch';
			}
			$html = '<a href="javascript:void(0)" onclick="replaceSimpleMediaImages(\''.$direction.'_'.$atvId.'\');" name="" id="swatch'.$atvId.'" class="'.$_aClass.'" title="'.$altText.'" style="height: '.$_swatchOuterHeight.'px; '.(!$_hasImage ? 'min-width' : 'width').': '.$_swatchOuterWidth.'px;">';
			$html .= '<span class="swatch-label" style="height: '.$_swatchInnerHeight.'px; '.(!$_hasImage ? 'min-width' : 'width').': '.$_swatchInnerWidth.'px; line-height: '.$_swatchInnerHeight.'px;">';
			if ($_hasImage){
				$html .= '<img src="'.$_swatchUrl.'" alt="'.$altText.'" width="'.$_swatchInnerWidth.'" height="'.$_swatchInnerHeight.'" />';
            } else {
                $html .= $altText;
			}
            $html .= '</span><span class="x">X</span></a>';
			
		} else {
			if(!$this->hasData('options_'.$atid))
			{
				$_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setPositionOrder('asc')->setAttributeFilter($atid)->setStoreFilter(0)->load();
				
				foreach( $_collection->toOptionArray() as $_cur_option ) {
					$_option_vals[$_cur_option['value']] = $_cur_option['label'];
				}
				$this->setData('options_'.$atid, $_option_vals);
			} else {
				$_option_vals = $this->getData('options_'.$atid);
			}
			
			if(array_key_exists($atvId, $_option_vals)){
				$adminLbl = Mage::helper('acsp')->getSwatchColorByOpId($atvId);
				$altText = $_option_vals[$atvId];
				$swatchsize = Mage::helper('acsp')->getSwatchSize($attributeCode);
				$sizes = explode("x", $swatchsize);
				$width = $sizes[0];
				$height = $sizes[1];
				$swatchimage = Mage::helper('acsp')->getSwatchUrl($atvId);
				if($swatchimage != '')
				{
					$html = '<img src="'.$swatchimage.'" id="swatch'.$atvId.'" class="swatch" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" onclick="replaceSimpleMediaImages(\''.$direction.'_'.$atvId.'\');"/>';
				}
				elseif($adminLbl)
				{
					$multuColor = explode('::',$adminLbl);
					if(count($multuColor)==1){
						$html = '<div id="swatch'.$atvId.'" title="'.$altText.'" class="swatch" style="background-color:'.$adminLbl.'; width:'.$width.'px; height:'.$height.'px;" onclick="replaceSimpleMediaImages(\''.$direction.'_'.$atvId.'\');"></div>';
					} else {
						$html = '<div id="swatch'.$atvId.'" title="'.$altText.'" class="swatch" style="width:'.(count($multuColor)*($width+2)).'px;box-sizing: border-box;" onclick="replaceSimpleMediaImages(\''.$direction.'_'.$atvId.'\');">';
						foreach($multuColor as $mColor){
							$html .= '<div style="background-color:'.$mColor.'; width:'.$width.'px; height:'.$height.'px;float:left;"></div>';
						}
						$html .= '</div>';
					}
				}
				else
				{
					$html = '<img src="'.Mage::helper('acsp')->getSwatchUrl('empty').'" id="swatch'.$atvId.'" class="swatch" alt="'.$altText.'" width="'.$width.'px" height="'.$height.'px" title="'.$altText.'" onclick="replaceSimpleMediaImages(\''.$direction.'_'.$atvId.'\');"/>';
				}
			}
		}
		return $html;
	}
	
	private function generateTier($_product,$orgPrice,$additional = 0)
	{
		$array = $_product->getTierPrice();
		$_taxHelper = Mage::helper('tax');
		$farr = array();
		foreach($array as $key => $tier)
		{
			$tempPrice = min($_taxHelper->getPrice($_product,$tier['price']), $orgPrice);
			$farr[(int)$tier['price_qty']] = $tempPrice+$additional;
		}
		return $farr;
	}
	
	protected function _preparePrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getProduct()->getFinalPrice() * $price / 100;
        }
        $price = Mage::app()->getStore()->roundPrice($price);
        return $price;
        // return $this->_convertPrice($price, true);
    }
	
	protected function _prepareOldPrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getProduct()->getPrice() * $price / 100;
        }
        $price = Mage::app()->getStore()->roundPrice($price);
        return $price;
        // return $this->_convertPrice($price, true);
    }
	
	public function _convertPrice($price, $round = false)
    {
        if (empty($price)) {
            return 0;
        }

		$price = Mage::app()->getStore()->convertPrice($price);
		
		if(!$this->isPriceIncludeTax()) {
			$price += $this->getTaxCalculator()->calcTaxAmount($price,$this->_calcTaxRate());
		}
		
		if ($round) {
            $price = Mage::app()->getStore()->roundPrice($price);
        }
        return $price;
    }
	
	private $_taxRate = null;
	
	private function _calcTaxRate()
	{
		if($this->_taxRate == null)
		{
			$taxCalculation = $this->getTaxCalculator();
			if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
				$taxCalculation->setCustomer(Mage::registry('current_customer'));
			}
			
			$_request = $taxCalculation->getRateRequest();
			$_request->setProductClassId($this->getProduct()->getTaxClassId());
			$this->_taxRate = $taxCalculation->getRate($_request);
		}
		return $this->_taxRate;
	}
	
	public function isSuperSimpleProduct($aCode,$a,$bCode,$b)
	{
		$children = $this->getAllChildProducts();
		//echo '<pre>';print_r($children);die;
		if(count($children))
		{
			foreach($children as $pid => $pData)
			{
				if($pData[$aCode.'_value'] == $a && $pData[$bCode.'_value'] == $b){
					return array_merge($pData, array('id' => $pid));	
				}
			}
		}
		return false;
	}

	public function isSuperSimpleProductSingle($aCode,$a)
	{
		$children = $this->getAllChildProducts();
		//echo '<pre>';print_r($children);die;
		if(count($children))
		{
			foreach($children as $pid => $pData)
			{
				if($pData[$aCode.'_value'] == $a){
					return array_merge($pData, array('id' => $pid));	
				}
			}
		}
		return false;
	}
	
	public function isSuperSimpleProductMany($aCode,$a,$bCode,$b)
	{
		$selectedOld = $this->getSuperAttrThird(); 
		$children = $this->getAllChildProducts();
		if(count($children))
		{
			foreach($children as $pid => $pData)
			{
				$continue = false;
				foreach($selectedOld as $atcode => $atopv){
					if($pData[$atcode.'_value'] != $atopv['option']) {
						$continue = true;
						break;	
					}
				}
				if($continue) continue;
				if($pData[$aCode.'_value'] == $a && $pData[$bCode.'_value'] == $b){
					return array_merge($pData, array('id' => $pid));	
				}
			}
		}
		return false;
	}
}
?>
