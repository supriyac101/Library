<?php
if(Mage::helper('core')->isModuleEnabled('ITwebexperts_Request4quote'))
{
	require_once str_replace('_',DS,'ITwebexperts_Request4quote_controllers_QuoteController.php');

	class Best4Mage_FrontendConfigurableProductMatrix_QuoteController extends ITwebexperts_Request4quote_QuoteController {
		
		public function addallAction()
		{
			
			$cart   = $this->_getCart();
			
			$params = $this->getRequest()->getParams();
			
			if($this->getRequest()->getParam('r4quote') && $this->getRequest()->getParam('r4quote') != 'new'){
				if(!Mage::registry('cquote') || Mage::registry('cquote')->getId() != $this->getRequest()->getParam('r4quote')){
					//echo 'rrrr';
					$cart->truncate();
					$quote = Mage::getModel('request4quote/quote')->loadByIdWithoutStore($this->getRequest()->getParam('r4quote'));
					$quoteArr = $quote->getItemsCollection();
					//Mage::log(print_r($quoteArr), null, 'mylogfile.log');
					foreach($quoteArr as $quoteItem){
						if(!$quoteItem->getParentItem()){
							//Mage::log('iside22', null, 'mylogfile.log');
							$optionCollection = Mage::getModel('request4quote/quote_item_option')->getCollection()
									->addItemFilter(array($quoteItem->getId()));
							$optionArr = $optionCollection->getOptionsByItem($quoteItem);
							foreach($optionArr as $option){
								if($option->getCode()== 'info_buyRequest'){
									//Mage::log('iside22', null, 'mylogfile.log');
									/*$uProduct = Mage::getModel('catalog/product')
										->setStoreId(Mage::app()->getStore()->getId())
										->load($option->getProductId());*/
									$infoBuyRequest = unserialize($option->getValue());
									$infoBuyRequest['r4q'] = 1;
									$cart->addProduct($quoteItem->getProduct(), $infoBuyRequest);
									break;
								}
							}
						}
					}
					Mage::register('cquote', $quote);
				}
			}
			
			unset($params['product_matrix']);
			
			$product_matrix = $this->getRequest()->getParam('product_matrix');
			
			if (count(array_filter($product_matrix['qty'])) == 0) {
				if(count($params['super_attribute']) != 0 && count($params['super_attribute']) == count(array_filter($params['super_attribute']))) {
					$product_matrix = array();
					$product_matrix['qty'] = array( $params['product'] => $params['qty']);
					$product_matrix[$params['product']] = $params;
				} else {
					$this->_getSession()->addError(Mage::helper('core')->escapeHtml('No product selected for add to quote.'));
					$this->getResponse()->setRedirect($this->_getRefererUrl());
					return;
				}
			}
			//echo '<pre>';print_r($product_matrix);die;
			try {
				
				$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
				);
				
				$related = $this->getRequest()->getParam('related_product');
				$addToCartProductCount = 0;
				
				foreach($product_matrix['qty'] as $key => $singleQty)
				{
					if($singleQty == 0) {
						$addToCartProductCount++;
						continue;
					}
					
					$product = null;
					$product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app()->getStore()->getId() )->load ( $params['product'] );
					if (!$product) {
						$this->_goBack();
						return;
					}
					
					if (!$product->getR4qEnabled()) {
						Mage::throwException(Mage::helper('request4quote')->__('Quote requests disabled for this product.'));
					}
					
					$current_params = array();
					$current_params['product'] = $params['product'];
					$current_params['super_attribute'] = $product_matrix[$key]['super_attribute'];
					$current_params['qty'] = $filter->filter($singleQty);
					if(isset($params['options'])) $current_params['options'] = $params['options'];
					if(isset($params['cptp_qty'])) $current_params['cptp_qty'] = $params['cptp_qty'];
					if(isset($params['customoptionprice'])) $current_params['customoptionprice'] = $params['customoptionprice'];
					if(isset($product_matrix[$key]['customname'])) $current_params['customname'] = $product_matrix[$key]['customname'];
					if(isset($product_matrix[$key]['customthumb'])) $current_params['customthumb'] = $product_matrix[$key]['customthumb'];
					if(isset($product_matrix[$key]['customprice'])) $current_params['customprice'] = $product_matrix[$key]['customprice'];
					
					$cart->addProduct($product, $current_params);
				}
				
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}
				
				$quote = $cart->getQuote();
				Mage::helper('request4quote')->saveStartEndDatesToQuote($quote,$product);
				
				$cart->save();
				
				$this->_getSession()->setCartWasUpdated(true);
				
				if (!$this->_getSession()->getNoCartRedirect(true)) {
					if (!$cart->getQuote()->getHasError()) {
						$message = $this->__('%s was added to your Quote Request.', Mage::helper('core')->escapeHtml($product->getName()));
						$this->_getSession()->addSuccess($message);
					}
					$this->_goBack();
				}
				
			} catch (Mage_Core_Exception $e) {
				$session = Mage::getSingleton('catalog/session');
				if ($session->getUseNotice(true)) {
					$session->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$session->addError(Mage::helper('core')->escapeHtml($message));
					}
				}

				$url = $this->_getSession()->getRedirectUrl(true);
				if ($url) {
					$this->getResponse()->setRedirect($url);
				} else {
					$this->_redirectReferer(Mage::helper('request4quote/cart')->getCartUrl());
				}
			} catch (Exception $e) {
				$session = Mage::getSingleton('catalog/session');
				$session->addException($e, $this->__('Cannot add the item to Quote Request.'));
				Mage::logException($e);
				$this->_goBack();
			}
			$this->_goBack();
		}	
		
	}
}
