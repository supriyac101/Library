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
		$step2 = "";
		$step2b = "";
		$step3 = "";
		$step4 = "";
		$step5 = "";
		$step6 = "";
		$i = 0;
		foreach ($_productCollectionOpt->getOptions() as $_option): $i++;
			if($_option->getCssClass() == "step2" && $_option->getType() == "area"):
				$step2 .='<input type="'.$_option->getType().'" class="hidden" name="options['.$_option->getId().']" price="'.$_option->getPrice().'" value="" />';
			endif;
			if($_option->getCssClass() == "step2" && $_option->getType() == "field"):
				$step2b .='<input type="'.$_option->getType().'" class="hidden" name="options['.$_option->getId().']" price="'.$_option->getPrice().'" value="" />';
			endif;
			if($_option->getCssClass() == "step3"):
				$step3 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3>
				<input type="hidden" name="optionradio_'.$i.'" value="'.$_option->getTitle().'">
				<input type="hidden" class="hidrad" name="options'.str_replace(" ", "", $_option->getSubtitle()).'" value="" />
				<div class="stepOption"><ul>';
				$_values = $_option->getValues();
				foreach ($_values as $k => $_value) {
					if($_option->getType() == "checkbox")
						$step3 .='<li><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().'][]" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" image="'.$_value->getImage().'" price="'.$_value->getPrice().'" title="'.$_value->getTitle().'" /><input type="hidden" name="options'.str_replace(" ", "", $_value->getDetails()).'" id="radio'.$_value->getId().'" value="" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label>
					</li>';
					else
						$step3 .='<li><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" image="'.$_value->getImage().'" price="'.$_value->getPrice().'" description="'.$_value->getDescription().'" title="'.$_value->getTitle().'" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label>
					</li>';
				}
				$step3 .='</ul></div>';
				if($_option->getSubtitle() == "leather"){
					$step3 .='
					<div class="stepDown">
						<div class="stepImg">
							<img src="" alt="" />
						</div>
						<div class="propric"></div>
						<div class="stepText">
							<p></p>
						</div>
					</div>';
				}else{
					$step3 .='
					<div class="stepDown">
						<div class="stepImgBr">
						<img alt="" src="">
						</div>
						<div class="propric"></div>
					</div>';
				}
				$step3 .='</li>';
			endif;
			if($_option->getCssClass() == "step4"):
				$step4 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3>
				<input type="hidden" name="optionradio_'.$i.'" value="'.$_option->getTitle().'">
				<input type="hidden" class="hidrad" name="options'.str_replace(" ", "", $_option->getSubtitle()).'" value="" />';
				if($_option->getSortOrder() == 8):
					$step4 .='<div class="step4">';
				endif;
				$_values = $_option->getValues();
				foreach ($_values as $k => $_value) {
					if($_option->getSortOrder() != 8):
					$step4 .='<div class="box1"><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" price="'.$_value->getPrice().'" title="'.$_value->getTitle().'" /><img src="'.$_value->getImage().'" alt="'.$_value->getTitle().'" /></div>';
					elseif($_option->getSortOrder() == 8):
					$step4 .='<p><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" price="'.$_value->getPrice().'" image="'.$_value->getImage().'" title="'.$_value->getTitle().'" /><label for="options_'.$_option->getId().'_'.$_value->getId().'">'.$_value->getTitle().'</label></p>';
					endif;
				}
				if($_option->getSortOrder() != 8):
					$step4 .='<div class="step4Pric"></div>';
				endif;
				if($_option->getSortOrder() == 8):
					$step4 .='</div><div class="step4Img"><img src="" alt="" /></div><div class="step4Pric"></div>';    
				endif;
				$step4 .='</li>';
			endif;
			if($_option->getCssClass() == "step5"):
				//echo "<pre>";
				//print_r($_option);
				$step5 .= '<li>
							<label class="numberLb">'.$_option->getSubtitle().'</label>
							<div class="logo-box">
								<div class="logo'.$_option->getSubtitle().'-input-box stpfvlg"></div>
								<div class="stpfvdn">
									<div class="filenamesp" id="filenamesp'.$_option->getSubtitle().'"></div>';
									$step5 .='<div class="stpfvuploadbtn">
										<div class="upload">Browse</div>
										<input name="filename'.$_option->getSubtitle().'" id="filename'.$_option->getSubtitle().'" value="" class="stepfvfile" type="file" price="'.$_option->getPrice().'">
										<input type="hidden" name="logopos'.$_option->getSubtitle().'" value="" />
										<input type="hidden" name="logopric'.$_option->getSubtitle().'" value="" />
										<input type="'.$_option->getType().'" id="logval'.$_option->getSubtitle().'" class="hidden" name="options['.$_option->getId().']" value="" />
									</div>
								</div>
							</div>
						</li>';
			endif;
			if($_option->getCssClass() == "step6"):
				$step6 .= '<li>
							<label class="numberLb">'.$_option->getTitle().'</label>
							<div class="input-box">
								<input type="'.$_option->getType().'" class="input-text required-entry input-textStp6" value="" name="options['.$_option->getId().']" price="'.$_option->getPrice().'" />
								<input type="hidden" name="'.$_option->getSubtitle().'" value="">
								<input type="hidden" name="'.$_option->getSubtitle().'pric" value="">
							</div>
						</li>';
			endif;
		endforeach;
		
		$response['refreshtotalstep2'] = $step2;
		$response['refreshtotalstep2b'] = $step2b;
		$response['refreshtotalBLK'] = $step3;
		$response['refreshtotalstep4'] = $step4;
		$response['refreshtotalstep5'] = $step5;
		$response['refreshtotalstep6'] = $step6;
		
		foreach($_productCollection as $_product):
			$response['price'] = $_product->getFinalPrice();
			$response['imgurl'] = $_product->getImageUrl();
			$response['proid'] = $_product->getId();
			$_productColl =  Mage::getModel("catalog/product")->load($_product->getId());
			$response['proname'] = $_productColl->getName();
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
    
	public function urlsaveAction() {
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
	
	public function reviewAction(){
		/*ini_set('display_errors', 0);
		Varien_Profiler::enable();
		Mage::setIsDeveloperMode(true);*/
		//echo "<pre>";
		//print_r($_POST);
		
		$message ="";
		$_productoption1 = Mage::app()->getRequest()->getParam('entityid');
		$attributeValue = Mage::getModel('catalog/product')->load($_productoption1)->getEntityId();
		$attributeValue1 = Mage::getModel('catalog/product')->load($_productoption1)->getName();
		$attributeValue2 = Mage::getModel('catalog/product')->load($_productoption1)->getPrice();
		
		$_productoption13 = Mage::app()->getRequest()->getParam('optionsPerforatedsleeves');
		$_productoption57 = Mage::app()->getRequest()->getParam('optionsPerforatedlegs');
		$_productoption58 = Mage::app()->getRequest()->getParam('optionsPerforatedshape');
		
		$message .='
			<table>
				<tr><td><h6 class="revtbhdg">Product id</h6></td><td>'.$attributeValue.'</td>
				<td><h6 class="revtbhdg">Product Name</h6></td><td>'.$attributeValue1.'</td>
				<td><h6 class="revtbhdg">Product Price</h6></td><td>'.$attributeValue2.'</td></tr>';
		
		$params = Mage::app()->getRequest()->getParams();
		/** @var Mage_Catalog_Model_Product $product */
		$product = Mage::getModel('catalog/product')->load($params['entityid']);
		$info = new Varien_Object($params);
		
		// Don't throw an exception if required options are missing
		$processMode = Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE;
		
		$options = array();
		//$_collectionSize = 12;
		$_columnCount = 3;
		$i=0; foreach ($product->getOptions() as $option) { $i++;
			/* @var $option Mage_Catalog_Model_Product_Option */
			$group = $option->groupFactory($option->getType())
				->setOption($option)
				->setProduct($product)
				->setRequest($info)
				->setProcessMode($processMode)
				->validateUserValue($info->getOptions());
		
			$optionValue = $info->getData('options/' . $option->getId());
			
			
			$options[] = array(
				'label' => $option->getTitle(),
				'value' => $group->getFormattedOptionValue($optionValue),
				'print_value' => $group->getPrintableOptionValue($optionValue),
				'option_id' => $option->getId(),
				'option_type' => $option->getType(),
				'custom_view' => $group->isCustomizedView(),
			);
			
			if($option->getType() == "area" || $option->getSubtitle() == "designurl"):
				$message .='<td><h6 class="revtbhdg">'.$option->getTitle().'</h6></td><td><img src="'.$group->getPrintableOptionValue($optionValue).'" width="100px" height="100px" /></td>';
			elseif($option->getType() == "checkbox"):
				$message .='<td><h6 class="revtbhdg">'.$option->getTitle().'</h6></td><td>';
				if($_productoption13)
					$message .= $_productoption13;
				if($_productoption57)
					$message .= ', '.$_productoption57;
				if($_productoption58)
					$message .= ', '.$_productoption58;
				$message .='</td>';
			else:
				$message .='<td><h6 class="revtbhdg">'.$option->getTitle().'</h6></td><td>'.$group->getPrintableOptionValue($optionValue).'</td>';
			endif;
			//echo $option->getSubtitle();
			//echo $option->getTitle()."------".$group->getPrintableOptionValue($optionValue)."\n";
			if ($i%$_columnCount==0):
				$message .='</tr><tr>';
			endif;
		}
		
		$message .='</table>';
		//print_r($options);
		$continue['content'] = $message;
		return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($continue));
    }
    
	public function sendmailAction(){
		/*Mage::setIsDeveloperMode(true);
		ini_set('display_errors', 1);
		echo "<pre>";
		print_r($_POST);*/
		
		$message ="";
		$_productoption1 = Mage::app()->getRequest()->getParam('entityid');
		$attributeValue = Mage::getModel('catalog/product')->load($_productoption1)->getEntityId();
		$attributeValue1 = Mage::getModel('catalog/product')->load($_productoption1)->getName();
		$attributeValue2 = Mage::getModel('catalog/product')->load($_productoption1)->getPrice();
		
		$_productoption13 = Mage::app()->getRequest()->getParam('optionsPerforatedsleeves');
		$_productoption57 = Mage::app()->getRequest()->getParam('optionsPerforatedlegs');
		$_productoption58 = Mage::app()->getRequest()->getParam('optionsPerforatedshape');
		
		$message .='
			<table>
				<tr><td>Product id</td><td>'.$attributeValue.'</td></tr>
				<tr><td>Product Name</td><td>'.$attributeValue1.'</td></tr>
				<tr><td>Product Price</td><td>'.$attributeValue2.'</td></tr>';
		
		$params = Mage::app()->getRequest()->getParams();
		/** @var Mage_Catalog_Model_Product $product */
		$product = Mage::getModel('catalog/product')->load($params['entityid']);
		$info = new Varien_Object($params);
		
		// Don't throw an exception if required options are missing
		$processMode = Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE;
		
		$options = array();
		foreach ($product->getOptions() as $option) {
			/* @var $option Mage_Catalog_Model_Product_Option */
			$group = $option->groupFactory($option->getType())
				->setOption($option)
				->setProduct($product)
				->setRequest($info)
				->setProcessMode($processMode)
				->validateUserValue($info->getOptions());
		
			$optionValue = $info->getData('options/' . $option->getId());
			
			$options[] = array(
				'label' => $option->getTitle(),
				'value' => $group->getFormattedOptionValue($optionValue),
				'print_value' => $group->getPrintableOptionValue($optionValue),
				'option_id' => $option->getId(),
				'option_type' => $option->getType(),
				'custom_view' => $group->isCustomizedView(),
			);
			if($option->getType() == "area" || $option->getSubtitle() == "designurl"):
				$message .='<tr><td>'.$option->getTitle().'</td><td><img src="'.$group->getPrintableOptionValue($optionValue).'" width="150px" height="150px" /></td></tr>';
			elseif($option->getType() == "checkbox"):
				$message .='<tr><td>'.$option->getTitle().'</td><td>';
				if($_productoption13)
					$message .= $_productoption13;
				if($_productoption57)
					$message .= ', '.$_productoption57;
				if($_productoption58)
					$message .= ', '.$_productoption58;
				$message .='</td></tr>';
			else:
				$message .='<tr><td>'.$option->getTitle().'</td><td>'.$group->getPrintableOptionValue($optionValue).'</td></tr>';
			endif;
			//echo $option->getSubtitle();
			//echo $option->getTitle()."------".$group->getPrintableOptionValue($optionValue)."\n";
		}
		$message .='</table>';
		
		// Always set content-type when sending HTML email
		$headers='MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html;charset=iso-8859-1' . "\r\n";
	
	
		$to = Mage::getStoreConfig('trans_email/ident_general/email');
		$subject = "Biker Leather customization option";
		
		
		
		
		$body = $message;
		$mail = Mage::getModel('core/email');
		$mail->setToName('Admin');
		$mail->setToEmail($to);
		$mail->setBody($body);
		$mail->setSubject($subject);
		$mail->setFromEmail($headers);
		$mail->setFromName($headers);
		$mail->setType('html');// You can use 'html' or 'text'
		
		try {
			$mail->send();
			Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
			//$this->_redirectReferer();
		}
		catch (Exception $e) {
			Mage::getSingleton('core/session')->addError('Unable to send.');
			//$this->_redirectReferer();
		}
	}
	
    public function addtocartAction(){
		/*ini_set('display_errors', 0);
		Varien_Profiler::enable();
		Mage::setIsDeveloperMode(true);*/
		//echo "<pre>";
		//print_r($_POST);
		$uploadedimg = Mage::app()->getRequest()->getParam('uploadedimg');
		$_options = Mage::app()->getRequest()->getParam('options');
		//print_r($_options);
		/*foreach($_options as $_key => $_val):
			$params = array($_key => $_val);
		endforeach;
		print_r($params);*/
		$product_id = Mage::app()->getRequest()->getParam('entityid');
		$product_qty= 1;
		$_product = Mage::getSingleton('catalog/product')->load($product_id);
		//$additional_options = $_product->getProductOptions();
		//print_r($additional_options);
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		$cart->addProduct($_product,
			array('qty' => 1,
				'options' => $_options
			)
		);
		//$cart->setCustomPrice($rush);
		//var_dump($cart);
		//exit;
		$cart->save();
		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	}
}