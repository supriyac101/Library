<?php
    namespace Custom\Mobileapp\Model;
    use Custom\Mobileapp\Api\HomeInterface;
    
    class Home implements HomeInterface{
        /**
        * Returns greeting message to user
        *
        * @api
        * @param json.
        * @return json Greeting message with json.
        */
        
        public function homepage(){
            $results = array();
            
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
            $currentStore = $storeManager->getStore();
            
            $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            
            $banner = array(
                            array(
                                "banner_id" => 1,
                                "banner_img_url" => $mediaUrl."wysiwyg/magebig/slider/slider1.jpg"
                            ),
                            array(
                                "banner_id" => 2,
                                "banner_img_url" => $mediaUrl."wysiwyg/magebig/slider/slider2.jpg"
                            ),
                            array(
                                "banner_id" => 3,
                                "banner_img_url" => $mediaUrl."wysiwyg/magebig/slider/slider3.jpg"
                            )
                        );
            
            $menuArray = [];
            $categoryArray = [];
            $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
            $categoryHelper = $objectManager->get('\Magento\Catalog\Helper\Category');
            $categories = $categoryHelper->getStoreCategories();
            
            /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            // get list of all the categories
            $categories = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')->create();
            $categories->addAttributeToSelect('*');*/
            
            $category = 0;
            foreach ($categories as $category) {
                //return print_r($category->getData());
                //return $category->getName();
                $thisMenuData['menu_id'] = $category->getId();
                $thisMenuData['menu_name'] = $category->getName();
                $thisMenuData['menu_url'] = $category->getRequestPath();
                $thisMenuData['menu_icon'] = $category->getImageUrl();
                
                $thisCategoryData['category_id'] = $category->getId();
                $thisCategoryData['category_name'] = $category->getName();
                $thisCategoryData['category_url'] = $category->getRequestPath();
                $thisCategoryData['category_image_url'] = $category->getImageUrl();
                
                $menuArray[] = $thisMenuData;
                $categoryArray[] = $thisCategoryData;
                $category++;
            }
            
            $productsArray = [];
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
            $collection = $productCollection->create()
                        ->addAttributeToSelect('*')
                        ->load();
            
            foreach ($collection as $product){
                //return(print_r($product->getData()));
                $thisProductData['item_id'] = $product->getEntityId();
                $thisProductData['item_image_url'] = $product->getImage();
                $thisProductData['item_name'] = $product->getName();
                $thisProductData['actual_price'] = $product->getPrice();
                $thisProductData['offer_price'] = $product->getSpecialPrice();
                $thisProductData['rating'] = $product->getRating();
                
                $productsArray[] = $thisProductData;
            }  
            
            $results['banner'] = $banner;
            $results['menu'] = $menuArray;
            $results['category'] = $categoryArray;
            $results['recomended'] = $productsArray;
            
            return json_encode($results);
            //return $results;
        }
    }
?>











