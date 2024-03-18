<?php
class Custom_Productupdate_PriceSkuController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    	$products = Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToFilter('status', 1);//enabled
        //$products->addAttributeToFilter('visibility', 4);//catalog, search
        //$products->addAttributeToSort('name', 'ASC');
        $products->addAttributeToSelect('*');
        
        /*----- 07-06-2019 ----- */
        // call API for currency conversion rate
        $date = new DateTime();
        $_dateBefore = $date->modify("-1 day");
        $date2 = $_dateBefore->format('Y-m-d');
        $date3 = explode("-",$date2);
        $year = $date3[0];
        $month = $date3[1];
        $day = $date3[2];
        //exit;
        //$time_stmp = gmdate("Y-m-d\TH:i:s\Z", strtotime('-48 hours', time()));
        
        // database table connection
        $resource     = Mage::getSingleton('core/resource');
        $writeAdapter = $resource->getConnection('core_write');
        $table        = $resource->getTableName('store_coversion_rate');
        
        // for dolor
        $_apiCurrency = "dolar";
        $dolar_api = "https://api.sbif.cl/api-sbifv3/recursos_api/".$_apiCurrency."/".$year."/".$month."/dias/".$day."?apikey=69536bf6d9eb5bbcf69655411edf08295a60f2d6&formato=xml";
        //echo $dolar_api;
        $xml = simplexml_load_file($dolar_api);
        //print_r($xml);
        $val = $xml->Dolares->Dolar->Valor;
        $valor_price = str_replace(',','.',$val);
        
        // save currency coversion rate in a table
        $query = "INSERT INTO {$table} (`rate_id`,`date_time`,`currency`,`convertion_price`) VALUES ('', '$date2', '$_apiCurrency', '$valor_price');";
        //echo $query;
        $writeAdapter->query($query);
        
        // for euro
        $_apiCurrencyE = "euro";
        $euro_api = "https://api.sbif.cl/api-sbifv3/recursos_api/".$_apiCurrencyE."/".$year."/".$month."/dias/".$day."?apikey=69536bf6d9eb5bbcf69655411edf08295a60f2d6&formato=xml";
        //echo $euro_api;
        $xml2 = simplexml_load_file($euro_api);
        //print_r($xml);
        $val2 = $xml2->Euros->Euro->Valor;
        $valor_price2 = str_replace(',','.',$val2);
        
        // save currency coversion rate in a table
        $query2 = "INSERT INTO {$table} (`rate_id`,`date_time`,`currency`,`convertion_price`) VALUES ('', '$date2', '$_apiCurrencyE', '$val2');";
        //echo $query2;
        $writeAdapter->query($query2);
        
        // for UF
        $_apiCurrencyUF = "uf";
        $uf_api = "https://api.sbif.cl/api-sbifv3/recursos_api/".$_apiCurrencyUF."/".$year."/".$month."/dias/".$day."?apikey=69536bf6d9eb5bbcf69655411edf08295a60f2d6&formato=xml";
        //echo $uf_api;
        $xml3 = simplexml_load_file($uf_api);
        //print_r($xml);
        $val3 = $xml3->UFs->UF->Valor;
        //$valor_price = str_replace(',','.',$val);
        
        // save currency coversion rate in a table
        $query3 = "INSERT INTO {$table} (`rate_id`,`date_time`,`currency`,`convertion_price`) VALUES ('', '$date2', '$_apiCurrencyUF', '$val3');";
        //echo $query3;
        $writeAdapter->query($query3);
        
        // for UTM
        $_apiCurrencyUTM = "utm";
        //$utm_api = "https://api.sbif.cl/api-sbifv3/recursos_api/".$_apiCurrencyUTM."/".$year."/".$month."/dias/".$day."?apikey=69536bf6d9eb5bbcf69655411edf08295a60f2d6&formato=xml";
        $utm_api = "https://api.sbif.cl/api-sbifv3/recursos_api/utm?apikey=69536bf6d9eb5bbcf69655411edf08295a60f2d6&formato=xml";
        //echo $utm_api;
        $xml4 = simplexml_load_file($utm_api);
        //print_r($xml);
        $val4 = $xml4->UTMs->UTM->Valor;
        $valor_price4 = str_replace('.','',$val4);
        
        // save currency coversion rate in a table
        $query4 = "INSERT INTO {$table} (`rate_id`,`date_time`,`currency`,`convertion_price`) VALUES ('', '$date2', '$_apiCurrencyUTM', '$val4');";
        //echo $query4;
        $writeAdapter->query($query4);
        
        //
        foreach($products as $product){
            $_proId = $product->getId();
            $_product = Mage::getModel('catalog/product')->load($_proId);
            
            echo $sku = $_product->getSku();
            echo '->'.$_proId;
            
            /*----- 07-06-2019 ----- */
            $api_key = Mage::getStoreConfig('productupdate/productupdate_group/apiKey');
            $access_key = Mage::getStoreConfig('productupdate/productupdate_group/accessKey');
            $time_stmp = gmdate("Y-m-d\TH:i:s\Z");
            //$string = $api_key.",".$access_key.",".$time_stmp;
            $string = $api_key.",".$access_key.",";
            //$signature = hash('sha256', '5119728a-7d9a-4dc0-9733-0dddd7d8207b,e56ad576-9572-43ba-ab04-6b79724b21b9,'.$time_stmp);
            $signature = hash('sha256', $string.$time_stmp);
            //exit;
            //echo $api_url = 'https://intcomex-test.apigee.net/v1/getproduct?&apiKey=5119728a-7d9a-4dc0-9733-0dddd7d8207b&utcTimeStamp=2019-06-06T00:50:40.982Z&signature=8a4b8a74a4961f1251f41e9a2696db616185b0830eddad6aa8dc9948207b1d9c&sku='.$sku.'&locale=es';
            $api_url = 'https://intcomex-test.apigee.net/v1/getproduct?locale=es&apiKey='.$api_key.'&utcTimeStamp='.$time_stmp.'&signature='.$signature.'&sku='.$sku;
            //echo $api_url; exit;
            //echo '<pre>';
            /*----- 07-06-2019 ----- */
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $api_url);
            $result = curl_exec($ch);
            curl_close($ch);
            
            $get_prod_array = json_decode($result, true);
            //print_r($get_prod_array);
            $price = $get_prod_array['Price']['UnitPrice'];
            $mpn = $get_prod_array['Mpn'];
            $description = $get_prod_array['Description'];
            $manufacturerId = $get_prod_array['Manufacturer']['ManufacturerId'];
            $manufacturer = $get_prod_array['Manufacturer']['Description'];
            $brandId = $get_prod_array['Brand']['BrandId'];
            $brand = $get_prod_array['Brand']['Description'];
            $model = $get_prod_array['Model'];
            
            //echo $valor_price;
            if($price && $valor_price){
                //echo "price=" .$price = 2;
                //echo "valor_price=" .$valor_price = 50;
                $_convertPrice = $price * $valor_price;
                $_priceCat = $_product->getAttributeText('price_category');
                $_priceCat1 = explode("-", $_priceCat);
                $_priceCat2 = $_priceCat1[1];
                $_priceCat3 = explode("%", $_priceCat2);
                $_profitAmt = $_priceCat3[0];
                $_profitAmt = $_profitAmt/100;
                $_finalPrice = $_convertPrice/(1 - $_profitAmt);
                //exit;

                $product->setCost($_convertPrice);
                $product->setPrice($_finalPrice);
            }
            if($mpn){
                $product->setProductMpn($mpn);
            }
            if($description){
                $product->setDescription($description);
            }
            if($manufacturer){
                $product->setManufacturerNew($manufacturer);
            }
            if($brand){
                $product->setBrand($brand);
            }
            if($brand){
                $product->setModel($model);
            }
            
            $product->save();
            
            /*----- 07-06-2019 ----- */
            
            $stock = $get_prod_array['InStock'];
            $stockBackendval = Mage::getStoreConfig('cataloginventory/options/stock_backend_value');
            $_insertStock = ($stock*$stockBackendval)/100;
            $stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($_proId);
            $stockItemId = $stockItem->getId();
            /*----- 07-06-2019 ----- */
            if($_insertStock != 0){
                $stockItem->setData('is_in_stock', 1);
            }else{
                $stockItem->setData('is_in_stock', 0);
            }
            /*----- 07-06-2019 ----- */
            $stockItem->setData('qty', (integer)$_insertStock);
            $stockItem->save();
            
            echo '<hr>';
            
        }
        echo '<br>Completed';
    
    }
    
    public function addproductAction(){
        // database table connection
        $resource     = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeAdapter = $resource->getConnection('core_write');
        $table        = $resource->getTableName('addproduct');
        //echo "here";
        
        // -------------------------------------------------------------------------------
        $api_key = Mage::getStoreConfig('productupdate/productupdate_group/apiKey');
        $access_key = Mage::getStoreConfig('productupdate/productupdate_group/accessKey');
        $time_stmp = gmdate("Y-m-d\TH:i:s\Z");
        $string = $api_key.",".$access_key.",";
        $signature = hash('sha256', $string.$time_stmp);
        //$api_url = 'https://intcomex-test.apigee.net/v1/getcatalog?locale=es&inventoryFilter=InStock&apiKey='.$api_key.'&utcTimeStamp='.$time_stmp.'&signature='.$signature;
        $api_url = 'https://intcomex-prod.apigee.net/v1/getcatalog?locale=es&inventoryFilter=InStock&apiKey='.$api_key.'&utcTimeStamp='.$time_stmp.'&signature='.$signature;
        //echo $api_url; die();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $api_url);
        $result = curl_exec($ch);
        curl_close($ch);
        $get_prod_array = json_decode($result, true);
        //echo "<pre>"; print_r($get_prod_array);
        //echo $get_prod_array['fault']['detail']['errorcode']; die;
        $_apiError = $get_prod_array['fault']['detail']['errorcode'];
        if($_apiError == "messaging.adaptors.http.flow.GatewayTimeout"){
            $log_time = date('Y-m-d h:i:sa');
            $log_msg = "************** Start Log For Day : '" . $log_time . "'**********";
            $log_filename = "/var/www/html/dev7/var/log/intcomex.log";
            if (!file_exists($log_filename)){
                // create directory/folder uploads.
                mkdir($log_filename, 0777, true);
            }
            $log_file_data = $log_msg."\n".$_apiError;
            file_put_contents($log_filename, $log_file_data . "\n", FILE_APPEND);
        }else{
            $num_rows = count($get_prod_array);
            $_count = 0;
            foreach($get_prod_array as $product){
                $name = substr($product['Description'],0,20);
                $description = $product['Description'];
                $short_description = $product['Description'];
                $sku = $product['Sku'];
                $weight = $product['Freight']['Item']['Weight'];
                $price = $product['Price']['UnitPrice'];
                $qty = $product['InStock'];
                
                // save products
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                $products = Mage::getModel('catalog/product');
                try{
                    $_count++;
                    $products
                        ->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
                        ->setAttributeSetId(4) //ID of a attribute set named 'default'
                        ->setTypeId('simple') //product type
                        ->setCreatedAt(strtotime('now')) //product creation time
                        ->setSku($sku) //SKU
                        ->setName($name) //product name
                        ->setWeight($weight)
                        ->setStatus(1) //product status (1 - enabled, 2 - disabled)
                        ->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                        ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
                        ->setPrice($price) //price in form 11.22
                        ->setDescription($description)
                        ->setShortDescription($short_description)
                        ->setStockData(array(
                                           'is_in_stock' => 1, //Stock Availability
                                           'qty' => $qty //qty
                                       )
                        )
                        ->setCategoryIds(array(45)); //assign product to categories
                        //die('bbb');
                    $products->save();
                                        
                    // save product sku and description in a table
                    $query = "INSERT INTO {$table} (`id`,`sku`,`description`) VALUES (NULL, '$sku', '$description');";
                    //echo $query;
                    //die('query');
                    //$writeAdapter->query($query);
                }catch(Exception $e){
                    Mage::log($e->getMessage());
                }
                //if($_count >= 30){break;}
            }
            
            // mail to admin
            $content = 'Estimados,<br>Estos nuevos productos han sido creados en el sitio web:';
            $content .= '<ul>
                            <li>SKU</li>
                            <li> | </li>
                            <li>Description</li>';
            $query2 = 'SELECT * FROM ' . $table;
            $results = $readConnection->query($query2);
            while($row = $results->fetch()){
                $content .= '<li>'.$row['sku'].'</li>
                            <li> | </li>
                            <li>'.$row['description'].'</li>';
            }
            $content .= '</ul>';
            $content .= 'Por favor complete la información en el panel de administración<br>Gracias';
            //echo $content; die();
            //$mailto = "chasupriya@gmail.com";
            $mailto = Mage::getStoreConfig('trans_email/ident_general/email');
            $subject = "New product(s) added";
            
            $header = "MIME-Version: 1.0\r\n";
            $header .= "Content-type:text/html; charset=UTF-8-1\r\n";
            //$header .= "From: <webmaster@example.com> \r\n";
            
            try {
                mail($mailto, $subject, $content, $header);
                //echo "mail sent successfully";
            } catch (Exception $e) {
                //echo "Unable to send mail!!";
            }
            
            //write into log file--------------------------------------
            //$_productCount = count($_saveCount);
            $log_time = date('Y-m-d h:i:sa');
            $log_msg = "************** Start Log For Day : '" . $log_time . "'**********<br>";
            $log_msg .= $_count ."Product(s) added<br>";
            $log_msg .= "Mail sent successfully";
            $log_filename = "/var/www/html/dev7/var/log/intcomex.log";
            if (!file_exists($log_filename)){
                // create directory/folder uploads.
                mkdir($log_filename, 0777, true);
            }
            $log_file_data = $log_msg;
            file_put_contents($log_filename, $log_file_data . "\n", FILE_APPEND);
        }
    }
}