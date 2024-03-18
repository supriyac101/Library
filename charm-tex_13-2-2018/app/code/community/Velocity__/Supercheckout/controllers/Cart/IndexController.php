<?php
require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';

class Velocity_Supercheckout_Cart_IndexController extends Mage_Checkout_CartController {

    /**
     * This is content load URL for Angular Js to call the block for layout
     * Created By - Brajendra
     */
    public function cartLayoutAction() {
        $block = $this->getLayout()->createBlock('Velocity_Supercheckout_Block_Cart', 'shoppingcartpopup_cart', array('template' => 'shoppingcartpopup/cart.phtml'));
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Function to get the details of customer's cart
     * Created By: Brajendra
     */
    public function getCartAction($msgs = array()) {
        // This array will be used to get the related products list if there are no mappped products with cart products - Brajendra
        $notInProductIds = array();

        // Visibility filter condition for related products
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        // Visibility filter condition for related products
        // Shopping cart popup session variable
        $settings = Mage::getSingleton('core/session')->getShoppingCartPopUpSettings();
        
        $settings_array = unserialize($settings);
//        echo '<pre>';
//    print_r($settings_array['supercheckout']['shopping_cart']);
//    echo '</pre>';
        $itemsCount = Mage::helper('checkout/cart')->getItemsCount();
        if ($itemsCount < 1) {
            echo json_encode(array("cartProductsCount" => 0));
            die;
        }

        // getQuote function to get the cart details
        $cart = Mage::getModel('checkout/cart')->getQuote();

        $cartTotal = array();
        $cartTotal['couponCode'] = "";
        $cartTotal['discount'] = array();

        $totals = $cart->getTotals();
        $cartTotal['subtotal'] = Mage::helper('core')->currency($totals['subtotal']->getValue(), true, false);

        if (trim($cart->getCouponCode()) != "") {
            $cartTotal['couponCode'] = $cart->getCouponCode();
        }

        if (isset($totals['discount'])) {
            $cartTotal['discount'] = array('title' => $totals['discount']->getTitle(),
                'discount' => Mage::helper('core')->currency($totals['discount']->getValue(), true, false)
            );
        }

        $cartTotal['grandTotal'] = Mage::helper('core')->currency($cart->getGrandTotal(), true, false);

        $productCategories = array();
        $productArray = array();

        foreach ($cart->getAllVisibleItems() as $item) {
            $productCartID = $item->getId();
            $productID = $item->getProductId();

            // Product Categories - Get product categories to find related products
            $categoryIds = $item->getProduct()->getCategoryIds($productID);
            if (count($categoryIds) > 0) {
                foreach ($categoryIds as $categoryId) {
                    array_push($productCategories, $categoryId);
                }
            }
            // Product Categories
            // Product Delete URL - Code to ge the delete url of product from cart - Brajendra
            $renderer = new Mage_Checkout_Block_Cart_Item_Renderer();
            $renderer->setItem($item);
            $deleteURL = $renderer->getDeleteUrl();
            // Product Delete URL - 

            $productImage = (string) Mage::helper('catalog/image')->init($item->getProduct(), 'small_image')->resize(120, 90);

            array_push($notInProductIds, $productID);

            // Product options
            $productOptions = array();
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            if (!empty($options['attributes_info'])) {
                $productOptions = $options['attributes_info'];
            }
            // Product options
            // Bundle product options
            $bundleProductOptions = array();
            if (isset($options['bundle_options'])) {
                $bundleProductOptions = $options['bundle_options'];
            }
            // Bundle product options
            // Cart products array
		
    //@30-July-2015 - patch to show custom options in popup
    $checkoutSession = Mage::getSingleton('checkout/session');
    $checkoutquote = Mage::getSingleton('checkout/session')->getQuote();
    $totalsquote =  $checkoutquote->getTotals();
    $settings = Mage::helper('supercheckout')->getSettings() ;
    //foreach ($checkoutSession->getQuote()->getAllVisibleItems() as $item) {
        $_item = Mage::getModel('catalog/product')->load($productID);
	$helper = Mage::helper('catalog/product_configuration');
        $var=$helper->getCustomOptions($item);
	$optionstr='';   
	if($var) {
	    foreach ($var as $o) {
	    $optionstr.=$o['label'].':'.$o['print_value'].', '; 
	    $optionstr=str_replace("&quot;","\"",$optionstr);
	    $optionstr=str_replace("&#039;","'",$optionstr);
	    $optionstr=str_replace("&amp;","&",$optionstr);
	    $optionstr=str_replace("&lt;","<",$optionstr);
	    $optionstr=str_replace("&gt;;",">",$optionstr);
	    }
	}
    //}
	

            array_push($productArray, array('productID' => $productID, 'productCartID' => $productCartID,
                'productName' => $item->getProduct()->getName(),
                'price' => Mage::helper('core')->currency($item->getProduct()->getPrice(), true, false),
                'finalPrice' => Mage::helper('core')->currency($item->getProduct()->getFinalPrice(), true, false),
                'priceTotal' => Mage::helper('core')->currency($item->getRowTotal(), true, false),
                'productSKU' => $item->getProduct()->getSku(),
                'productImage' => $productImage,
                'quantity' => $item->getQty(),
                'productUrl' => $item->getProduct()->getProductUrl(),
                'productAttrs' => $productOptions,
                'bundleProductOptions' => $bundleProductOptions,
                'deleteURL' => $deleteURL,
		'customAttr' => $optionstr
            ));
        }

        // BOC - Code to get the related products list - Brajendra - 02-Apr-2014
        $related_products = array();
        if ($settings_array['supercheckout']['shopping_cart']['show'] == 'related_product') {
            $i = 0;
            // This array will be used to get the related and cross-sell products list if there are no mappped products with cart products
            $productNameArr = array();
            $j = 0;

            foreach ($cart->getAllVisibleItems() as $item) {
                $productID = $item->getProductId();

                $product_name = explode(" ", $item->getProduct()->getName());
                foreach ($product_name as $str) {
                    if (strlen($str) >= 3) {
                        $productNameArr[$j] = array('attribute' => 'name', 'like' => '%' . $str . '%');
                    }
                    $j++;
                }

                // Related products

                if (count($related_products) < 4) {
                    $related_prods = $item->getProduct()->getRelatedProductCollection();
                    $related_prods->addFieldToFilter('entity_id', array('nin' => $notInProductIds));
                    $related_prods->addAttributeToFilter('visibility', $visibility);
                    $related_prods->getSelect()->limit(4);

                    foreach ($related_prods as $related) {
                        if (count($related_products) < 4) {
                            $_product = Mage::getModel('catalog/product')->load($related->entity_id);
                            $thumbnail = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->resize(100, 120);
                            $related_products[$i] = array('name' => $_product->getName(),
                                'price' => $_product->getFinalPrice(),
                                'href' => $_product->getProductUrl(),
                                'thumb' => $thumbnail,
                            );

                            array_push($notInProductIds, $related->entity_id);

                            unset($_product);
                            $i++;
                        }
                    }
                    unset($related_prods);
                    unset($related);
                }
                // Related products

                // Cross sell products
                if (count($related_products) < 4) {
                    $remaining_products = 4 - count($related_products);
                    $cross_sells = $item->getProduct()->getCrossSellProducts();

                    foreach ($cross_sells as $related) {
                        if (is_int(array_search($related->entity_id, $notInProductIds))) {
                            // do nothing
                        } else {
                            $_product = Mage::getModel('catalog/product')->load($related->entity_id);
                            if (is_int(array_search($_product->getVisibility(), $visibility))) {
                                $thumbnail = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->resize(100, 120);
                                $related_products[$i] = array('name' => $_product->getName(),
                                    'price' => $_product->getFinalPrice(),
                                    'href' => $_product->getProductUrl(),
                                    'thumb' => $thumbnail,
                                );

                                array_push($notInProductIds, $related->entity_id);
                                unset($_product);
                                $i++;
                            }
                        }
                        if (count($related_products) == 4) {
                            break;
                        }
                    }
                    unset($cross_sells);
                    unset($related);
                }
            }
            // Cross sell products
            // Related Product by name
            // If related product array length is not equal to 4 then find the related products similar to product's name in cart
            if (count($related_products) < 4) {
                $remaining_products = 4 - count($related_products);

                $products = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToSelect(array('name', 'price', 'product_url', 'small_image'))
                                ->addAttributeToFilter($productNameArr)
                                ->addAttributeToFilter('entity_id', array('nin' => $notInProductIds))
                                ->addAttributeToFilter('visibility', $visibility)
                                ->setPage(1, $remaining_products)->load();

                foreach ($products as $_product) {
                    $thumbnail = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->resize(100, 120);
                    $related_products[$i] = array('name' => $_product->getName(),
                        'price' => $_product->getFinalPrice(),
                        'href' => $_product->getProductUrl(),
                        'thumb' => $thumbnail,
                    );

                    unset($_product);
                    $i++;
                }
            }
            // Related Product by name
            // Reated product using categories
            if (count($related_products) < 4) {
                if (count($productCategories) > 0) {
                    $remaining_products = 4 - count($related_products);

                    $ver_info = Mage::getVersionInfo();
                    $mag_version = $ver_info['major'] . $ver_info['minor'] . $ver_info['revision'] . $ver_info['patch'];
                    foreach ($productCategories as $categoryID) {
                        $category = Mage::getModel('catalog/category')->load($categoryID);
                        $products = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addCategoryFilter($category, true)
                                ->addAttributeToSelect(array('name', 'price', 'product_url', 'small_image'))
                                ->addFieldToFilter('entity_id', array('nin' => $notInProductIds))
                                ->addAttributeToFilter('visibility', $visibility);
                        $products->getSelect()->limit($remaining_products);

                        if ($products->count() > 0) {
                            foreach ($products as $_product) {
                                $thumbnail = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->resize(100, 120);
                                $related_products[$i] = array('name' => $_product->getName(),
                                    'price' => $_product->getPrice(),
                                    'href' => $_product->getProductUrl(),
                                    'thumb' => $thumbnail,
                                );

                                if (count($related_products) == 4) {
                                    break;
                                }
                                $i++;
                            }
                        }
                        if (count($related_products) == 4) {
                            break;
                        }
                    }
//                            $collection = Mage::getModel('catalog/product')
//                                            ->getCollection()
//                                            ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
//                                            ->addAttributeToSelect(array('name', 'price', 'product_url', 'small_image'))
//                                            ->addAttributeToFilter('category_id', array('in' => $productCategories))
//                                            ->addFieldToFilter('entity_id', array('nin' => $notInProductIds))
//                                            ->addAttributeToFilter('visibility', $visibility)
//                                            ->setPage(1, $remaining_products)->load();
//                            foreach ($collection as $_product) {
//                                $thumbnail = (string) Mage::helper('catalog/image')->init($_product, 'small_image')->resize(100, 120);
//                                $related_products[$i] = array('name' => $_product->getName(),
//                                    'price' => $_product->getPrice(),
//                                    'href' => $_product->getProductUrl(),
//                                    'thumb' => $thumbnail,
//                                );
//                                $i++;
//                            }
                }
            }
            // Reated product using categories
        }
        // EOC - Code to get the related products list - Brajendra - 02-Apr-2014

        echo json_encode(array("cartProductsCount" => $itemsCount, "cartProducts" => $productArray, "cartTotal" => $cartTotal, "relatedProducts" => $related_products, "messages" => $msgs));
		exit;
    }

    /**
     * Function to update the cart product's quantity
     * Created By: Brajendra
     */
    public function updateCartAction() {
        //Mage::setIsDeveloperMode(true);

        $msgs = array();
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->product_id)) {
            $arr = array($data->product_id => Array('qty' => $data->qty));
            $this->getRequest()->setParam('cart', $arr);

            $ver_info = Mage::getVersionInfo();
            $mag_version = $ver_info['major'] . $ver_info['minor'] . $ver_info['revision'] . $ver_info['patch'];

            switch ($mag_version) {
                case '1600':
                case '1620':
                case '1510':
                case '1501':
                    try {
                        $cartData = $this->getRequest()->getParam('cart');
                        if (is_array($cartData)) {
                            $filter = new Zend_Filter_LocalizedToNormalized(
                                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
                            );
                            foreach ($cartData as $index => $data) {
                                if (isset($data['qty'])) {
                                    $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                                }
                            }
                            $cart = $this->_getCart();
                            if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                                $cart->getQuote()->setCustomerId(null);
                            }

                            $cartData = $cart->suggestItemsQty($cartData);
                            $cart->updateItems($cartData)
                                    ->save();
                            $msgs['success'] = $this->__('Your cart has been updated.');
                        }
                        $this->_getSession()->setCartWasUpdated(true);
                    } catch (Mage_Core_Exception $e) {
                        $msgs['success'] = $e->getMessage();
                    } catch (Exception $e) {
                        $msgs['success'] = $this->__('Cannot update shopping cart.');
                        Mage::logException($e);
                    }
                    break;
                default:
                    $this->getRequest()->setParam('update_cart_action', $data->update_cart_action);
                    try {
                        $this->_updateShoppingCart();
                        $msgs['success'] = $this->__('Your cart has been updated.');
                    } catch (Mage_Core_Exception $e) {
                        $error = $this->__(Mage::helper('core')->escapeHtml($e->getMessage()));
                    } catch (Exception $e) {
                        $msgs['success'] = $this->__('Cannot update shopping cart.');
                        Mage::logException($e);
                    }
                    break;
            }
        }
        $this->getCartAction($msgs);
    }

    /**
     * Function to apply a coupon on cart
     * Created By: Brajendra
     */
    public function couponPostAction() {
        $msgs = array();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->ajax)) {
            parent::couponPostAction();
            return;
        }
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $data->coupon_code;
        if ($data->remove == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }

        try {
            $codeLength = strlen($couponCode);
            // This line is for Magento 1.7.0.2-1.6.0.0 version because COUPON_CODE_MAX_LENGTH variable is defined
            //$isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
            $isCodeLengthValid = $codeLength && $codeLength <= 255;

            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                    ->collectTotals()
                    ->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode()) {
                    $msgs['success'] = $this->__('Coupon code "%s" has been applied.', Mage::helper('core')->escapeHtml($couponCode));
                } else {
                    $msgs['success'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
                }
            } else {
                $msgs['success'] = $this->__('Coupon code has been canceled.');
            }
        } catch (Mage_Core_Exception $e) {
            $msgs['success'] = $e->getMessage();
        } catch (Exception $e) {
            $msgs['success'] = $this->__('Cannot apply the coupon code.');
            Mage::logException($e);
        }

        $this->getCartAction($msgs);
    }

    /**
     * Function to remove product from shopping cart
     * Created By: Brajendra
     */
    public function deleteAction() {
        $msgs = array();
        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                        ->save();
                $msgs['success'] = $this->__('Product has been removed from cart.');
            } catch (Exception $e) {
                $msgs['success'] = $this->__('Cannot remove the item.');
                Mage::logException($e);
            }
        }
        $this->getCartAction($msgs);
    }

}
?>