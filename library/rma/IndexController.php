<?php
class Custom_Rmaform_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/rmaform?id=15 
    	 *  or
    	 * http://site.com/rmaform/id/15 	
    	 */
    	/* 
		$rmaform_id = $this->getRequest()->getParam('id');

  		if($rmaform_id != null && $rmaform_id != '')	{
			$rmaform = Mage::getModel('rmaform/rmaform')->load($rmaform_id)->getData();
		} else {
			$rmaform = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($rmaform == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$rmaformTable = $resource->getTableName('rmaform');
			
			$select = $read->select()
			   ->from($rmaformTable,array('rmaform_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$rmaform = $read->fetchRow($select);
		}
		Mage::register('rmaform', $rmaform);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function postAction(){
        //echo "hello";
        
        $post = $this->getRequest()->getPost();
        if($post){
            // access
            $secretKey = '';
            $captcha = $post['g-recaptcha-response'];
    
            /*if(!$captcha){
                echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
                exit;
            }*/
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);
    
            //if(intval($responseKeys["success"]) !== 1) {
              //echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
            //} else {
                //echo "<pre>";
                //print_r($_FILES);
                //exit;
                $company = $post['company'];
                $_businessName = $post['business_name'];
                $_billingAddress = $post['billing_address'];
                $_dispatchAddress = $post['dispatch_address'];
                $city = $post['city'];
                $email = $post['email'];
                $name = $post['name'];
                $_purchaseOrder = $post['purchase_order'];
                $telephone = $post['telephone'];
                $content = $post['content'];
                $status = $post['status'];
                
                // database table connection
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $writeAdapter = $resource->getConnection('core_write');
                $table = $resource->getTableName('rmaform_data');
                $table2 = $resource->getTableName('rma_item');
                
                // save rma form data in table
                $query = "INSERT INTO {$table} (`rma_id`,`company`,`business_name`,`billing_address`,`dispatch_address`,`city`,`email`,`name`,`purchase_order`,`telephone`,`content`,`status`) VALUES ('', '$company','$_businessName','$_billingAddress','$_dispatchAddress','$city','$email','$name','$_purchaseOrder','$telephone','$content','$status');";
                //echo $query;
                //exit;
                if($writeAdapter->query($query)){
                    $_rmaQuery = "SELECT `rma_id` FROM {$table} ORDER BY `rma_id` DESC LIMIT 0,1";
                    //echo $_rmaQuery; exit;
                    $_rmaformId = $readConnection->fetchOne($_rmaQuery);
                    //echo $_rmaformId; exit;
                    $sql_start = "INSERT INTO {$table2} (`id`,`rmaform_id`,`itemid`,`invoice`,`product_name`,`quantity`,`type_of_fault`,`filename`) VALUES ";
                    $sql_array = array();
                    foreach($post['itemid'] as $key => $_item){
                        $_itemId = $_item;
                        $_invoice = $post['invoice'][$key];
                        $_productName = $post['product_name'][$key];
                        $_quantity = $post['quantity'][$key];
                        $_typeOffault = $post['type_of_fault'][$key];
                        $_fileName = $_FILES['image']['name'][$key]; //exit;
                        
                        $sql_array[] = "('','".$_rmaformId."', '".$_itemId."', '".$_invoice."', '".$_productName."', '".$_quantity."', '".$_typeOffault."','".$_fileName."');";
                        
                        $query_single = $sql_start . implode(', ', $sql_array);
                        //echo $query_single;
                        $writeAdapter->query($query_single);
                        
                        if(isset($_FILES['image']['name'][$key]) && $_FILES['image']['name'][$key] != '') {
                            try {
                                /* Starting upload */	
                                //$uploader = new Varien_File_Uploader('image');
                                $uploader = new Varien_File_Uploader(
                                    array(
                                        'name' => $_FILES['image']['name'][$key],
                                        'type' => $_FILES['image']['type'][$key],
                                        'tmp_name' => $_FILES['image']['tmp_name'][$key],
                                        'error' => $_FILES['image']['error'][$key],
                                        'size' => $_FILES['image']['size'][$key]
                                    )
                                );
                                // Any extention would work
                                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                                $uploader->setAllowRenameFiles(false);
                                
                                // Set the file upload mode 
                                // false -> get the file directly in the specified folder
                                // true -> get the file in the product like folders 
                                //	(file.jpg will go in something like /media/f/i/file.jpg)
                                $uploader->setFilesDispersion(false);
                                        
                                // We set media as the upload dir
                                $path = Mage::getBaseDir('media') . DS ;
                                $uploader->save($path, $_FILES['image']['name'][$key] );
                                
                            } catch (Exception $e) {
                          
                            }
                        
                            //this way the name is saved in DB
                            $post['image'][$key] = $_FILES['image']['name'][$key];
                        }
                    }
                }
                return $this->sendmailAction();
            //}
        }
    }
    
    public function sendmailAction(){
		$post = $this->getRequest()->getPost();
        if($post){
            $company = $post['company'];
            $_businessName = $post['business_name'];
            $_billingAddress = $post['billing_address'];
            $_dispatchAddress = $post['dispatch_address'];
            $city = $post['city'];
            $email = $post['email'];
            $name = $post['name'];
            $_purchaseOrder = $post['purchase_order'];
            $telephone = $post['telephone'];
            $content = $post['content'];
            
            $_emailContent = '<ul>
                                <li>'.$company.'</li>
                                <li>'.$_businessName.'</li>
                                <li>'.$_billingAddress.'</li>
                                <li>'.$_dispatchAddress.'</li>
                                <li>'.$city.'</li>
                                <li>'.$email.'</li>
                                <li>'.$name.'</li>
                                <li>'.$_purchaseOrder.'</li>
                                <li>'.$telephone.'</li>
                                <li>'.$content.'</li>
                            </ul>';
            //$uid = md5(uniqid(time()));
            
            $mailto = "chasupriya@gmail.com";
            //$mailto = Mage::getStoreConfig('trans_email/ident_general/email');
            $subject = "RMA Form Data";
            //$message = "Hello \r\n".$toname."\r\n\r\n".$toadd."\r\n".$tocomment."\r\nPlease find the attachment.";
            //$message = "Hello";
            
            $header = "From: ".$email."\r\n";
            $header .= "Reply-To: ".$email."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
                        
            /*if (mail($mailto, $subject, "", $header)) {
                echo "mail send ... OK"; // or use booleans here
            } else {
                echo "mail send ... ERROR!";
            }*/
            $response['success'] = "Your form has been saved";
            $response['error'] = "Unable to save the form";
            
            try {
                mail($mailto, $subject, $_emailContent, $header);
                //$mpdf->Output();
                //return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
                $url = "http://mage.ecomsoft.co.in/comercialagustin/rma-new";
                Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            } catch (Exception $e) {
                $url = "http://mage.ecomsoft.co.in/comercialagustin/rma-new";
                Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            }
        }
	}
    
    public function postopt1Action(){
        //echo "hello";
        
        $post = $this->getRequest()->getPost();
        if($post){
            // access
            $secretKey = '';
            $captcha = $post['g-recaptcha-response'];
    
            /*if(!$captcha){
                echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
                exit;
            }*/
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);
    
            //if(intval($responseKeys["success"]) !== 1) {
                //echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
            //} else {
                //echo "<pre>";
                //print_r($_FILES);
                //exit;
                $_formOption = $post['form_option'];
                $_typeOffault = $post['type_of_fault'];
                $company = $post['company'];
                $name = $post['name'];
                $telephone = $post['telephone'];
                $email = $post['email'];
                $invoice = $post['invoice'];
                $_purchaseOrder = $post['purchase_order'];
                
                
                // database table connection
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $writeAdapter = $resource->getConnection('core_write');
                $table = $resource->getTableName('rmaoption_form');
                                
                // save rma form data in table
                $query = "INSERT INTO {$table} (`id`,`form_option`,`type_of_fault`,`company`,`name`,`telephone`,`email`,`invoice`,`purchase_order`) VALUES ('','$_formOption','$_typeOffault','$company','$name','$telephone','$email','$invoice','$_purchaseOrder');";
                //echo $query;
                //exit;
                if($writeAdapter->query($query)){
                    $_emailContent = '<ul>
                                        <li>'.$_formOption.'</li>
                                        <li>'.$company.'</li>
                                        <li>'.$name.'</li>
                                        <li>'.$telephone.'</li>
                                        <li>'.$email.'</li>
                                        <li>'.$invoice.'</li>
                                        <li>'.$_purchaseOrder.'</li>
                                    </ul>';
                    
                    $mailto = "chasupriya@gmail.com";
                    //$mailto = Mage::getStoreConfig('trans_email/ident_general/email');
                    $subject = "RMA Form Data";
                    
                    $header = "MIME-Version: 1.0\r\n";
                    $header .= "Content-type:text/html; charset=UTF-8-1\r\n";
                    $header .= "From: ".$email."\r\n";
                    $header .= "Reply-To: ".$email."\r\n";
                                
                    try {
                        mail($mailto, $subject, $_emailContent, $header);
                        $url = "http://mage.ecomsoft.co.in/comercialagustin/rmav2";
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    } catch (Exception $e) {
                        $url = "http://mage.ecomsoft.co.in/comercialagustin/rmav2";
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    }
                }
            //}
        }
    }
    
    public function postopt2Action(){
        //echo "hello";
        
        $post = $this->getRequest()->getPost();
        if($post){
            // access
            $secretKey = '';
            $captcha = $post['g-recaptcha-response'];
    
            /*if(!$captcha){
                echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
                exit;
            }*/
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);
    
            //if(intval($responseKeys["success"]) !== 1) {
              //echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
            //} else {
                //echo "<pre>";
                //print_r($_FILES);
                //exit;
                $_formOption = $post['form_option'];
                $_typeOffault = $post['type_of_fault'];
                $company = $post['company'];
                $name = $post['name'];
                $telephone = $post['telephone'];
                $email = $post['email'];
                $_purchaseOrder = $post['purchase_order'];
                
                
                // database table connection
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $writeAdapter = $resource->getConnection('core_write');
                $table = $resource->getTableName('rmaoption_form');
                                
                // save rma form data in table
                $query = "INSERT INTO {$table} (`id`,`form_option`,`company`,`name`,`telephone`,`email`,`purchase_order`) VALUES ('','$_formOption','$company','$name','$telephone','$email','$_purchaseOrder');";
                //echo $query;
                //exit;
                if($writeAdapter->query($query)){
                    $_emailContent = '<ul>
                                        <li>'.$_formOption.'</li>
                                        <li>'.$company.'</li>
                                        <li>'.$name.'</li>
                                        <li>'.$telephone.'</li>
                                        <li>'.$email.'</li>
                                        <li>'.$_purchaseOrder.'</li>
                                    </ul>';
                    
                    $mailto = "chasupriya@gmail.com";
                    //$mailto = Mage::getStoreConfig('trans_email/ident_general/email');
                    $subject = "RMA Form Data";
                    
                    $header = "MIME-Version: 1.0\r\n";
                    $header .= "Content-type:text/html; charset=UTF-8-1\r\n";
                    $header .= "From: ".$email."\r\n";
                    $header .= "Reply-To: ".$email."\r\n";
                                
                    try {
                        mail($mailto, $subject, $_emailContent, $header);
                        $url = "http://mage.ecomsoft.co.in/comercialagustin/rmav2";
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    } catch (Exception $e) {
                        $url = "http://mage.ecomsoft.co.in/comercialagustin/rmav2";
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    }
                }
            //}
        }
    }
    
    public function postopt3Action(){
        //echo "hello";
        
        $post = $this->getRequest()->getPost();
        if($post){
            // access
            $secretKey = '';
            $captcha = $post['g-recaptcha-response'];
    
            /*if(!$captcha){
                echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
                exit;
            }*/
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);
    
            //if(intval($responseKeys["success"]) !== 1) {
              //echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
            //} else {
                //echo "<pre>";
                //print_r($_FILES);
                //exit;
                $_formOption = $post['form_option'];
                $_reasonForRequest = $post['reason_for_request'];
                $company = $post['company'];
                $bill = $post['bill'];
                $_billFilename = $_FILES['bill_filename']['name'];
                $email = $post['email'];
                $_purchaseOrder = $post['purchase_order'];
                
                // database table connection
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $writeAdapter = $resource->getConnection('core_write');
                $table = $resource->getTableName('rmaoption_form');
                                
                // save rma form data in table
                $query = "INSERT INTO {$table} (`id`,`form_option`,`reason_for_request`,`company`,`bill`,`bill_filename`,`email`,`purchase_order`) VALUES ('','$_formOption','$_reasonForRequest','$company','$bill','$_billFilename','$email','$_purchaseOrder');";
                //echo $query;
                //exit;
                if($writeAdapter->query($query)){
                    // bill attachment upload
                    if(isset($_FILES['bill_filename']['name']) && $_FILES['bill_filename']['name'] != '') {
                        try {
                            /* Starting upload */	
                            $uploader = new Varien_File_Uploader('bill_filename');
                            /*$uploader = new Varien_File_Uploader(
                                array(
                                    'name' => $_FILES['image']['name'][$key],
                                    'type' => $_FILES['image']['type'][$key],
                                    'tmp_name' => $_FILES['image']['tmp_name'][$key],
                                    'error' => $_FILES['image']['error'][$key],
                                    'size' => $_FILES['image']['size'][$key]
                                )
                            );*/
                            // Any extention would work
                            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                            $uploader->setAllowRenameFiles(false);
                            
                            // Set the file upload mode 
                            // false -> get the file directly in the specified folder
                            // true -> get the file in the product like folders 
                            //	(file.jpg will go in something like /media/f/i/file.jpg)
                            $uploader->setFilesDispersion(false);
                                    
                            // We set media as the upload dir
                            $path = Mage::getBaseDir('media') . DS .'rmaform';
                            $uploader->save($path, $_FILES['bill_filename']['name']);
                            
                        } catch (Exception $e) {
                      
                        }
                    
                        //this way the name is saved in DB
                        $post['bill_filename'] = $_FILES['bill_filename']['name'];
                    }
                    
                    // mail send
                    $_emailContent = '<ul>
                                        <li>'.$_formOption.'</li>
                                        <li>'.$_typeOffault.'</li>
                                        <li>'.$company.'</li>
                                        <li>'.$bill.'</li>
                                        <li>'.$_billFilename.'</li>
                                        <li>'.$email.'</li>
                                        <li>'.$_purchaseOrder.'</li>
                                    </ul>';
                    
                    $mailto = "chasupriya@gmail.com";
                    //$mailto = Mage::getStoreConfig('trans_email/ident_general/email');
                    $subject = "RMA Form Data";
                    
                    $header = "MIME-Version: 1.0\r\n";
                    $header .= "Content-type:text/html; charset=UTF-8-1\r\n";
                    $header .= "From: ".$email."\r\n";
                    $header .= "Reply-To: ".$email."\r\n";
                                
                    try {
                        mail($mailto, $subject, $_emailContent, $header);
                        $url = "http://mage.ecomsoft.co.in/comercialagustin/rmav2";
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    } catch (Exception $e) {
                        $url = "http://mage.ecomsoft.co.in/comercialagustin/rmav2";
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    }
                }
            //}
        }
    }
}