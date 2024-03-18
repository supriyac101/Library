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
    
    public function optionAction(){
	
    }
}