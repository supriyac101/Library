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
		$step2a = "";
		$step2 = "";
		$step2b = "";
		$step3 = "";
		$step4 = "";
		$step5 = "";
		$step6 = "";
		$step6b = "";
		$validate = "";
		$i = 0;
		foreach ($_productCollectionOpt->getOptions() as $_option): $i++;
			if($_option->getCssClass() == "step2" && $_option->getType() == "drop_down"):
				$step2a .='<select title="" class="selectdesign" id="select_'.$_option->getId().'" name="options['.$_option->getId().']"><option value="">-- Please Select --</option><option value="0">Deselect design</option>';
				$_values = $_option->getValues();
				foreach ($_values as $k => $_value) {
					$_valuePrice = Mage::helper('core')->currency($_value->getPrice(), false, false);
					$step2a .='<option price="'.$_valuePrice.'" value="'.$_value->getId().'" image="'.$_value->getImage().'">'.$_value->getTitle().'</option>';
				}
				$step2a .='</select>';
			endif;
			if($_option->getCssClass() == "step2" && $_option->getType() == "area"):
				$_optionPrice = Mage::helper('core')->currency($_option->getPrice(), false, false);
				$step2 .='<input type="'.$_option->getType().'" class="hidden" name="options['.$_option->getId().']" price="'.$_optionPrice.'" value="" />';
			endif;
			if($_option->getCssClass() == "step2" && $_option->getType() == "field"):
				$_optionPrice = Mage::helper('core')->currency($_option->getPrice(), false, false);
				$step2b .='<input type="'.$_option->getType().'" class="hidden" name="options['.$_option->getId().']" price="'.$_optionPrice.'" value="" />';
			endif;
			if($_option->getCssClass() == "step3"):
				$step3 .='<li class="design-link"><h3>'.$_option->getTitle().'</h3>
				<input type="hidden" name="optionradio_'.$i.'" value="'.$_option->getTitle().'">
				<input type="hidden" class="hidrad" name="options'.str_replace(" ", "", $_option->getSubtitle()).'" value="" />
				<div class="stepOption"><ul>';
				$_values = $_option->getValues();
				foreach ($_values as $k => $_value) {
					if($_option->getType() == "checkbox")
						$step3 .='<li><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().'][]" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" image="'.$_value->getImage().'" price="'.Mage::helper('core')->currency($_value->getPrice(), false, false).'" description="'.$_value->getDescription().'" title="'.$_value->getTitle().'" /><input type="hidden" name="options'.str_replace(" ", "", $_value->getDetails()).'" id="radio'.$_value->getId().'" value="" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label>
					</li>';
					else
						$step3 .='<li><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" image="'.$_value->getImage().'" price="'.Mage::helper('core')->currency($_value->getPrice(), false, false).'" description="'.$_value->getDescription().'" title="'.$_value->getTitle().'" /><label for="radio'.$_value->getId().'">'.$_value->getTitle().'</label>
					</li>';
				}
				$step3 .='</ul></div>';
				//if($_option->getSubtitle() == "leather"){
					$step3 .='
					<div class="stepDown">
						<div class="stepImg">
							<img src="" alt="" />
						</div>
						<div class="stepText">
							<p></p>
						</div>
					</div>
					<div class="propric bottPrice"></div>';
				/*}else{
					$step3 .='
					<div class="stepDown">
						<div class="stepImgBr">
						<img alt="" src="">
						</div>
						<div class="propric"></div>
					</div>';
				}*/
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
					$step4 .='<div class="box1"><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" price="'.Mage::helper('core')->currency($_value->getPrice(), false, false).'" title="'.$_value->getTitle().'" /><img src="'.Mage::getBaseUrl().$_value->getImage().'" alt="'.$_value->getTitle().'" width="119" height="86" /></div>';
					elseif($_option->getSortOrder() == 8):
					$step4 .='<p><input type="'.$_option->getType().'" class="radiooptnstp3" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" price="'.Mage::helper('core')->currency($_value->getPrice(), false, false).'" image="'.$_value->getImage().'" title="'.$_value->getTitle().'" /><label for="options_'.$_option->getId().'_'.$_value->getId().'">'.$_value->getTitle().'</label></p>';
					endif;
				}
				if($_option->getSortOrder() != 8):
					$step4 .='<div class="step4Pric bottPrice"></div>';
				endif;
				if($_option->getSortOrder() == 8):
					$step4 .='</div><div class="step4Img"><img src="" alt="" /></div><div class="step4Pric bottPrice"></div>';    
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
										<input name="filename'.$_option->getSubtitle().'" id="filename'.$_option->getSubtitle().'" value="" class="stepfvfile" type="file" price="'.Mage::helper('core')->currency($_option->getPrice(), false, false).'">
										<input type="hidden" name="logopos'.$_option->getSubtitle().'" value="" />
										<input type="hidden" name="logopric'.$_option->getSubtitle().'" value="" />
										<input type="'.$_option->getType().'" id="logval'.$_option->getSubtitle().'" class="hidden" name="options['.$_option->getId().']" value="" />
									</div>
								</div>
							</div>
						</li>';
			endif;
			if($_option->getCssClass() == "step6"):
				if ($_option->getIsRequire()): $em = 'reqlbl'; else: $em =""; endif;
				if($_option->getIsRequire()): $validate = "validatecls"; else: $validate = ""; endif;
				if($_option->getType()=="radio"):
					$_values = $_option->getValues();
					$step6b .= '<div class="ultop-inner"><label class="'.$em.'">'.$_option->getTitle().'</label>';
					foreach ($_values as $k => $_value) {
						$step6b .='<span><label for="'.$_value->getTitle().'">'.$_value->getTitle().'</label><input type="'.$_option->getType().'" class="radiooptnstp6 '.$validate.'" name="options['.$_option->getId().']" id="options_'.$_option->getId().'_'.$_value->getId().'" value="'.$_value->getId().'" title="required" /></span>';
					}
					$step6b .='</div>';
				else:
					$step6 .= '<li>
							<label class="numberLb '.$em.'">'.$_option->getTitle().'</label>
							<div class="input-box">
								<input type="'.$_option->getType().'" class="input-text required-entry '.$validate.' input-textStp6" value="" message="required" name="options['.$_option->getId().']" price="'.Mage::helper('core')->currency($_option->getPrice(), false, false).'" />
								<input type="hidden" name="'.$_option->getSubtitle().'" value="">
								<input type="hidden" name="'.$_option->getSubtitle().'pric" value="">
							</div>
						</li>';
				endif;
			endif;
		endforeach;
		
		$response['refreshtotalstep2a'] = $step2a;
		$response['refreshtotalstep2'] = $step2;
		$response['refreshtotalstep2b'] = $step2b;
		$response['refreshtotalBLK'] = $step3;
		$response['refreshtotalstep4'] = $step4;
		$response['refreshtotalstep5'] = $step5;
		$response['refreshtotalstep6'] = $step6;
		$response['refreshtotalstep6b'] = $step6b;
		
		foreach($_productCollection as $_product):
			$_finalPrice = $_product->getFinalPrice();
			$formattedPrice = Mage::helper('core')->currency($_finalPrice, false, false);
			$response['price'] = $formattedPrice;
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
		
		
		/*$contact = Mage::getModel('customization/customization');
		$contact->setData('filename9', $fname);
		$contact->save();
		
		$this->_redirectReferer();*/	
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
		
		if(Mage::app()->getRequest()->getParam('productPrice')){$productPrice = Mage::app()->getRequest()->getParam('productPrice');}else{$productPrice = 0;}
		if(Mage::app()->getRequest()->getParam('pricurl')){$pricurl = Mage::app()->getRequest()->getParam('pricurl');}else{$pricurl = 0;}
		if(Mage::app()->getRequest()->getParam('pricdsg')){$pricdsg = Mage::app()->getRequest()->getParam('pricdsg');}else{$pricdsg = 0;}
		if(Mage::app()->getRequest()->getParam('priclthr')){$priclthr = Mage::app()->getRequest()->getParam('priclthr');}else{$priclthr = 0;}
		if(Mage::app()->getRequest()->getParam('prichmp')){$prichmp = Mage::app()->getRequest()->getParam('prichmp');}else{$prichmp = 0;}
		if(Mage::app()->getRequest()->getParam('pricvntl')){$pricvntl = Mage::app()->getRequest()->getParam('pricvntl');}else{$pricvntl = 0;}
		if(Mage::app()->getRequest()->getParam('priclng')){$priclng = Mage::app()->getRequest()->getParam('priclng');}else{$priclng = 0;}
		if(Mage::app()->getRequest()->getParam('pricprft')){$pricprft = Mage::app()->getRequest()->getParam('pricprft');}else{$pricprft = 0;}
		if(Mage::app()->getRequest()->getParam('pricstp41')){$pricstp41 = Mage::app()->getRequest()->getParam('pricstp41');}else{$pricstp41 = 0;}
		if(Mage::app()->getRequest()->getParam('pricstp42')){$pricstp42 = Mage::app()->getRequest()->getParam('pricstp42');}else{$pricstp42 = 0;}
		if(Mage::app()->getRequest()->getParam('pricstp43')){$pricstp43 = Mage::app()->getRequest()->getParam('pricstp43');}else{$pricstp43 = 0;}
		
		
		if(Mage::app()->getRequest()->getParam('logopric1')){$logopric1 = Mage::app()->getRequest()->getParam('logopric1');}else{$logopric1 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric2')){$logopric2 = Mage::app()->getRequest()->getParam('logopric2');}else{$logopric2 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric3')){$logopric3 = Mage::app()->getRequest()->getParam('logopric3');}else{$logopric3 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric4')){$logopric4 = Mage::app()->getRequest()->getParam('logopric4');}else{$logopric4 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric5')){$logopric5 = Mage::app()->getRequest()->getParam('logopric5');}else{$logopric5 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric6')){$logopric6 = Mage::app()->getRequest()->getParam('logopric6');}else{$logopric6 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric7')){$logopric7 = Mage::app()->getRequest()->getParam('logopric7');}else{$logopric7 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric8')){$logopric8 = Mage::app()->getRequest()->getParam('logopric8');}else{$logopric8 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric9')){$logopric9 = Mage::app()->getRequest()->getParam('logopric9');}else{$logopric9 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric10')){$logopric10 = Mage::app()->getRequest()->getParam('logopric10');}else{$logopric10 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric11')){$logopric11 = Mage::app()->getRequest()->getParam('logopric11');}else{$logopric11 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric12')){$logopric12 = Mage::app()->getRequest()->getParam('logopric12');}else{$logopric12 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric13')){$logopric13 = Mage::app()->getRequest()->getParam('logopric13');}else{$logopric13 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric14')){$logopric14 = Mage::app()->getRequest()->getParam('logopric14');}else{$logopric14 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric15')){$logopric15 = Mage::app()->getRequest()->getParam('logopric15');}else{$logopric15 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric16')){$logopric16 = Mage::app()->getRequest()->getParam('logopric16');}else{$logopric16 = 0;}
		if(Mage::app()->getRequest()->getParam('logopric17')){$logopric17 = Mage::app()->getRequest()->getParam('logopric17');}else{$logopric17 = 0;}
		
		$totoptpric = $productPrice + $pricdsg + $pricurl + $priclthr + $prichmp + $pricvntl + $priclng+ $pricprft + $pricstp41 + $pricstp42 + $pricstp43+$logopric1+$logopric2+$logopric3+$logopric4+$logopric5+$logopric6+$logopric7+$logopric8+$logopric9+$logopric10+$logopric11+$logopric12+$logopric13+$logopric14+$logopric15+$logopric16+$logopric17;
		
		$_baseProductPrice = Mage::helper('core')->currency($attributeValue2, false, false);
		$_totpropric = $_baseProductPrice+$totoptpric;
		
		
		$_productoption13 = Mage::app()->getRequest()->getParam('optionsPerforatedsleeves');
		$_productoption57 = Mage::app()->getRequest()->getParam('optionsPerforatedlegs');
		$_productoption58 = Mage::app()->getRequest()->getParam('optionsPerforatedshape');
		
		$message .='
			<table class="separate">
				<tr><td class="separate-td"><h6 class="revtbhdg">Product id</h6></td><td class="separate-td">'.$attributeValue.'</td>
				<td class="separate-td"><h6 class="revtbhdg">Product Name</h6></td><td class="separate-td">'.$attributeValue1.'</td>
				<td class="separate-td"><h6 class="revtbhdg">Product Price</h6></td><td class="separate-td">'.$currency_symbol.' '.number_format($_totpropric, 2, ',', ' ').'</td></tr>';
		
		$params = Mage::app()->getRequest()->getParams();
		/** @var Mage_Catalog_Model_Product $product */
		$product = Mage::getModel('catalog/product')->load($params['entityid']);
		$info = new Varien_Object($params);
		
		// Don't throw an exception if required options are missing
		$processMode = Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE;
		
		$options = array();
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
			if($group->getPrintableOptionValue($optionValue)){$_optVal = $group->getPrintableOptionValue($optionValue);}else{$_optVal="N/A";}
			
			if($option->getType() == "area" || $option->getSubtitle() == "designurl"):
				if($group->getPrintableOptionValue($optionValue)){$_optVal='<img src="'.$group->getPrintableOptionValue($optionValue).'" width="100px" height="100px" />';}else{$_optVal="N/A";}
				$message .='<td class="separate-td"><h6 class="revtbhdg">'.$option->getTitle().'</h6></td><td class="separate-td">'.$_optVal.'</td>';
			elseif($option->getType() == "checkbox"):
				$message .='<td class="separate-td"><h6 class="revtbhdg">'.$option->getTitle().'</h6></td><td class="separate-td">';
				if($_productoption13)
					$message .= $_productoption13;
				if($_productoption57)
					$message .= ', '.$_productoption57;
				if($_productoption58)
					$message .= ', '.$_productoption58;
				$message .='</td>';
			else:
				$message .='<td class="separate-td"><h6 class="revtbhdg">'.$option->getTitle().'</h6></td><td class="separate-td">'.$_optVal.'</td>';
			endif;
			//echo $option->getSubtitle();
			//echo $option->getTitle()."------".$group->getPrintableOptionValue($optionValue)."\n";
			if ($i%$_columnCount==0):
				$message .='</tr><tr>';
			endif;
		}
		
		// store currency code eg. USD, INR
		$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
		
		// store currency symbol eg. $
		$currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();
		$message .='<tr class="grdtot"><td colspan="5" class="separate-td"><h3 class="revtbhdg">Grand Total:</h3></td><td class="grdtot-td">' .$currency_symbol.' '.number_format($_totpropric, 2, ',', ' ').'</td></tr>';
		$message .='</table>';
		//print_r($group->getFormattedOptionValue($optionValue));
		$continue['content'] = $message;
		return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($continue));
    }
	
    public function printpdfAction(){
		/*ini_set('display_errors', 0);
		Varien_Profiler::enable();
		Mage::setIsDeveloperMode(true);*/
				
		/*$pdf = new Zend_Pdf();
        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $page->setFont($font, 6);
		
        $page->drawText($content, 40, $page->getHeight()-85, "UTF-8");
		
		//add pages to main document
        $pdf->pages[] = $page;
		$pdf->render();
		//$pdf->save('details.pdf');
		
        $fileName = 'details.pdf';
		
		//send it to the browser to download
		$this->_prepareDownloadResponse($fileName, $pdf->render(), 'application/pdf');*/
		
		//echo Mage::getBaseDir().DS."dompdf_config.inc.php";
		/*require(Mage::getBaseDir().DS.'WriteHTML.php');

		$pdf=new PDF_HTML();
		$pdf->AddPage();
		$pdf->SetFont('Arial');
		$pdf->WriteHTML($content);
		$pdf->Output('details.pdf','D');*/
		
		$content = Mage::app()->getRequest()->getParam('frmdata');
		//echo $content; exit;
		
		require(Mage::getBaseDir().DS.'mpdf60/mpdf.php');
		$mpdf=new mPDF('c');

		$stylesheet = file_get_contents(Mage::getBaseDir('skin').'/css/styles.css');
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->WriteHTML($content);
		
		$mpdf->Output('review.pdf','F');
	}
	
	public function sendmailAction(){
		/*Mage::setIsDeveloperMode(true);
		ini_set('display_errors', 1);
		echo "<pre>";
		print_r($_POST);*/
		
		/*$message = Mage::app()->getRequest()->getParam('frmdata');
		$toname = Mage::app()->getRequest()->getParam('toname');
		$toadd = Mage::app()->getRequest()->getParam('toadd');
		$tocomment = Mage::app()->getRequest()->getParam('tocomment');
		
		// Always set content-type when sending HTML email
		$headers='MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html;charset=iso-8859-1' . "\r\n";
		$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
		
		$to = Mage::app()->getRequest()->getParam('toemail');
		$subject = "Biker Leather customization option";
		
		$body = "Hello<br/>".$toname."<br/><br/>".$toadd."<br/>".$tocomment."<br/>".$message;
		$mail = Mage::getModel('core/email');
		$mail->setToName($toname);
		$mail->setToEmail($to);
		$mail->setBody($body);
		$mail->setSubject($subject);
		$mail->setFromEmail($headers);
		$mail->setFromName($headers);
		$mail->setType('html');*/// You can use 'html' or 'text'
		
		
		
		$frmdata = Mage::app()->getRequest()->getParam('frmdata');	
		$toname = Mage::app()->getRequest()->getParam('toname');
		$toadd = Mage::app()->getRequest()->getParam('toadd');
		$tocomment = Mage::app()->getRequest()->getParam('tocomment');
		
		$rand = rand();
		$filename= "review_".$rand.".pdf";
		require(Mage::getBaseDir().DS.'mpdf60/mpdf.php');
		$mpdf=new mPDF('c');

		$stylesheet = file_get_contents(Mage::getBaseDir('skin').'/css/styles.css');
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->WriteHTML($frmdata);
		
		$content = $mpdf->Output('','S');
		
		
		
		/*$path = Mage::getBaseDir().DS;
		$file = $path.$filename;
		$file_size = filesize($file);
		$handle = fopen($file, "r");
		$content = fread($handle, $file_size);
		fclose($handle);*/
		$content = chunk_split(base64_encode($content));
		$uid = md5(uniqid(time()));
		
		
		$mailto = Mage::app()->getRequest()->getParam('toemail');
		$subject = "RTX Leathers Customisation Review";
		$message = "Hello \r\n".$toname."\r\n\r\n".$toadd."\r\n".$tocomment."\r\nPlease find the attachment.";
		//$message = "Hello";
		
		$header = "From: RTX Leather \r\n";
		//$header .= "Reply-To: <test@test.com\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$header .= "--".$uid."\r\n";
		$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$header .= $message."\r\n\r\n";
		$header .= "--".$uid."\r\n";
		$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
		$header .= "Content-Transfer-Encoding: base64\r\n";
		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
		$header .= $content."\r\n\r\n";
		$header .= "--".$uid."--";
		
		/*if (mail($mailto, $subject, "", $header)) {
			echo "mail send ... OK"; // or use booleans here
		} else {
			echo "mail send ... ERROR!";
		}*/
		$response['success'] = "Your email has been sent";
		$response['error'] = "Unable to send email";
		
		try {
			mail($mailto, $subject, "", $header);
			//$mpdf->Output();
			return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
		}
		catch (Exception $e) {
			return $this->getResponse()->setHeader('Content-type', 'application/json')->setBody(Mage::helper('core')->jsonEncode($response));
		}
		
		/*try {
			$mail->send();
			Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
			//$this->_redirectReferer();
		}
		catch (Exception $e) {
			Mage::getSingleton('core/session')->addError('Unable to send.');
			//$this->_redirectReferer();
		}*/
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