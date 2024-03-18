<?php
class Webskitters_Customization_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/customization?id=15 
    	 *  or
    	 * http://site.com/customization/id/15 	
    	 */
    	/* 
		$customization_id = $this->getRequest()->getParam('id');

  		if($customization_id != null && $customization_id != '')	{
			$customization = Mage::getModel('customization/customization')->load($customization_id)->getData();
		} else {
			$customization = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($customization == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$customizationTable = $resource->getTableName('customization');
			
			$select = $read->select()
			   ->from($customizationTable,array('customization_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$customization = $read->fetchRow($select);
		}
		Mage::register('customization', $customization);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    public function productAction()
    {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	$_productId = Mage::app()->getRequest()->getParam('proid');
	//echo $_productId; exit;
	$_productCollection = Mage::getResourceModel('catalog/product_collection')
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('entity_id', $_productId);
	$response['status'] = 0;
	$response['proid'] = $_productId;
	//echo "<pre>";
	
	
	$_productCollectionOpt =  Mage::getModel("catalog/product")->load($_productId);
	$step3 = "";
	$step4 = "";
	$i=0;
	foreach ($_productCollectionOpt->getOptions() as $_option): $i++;
	    if($_option->getSortOrder() != 6): if($_option->getSortOrder() != 7): if($_option->getSortOrder() != 8):
		$step3 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3>
		<div class="stepOption"><ul>';
		$_values = $_option->getValues();
		foreach ($_values as $k => $_value) {
		    if($_option->getType() == "checkbox")
			$step3 .='<li><input type="'.$_option->getType().'" class="radiooptnstp3" name="options_'.str_replace(" ", "", $_value->getTitle()).'" id="'.$_value->getId().'" value="'.$_value->getTitle().'" image="'.$_value->getImage().'" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label>
		    </li>';
		    else
			$step3 .='<li><input type="'.$_option->getType().'" class="radiooptnstp3" name="options'.str_replace(" ", "", $_option->getTitle()).'" id="'.$_value->getId().'" value="'.$_value->getTitle().'" image="'.$_value->getImage().'" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label>
		    </li>';
		}
		$step3 .='</ul></div>';
		if($_option->getTitle() == "LEATHER"){
		    $step3 .='
			<div class="stepDown">
			    <div class="stepImg">
				<img src="" alt="" />
			    </div>
			    <div class="stepText">
				<p>Kangaroo leather
		Thickness 1,0mm +/- 0,1
		More comfortable but with a lower weight and higher tear resistance.</p>
			    </div>
			</div>';
		}else{
		    $step3 .='
			<div class="stepDown">
			    <div class="stepImgBr">
				<img alt="" src="">
			    </div>
			</div>';
		}
		$step3 .='</li>';
		
	    endif;endif;endif;
	    if($_option->getSortOrder() != 1): if($_option->getSortOrder() != 2): if($_option->getSortOrder() != 3): if($_option->getSortOrder() != 4): if($_option->getSortOrder() != 5):
		$step4 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3>';
		if($_option->getSortOrder() == 8):
		    $step4 .='<div class="step4">';
		endif;
		$_values = $_option->getValues();
		foreach ($_values as $k => $_value) {
		    if($_option->getSortOrder() != 8):
			$step4 .='<div class="box1"><input type="'.$_option->getType().'" name="options'. $_option->getTitle().'" id="radio'.$_value->getId().'" value="'.$_value->getTitle().'" /><img src="'.$_value->getImage().'" alt="'.$_value->getTitle().'" /></label></div>';
		    elseif($_option->getSortOrder() == 8):
			$step4 .='<p><input type="'.$_option->getType().'" class="radiooptnstp3" name="options'. $_option->getTitle().'" id="radio'.$_value->getId().'" image="'.$_value->getImage().'" value="'.$_value->getTitle().'" /><label for="radio_'.$_value->getId().'">'.$_value->getTitle().'</label></p>';
		    endif;
		}
		if($_option->getSortOrder() == 8):
		    $step4 .='</div><div class="step4Img"><img src="" alt="" /></div>';    
		endif;
		$step4 .='</li>';
	    endif;endif;endif;endif;endif;
	endforeach;
	
	$response['refreshtotalstep4'] = $step4;
	$response['refreshtotalBLK'] = $step3;
	
	foreach($_productCollection as $_product):
	    $response['price'] = $_product->getFinalPrice();
	    $response['imgurl'] = $_product->getImageUrl();
	    $response['proid'] = $_product->getId();
	    $_productColl =  Mage::getModel("catalog/product")->load($_product->getId());
	endforeach;
	
	return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
    }
    
    public function imagesaveAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename']['name'];
	    $uploader = new Varien_File_Uploader('filename');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    
    public function logouploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename1']['name'];
	    $uploader = new Varien_File_Uploader('filename1');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename1', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo2uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename2']['name'];
	    $uploader = new Varien_File_Uploader('filename2');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename2', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo3uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename3']['name'];
	    $uploader = new Varien_File_Uploader('filename3');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename3', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo4uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename4']['name'];
	    $uploader = new Varien_File_Uploader('filename4');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename4', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo5uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename5']['name'];
	    $uploader = new Varien_File_Uploader('filename5');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename5', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo6uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename6']['name'];
	    $uploader = new Varien_File_Uploader('filename6');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename6', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo7uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename7']['name'];
	    $uploader = new Varien_File_Uploader('filename7');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename7', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo8uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename8']['name'];
	    $uploader = new Varien_File_Uploader('filename8');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename8', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo9uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename9']['name'];
	    $uploader = new Varien_File_Uploader('filename9');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename9', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo10uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename10']['name'];
	    $uploader = new Varien_File_Uploader('filename10');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename10', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo11uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename11']['name'];
	    $uploader = new Varien_File_Uploader('filename11');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename11', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo12uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename12']['name'];
	    $uploader = new Varien_File_Uploader('filename12');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename12', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo13uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename13']['name'];
	    $uploader = new Varien_File_Uploader('filename13');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename13', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo14uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename14']['name'];
	    $uploader = new Varien_File_Uploader('filename14');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename14', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo15uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename15']['name'];
	    $uploader = new Varien_File_Uploader('filename15');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename15', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo16uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename16']['name'];
	    $uploader = new Varien_File_Uploader('filename16');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename16', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    public function logo17uploadAction() {
	Mage::setIsDeveloperMode(true);
	ini_set('display_errors', 1);
	//echo "<pre>";
	//print_r($_FILES['name']);
	$path = Mage::getBaseDir('media') . DS . "customdesign" . DS;
	if (!file_exists($path)) {
	    mkdir($path, 777, true);
	}
	try {
	    $fname = $_FILES['filename17']['name'];
	    $uploader = new Varien_File_Uploader('filename17');
	    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
	    $uploader->setAllowCreateFolders(true);
	    $uploader->setAllowRenameFiles(false);
	    $uploader->setFilesDispersion(false);
	    $uploader->save($path, $fname);
	    $response['imgname'] = $fname;
	    $response['imgpath'] = $path;
	    return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
	} catch (Exception $e) {
	    echo 'Error Message: ' . $e->getMessage();
	}
	
    
	$contact = Mage::getModel('customization/customization');
	$contact->setData('filename17', $fname);
	$contact->save();
    
	$this->_redirectReferer();	
    }
    /*public function checkoutAction(){
	echo "<pre>";
	print_r($_POST);
	$to = "gargi.bandyopadhyay@webskitters.com";
	$subject = "HTML email";
	
	$message = "";
	$_productoption1 = Mage::app()->getRequest()->getParam('entityid');
	$attributeValue = Mage::getModel('catalog/product')->load($_productoption1)->getEntityId();
	$attributeValue1 = Mage::getModel('catalog/product')->load($_productoption1)->getName();
	$attributeValue2 = Mage::getModel('catalog/product')->load($_productoption1)->getPrice();
	echo $message .= $attributeValue;
	echo $message .= $attributeValue1;
	echo $message .= $attributeValue2;
	
	$_productoption2 = Mage::app()->getRequest()->getParam('uploadedimg');
	$message .= $_productoption2;
	$_productoption3 = Mage::app()->getRequest()->getParam('designurl');
	$message .= $_productoption3;
	$_productoption4 = Mage::app()->getRequest()->getParam('optionradio_1');
	$message .= $_productoption4;
	$_productoption5 = Mage::app()->getRequest()->getParam('optionsLEATHER');
	$message .= $_productoption5;
	$_productoption6 = Mage::app()->getRequest()->getParam('optionradio_2');
	$message .= $_productoption6;
	$_productoption7 = Mage::app()->getRequest()->getParam('optionsHUMP');
	$message .= $_productoption7;
	$_productoption8 = Mage::app()->getRequest()->getParam('optionradio_3');
	$message .= $_productoption8;
	$_productoption9 = Mage::app()->getRequest()->getParam('optionsventilation');
	$message .= $_productoption9;
	$_productoption10 = Mage::app()->getRequest()->getParam('optionradio_4');
	$message .= $_productoption10;
	$_productoption11 = Mage::app()->getRequest()->getParam('optionslining');
	$message .= $_productoption11;
	$_productoption12 = Mage::app()->getRequest()->getParam('optionradio_5');
	$message .= $_productoption12;
	$_productoption13 = Mage::app()->getRequest()->getParam('optionsventilation');
	$message .= $_productoption13;
	$_productoption14 = Mage::app()->getRequest()->getParam('optionradio_6');
	$message .= $_productoption14;
	$_productoption15 = Mage::app()->getRequest()->getParam('optionsSliders_color');
	$message .= $_productoption15;
	$_productoption16 = Mage::app()->getRequest()->getParam('optionradio_7');
	$message .= $_productoption16;
	$_productoption17 = Mage::app()->getRequest()->getParam('optionsHard_CE_Armour_Inserts_or_Soft_padding');
	$message .= $_productoption17;
	$_productoption18 = Mage::app()->getRequest()->getParam('optionradio_8');
	$message .= $_productoption18;
	$_productoption19 = Mage::app()->getRequest()->getParam('optionsChoose_your_external_protections');
	$message .= $_productoption19;
	$_productoption12 = Mage::app()->getRequest()->getParam('logopos1');
	$message .= $_productoption12;
	$_productoption13 = Mage::app()->getRequest()->getParam('logopos2');
	$message .= $_productoption13;
	$_productoption14 = Mage::app()->getRequest()->getParam('logopos3');
	$message .= $_productoption14;
	$_productoption15 = Mage::app()->getRequest()->getParam('logopos4');
	$message .= $_productoption15;
	$_productoption16 = Mage::app()->getRequest()->getParam('logopos5');
	$message .= $_productoption16;
	$_productoption17 = Mage::app()->getRequest()->getParam('logopos6');
	$message .= $_productoption17;
	$_productoption18 = Mage::app()->getRequest()->getParam('logopos7');
	$message .= $_productoption18;
	$_productoption19 = Mage::app()->getRequest()->getParam('logopos8');
	$message .= $_productoption19;
	$_productoption20 = Mage::app()->getRequest()->getParam('logopos9');
	$message .= $_productoption20;
	$_productoption21 = Mage::app()->getRequest()->getParam('logopos10');
	$message .= $_productoption21;
	$_productoption22 = Mage::app()->getRequest()->getParam('logopos11');
	$message .= $_productoption22;
	$_productoption23 = Mage::app()->getRequest()->getParam('logopos12');
	$message .= $_productoption23;
	$_productoption24 = Mage::app()->getRequest()->getParam('logopos13');
	$message .= $_productoption24;
	$_productoption25 = Mage::app()->getRequest()->getParam('logopos14');
	$message .= $_productoption25;
	$_productoption26 = Mage::app()->getRequest()->getParam('logopos15');
	$message .= $_productoption26;
	$_productoption27 = Mage::app()->getRequest()->getParam('logopos16');
	$message .= $_productoption27;
	$_productoption28 = Mage::app()->getRequest()->getParam('logopos17');
	$message .= $_productoption28;
	$_productoption29 = Mage::app()->getRequest()->getParam('wrist');
	$message .= $_productoption29;
	$_productoption30 = Mage::app()->getRequest()->getParam('forearms');
	$message .= $_productoption30;
	$_productoption31 = Mage::app()->getRequest()->getParam('biceps');
	$message .= $_productoption31;
	$_productoption32 = Mage::app()->getRequest()->getParam('neck');
	$message .= $_productoption32;
	$_productoption33 = Mage::app()->getRequest()->getParam('chest');
	$message .= $_productoption33;
	$_productoption34 = Mage::app()->getRequest()->getParam('waist');
	$message .= $_productoption34;
	$_productoption35 = Mage::app()->getRequest()->getParam('hipbottom');
	$message .= $_productoption35;
	$_productoption36 = Mage::app()->getRequest()->getParam('name');
	$message .= $_productoption36;
	$_productoption37 = Mage::app()->getRequest()->getParam('knee');
	$message .= $_productoption37;
	$_productoption38 = Mage::app()->getRequest()->getParam('calf');
	$message .= $_productoption38;
	$_productoption39 = Mage::app()->getRequest()->getParam('ankle');
	$message .= $_productoption39;
	$_productoption40 = Mage::app()->getRequest()->getParam('shoulder');
	$message .= $_productoption40;
	$_productoption41 = Mage::app()->getRequest()->getParam('necktowaistlength');
	$message .= $_productoption41;
	$_productoption42 = Mage::app()->getRequest()->getParam('elbow');
	$message .= $_productoption42;
	$_productoption43 = Mage::app()->getRequest()->getParam('insidecrotchtoanklelength');
	$message .= $_productoption43;
	$_productoption44 = Mage::app()->getRequest()->getParam('kneecenttoanklebone');
	$message .= $_productoption44;
	$_productoption45 = Mage::app()->getRequest()->getParam('outsidewaisttoanklebone');
	$message .= $_productoption45;
	$_productoption46 = Mage::app()->getRequest()->getParam('shouldertowristlength');
	$message .= $_productoption46;
	$_productoption47 = Mage::app()->getRequest()->getParam('weight');
	$message .= $_productoption47;
	
	;
		mail($to,$subject,$message,$headers);
	

	
    }*/
}