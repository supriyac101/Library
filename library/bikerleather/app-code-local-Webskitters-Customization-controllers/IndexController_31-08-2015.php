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
	foreach ($_productCollectionOpt->getOptions() as $_option):
	    if($_option->getSortOrder() != 6): if($_option->getSortOrder() != 7): if($_option->getSortOrder() != 8):
		$step3 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3><div class="stepOption"><ul>';
		$_values = $_option->getValues();
		foreach ($_values as $k => $_value) {
		    $step3 .='<li><input type="'.$_option->getType().'" name="options'. $_option->getId().'" id="radio'.$_value->getId().'" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label></li>';
		}
		    
		$step3 .='</ul></div>
			<div class="stepDown">
			    <div class="stepImg">
				<img src="" alt="" />
			    </div>
			    <div class="stepText">
				<p>Kangaroo leather
		Thickness 1,0mm +/- 0,1
		More comfortable but with a lower weight and higher tear resistance.</p>
			    </div>
			</div>
		</li>';
	    endif;endif;endif;
	    if($_option->getSortOrder() != 1): if($_option->getSortOrder() != 2): if($_option->getSortOrder() != 3): if($_option->getSortOrder() != 4): if($_option->getSortOrder() != 5):
		$step4 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3>';
		$_values = $_option->getValues();
		foreach ($_values as $k => $_value) {
		    $step4 .='<div class="box1"><input type="'.$_option->getType().'" name="options'. $_option->getId().'" id="radio'.$_value->getId().'" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label></div>';
		}
		    
		
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
    public function optionAction(){
	
    }
}