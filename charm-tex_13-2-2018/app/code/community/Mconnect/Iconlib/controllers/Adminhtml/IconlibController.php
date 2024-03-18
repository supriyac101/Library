<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php

class Mconnect_Iconlib_Adminhtml_IconlibController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('iconlib/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Icon Manager'), Mage::helper('adminhtml')->__('Icon Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('iconlib/iconlib')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('iconlib_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('iconlib/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Icon Manager'), Mage::helper('adminhtml')->__('Icon Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Icon News'), Mage::helper('adminhtml')->__('Icon News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('iconlib/adminhtml_iconlib_edit'))
				->_addLeft($this->getLayout()->createBlock('iconlib/adminhtml_iconlib_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iconlib')->__('Icon does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {

			if($this->getRequest()->getParam('id') != '')
			{
				$id = $this->getRequest()->getParam('id');
			}
			else
			{
				$id = 0;
			}
		
			$model = Mage::getModel('iconlib/iconlib');
			$model->getResource()->beginTransaction();		
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {				
				$model->save();
				$iconId = $model->getId();


            } catch (Exception $e) {
				$model->getResource()->rollback();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $id));
					return;
				}
				$this->_redirect('*/*/');
				return;				
            }
						
			if(isset($_FILES['icon']['name']) && $_FILES['icon']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('icon');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					
					// We set media as the upload dir
                    $path_dir = Mage::getBaseDir('media') .DS.'admin_mconnect_iconlib_uploads'.DS.$iconId;
                    
                    if (is_dir($path_dir))
                    {
                        $dir = $iconId;
                    }
                    else
                    {
                        mkdir($path_dir,0777);
                        $dir = $iconId;
                    }					
							
					// We set media as the upload dir
                    $path = $path_dir.DS;
					$uploader->save($path, $_FILES['icon']['name']);
					
					//this way the name is saved in DB
					$media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'admin_mconnect_iconlib_uploads/'.$dir.'/';
					$data['iconfilename'] = $_FILES['icon']['name'];
					$data['icon'] = $media.$_FILES['icon']['name'];
					
					// Update data for Icon path and Icon file name.					
					$model->setData($data)
						->setId($iconId);
				
					if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
						$model->setCreatedTime(now())
							->setUpdateTime(now());
					} else {
						$model->setUpdateTime(now());
					}					
					$model->save();					
					// Update data for Icon path and Icon file name.															
					
					
				} catch (Exception $e) {
					$model->getResource()->rollback();
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);

					if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $iconId));
					return;
					}
					$this->_redirect('*/*/');
					return;
				
		        }
				
			}
			$model->getResource()->commit();	
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('iconlib')->__('Icon was successfully saved'));
			Mage::getSingleton('adminhtml/session')->setFormData(false);
			if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/edit', array('id' => $iconId));
			return;
			}
			$this->_redirect('*/*/');
			return;			  						
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iconlib')->__('Unable to find Icon to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
            $model = Mage::getModel('iconlib/iconlib');
            $iconLib2ProductsModel = Mage::getModel('iconlibtoproducts/iconlibtoproducts');                
			try {
                
                $iconLib2ProductsModelCollection = $iconLib2ProductsModel->getCollection();    
                $iconLib2ProductsModelCollection->addFieldToFilter('iconid',$this->getRequest()->getParam('id'));                            
                $iconLib2ProductsModelCollection->getSelect()->order('productid', 'asc');
                //echo $iconLib2ProductsModelCollection->getSelect(); exit;
                $iconLib2ProductsModelCollectionData = $iconLib2ProductsModelCollection->getData();
                $_productNames = array();
                $productModel = Mage::getModel('catalog/product');
                foreach($iconLib2ProductsModelCollectionData as $relData)
                {
                    $_productNames[] = $productModel->load($relData['productid'])->getName();                                    
                }
                $_productNames = array_unique($_productNames);
                $_productNamesStr = implode(', ',$_productNames);
                if($iconLib2ProductsModelCollection->count() > 0)
                {
                    throw new Exception('Still '.count($_productNames).' Product(s) ['.$_productNamesStr.'] are associated with requested Icon for deletion.');             
                }
                				
				$model->getResource()->beginTransaction(); 
                $IconFileName = $model->load($this->getRequest()->getParam('id'))->getIconfilename();                        
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				$model->getResource()->commit();
                
                                
                if(file_exists(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$this->getRequest()->getParam('id').'/'.$IconFileName)){
                    @unlink(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$this->getRequest()->getParam('id').'/'.$IconFileName);
                    @rmdir(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$this->getRequest()->getParam('id'));
                }                   	 
                         
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Icon was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
                $model->getResource()->rollback();
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $iconlibIds = $this->getRequest()->getParam('iconlib');
        if(!is_array($iconlibIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Icon(s)'));
        } else {
            $model = Mage::getModel('iconlib/iconlib');
            $iconLib2ProductsModel = Mage::getModel('iconlibtoproducts/iconlibtoproducts');
            $productModel = Mage::getModel('catalog/product');
                
                
                $_cantDeleteArr = array();
                $_IconProductMappingMsg = array();
                foreach ($iconlibIds as $iconlibId) {
                $_productNames = array();
                $iconLib2ProductsModelCollection = $iconLib2ProductsModel->getCollection();    
                $iconLib2ProductsModelCollection->addFieldToFilter('iconid',$iconlibId);                            
                $iconLib2ProductsModelCollection->getSelect()->order('productid', 'asc');
                //echo $iconLib2ProductsModelCollection->getSelect(); exit;
                $iconLib2ProductsModelCollectionData = $iconLib2ProductsModelCollection->getData();
                
                
                if($iconLib2ProductsModelCollection->count() > 0)
                {
                    foreach($iconLib2ProductsModelCollectionData as $relData)
                    {
                        $_productNames[] = $productModel->load($relData['productid'])->getName();                                                            
                    }                    
                    $_cantDeleteArr[$iconlibId] = implode(', ',array_unique($_productNames));
                }
                
                }
                
                $iconlibIds_final = array_unique(array_diff($iconlibIds,array_keys($_cantDeleteArr)));                
                //echo '<pre>'; print_r($_cantDeleteArr); exit;
                
                            
            try {                
                $model->getResource()->beginTransaction();
                
                foreach ($_cantDeleteArr as $key => $data) {
                    $_iconLabel = $model->load($key)->getIconlabel();
                    $_IconProductMappingMsg[] = 'Icon '.$_iconLabel.' =&gt; Product(s) ['.$_cantDeleteArr[$key].']';                
                }
                
                $_IconProductMappingMsgString = implode('<br />',$_IconProductMappingMsg);
                if($_IconProductMappingMsgString != '')
                {
                    Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('<span style="float: right;">&nbsp;"=&gt;" indicates Association<br></span>'.$_IconProductMappingMsgString));    
                }
                
                foreach ($iconlibIds_final as $iconlibId) {
                    $IconFileName = $model->load($iconlibId)->getIconfilename();                
                    $iconlib = $model->load($iconlibId);
                    $iconlib->delete();                    
                    if(file_exists(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$iconlibId.'/'.$IconFileName)){
                        @unlink(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$iconlibId.'/'.$IconFileName);
                        @rmdir(Mage::getBaseDir('media').'/admin_mconnect_iconlib_uploads/'.$iconlibId);
                    }                    
                }
                
                if(count($iconlibIds_final) > 0)
                {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($iconlibIds_final)));    
                }
                
                
                
                                
                $model->getResource()->commit();
            } catch (Exception $e) {
                $model->getResource()->rollback();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $iconlibIds = $this->getRequest()->getParam('iconlib');
        if(!is_array($iconlibIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Icon(s)'));
        } else {
            try {
                foreach ($iconlibIds as $iconlibId) {
                    $iconlib = Mage::getSingleton('iconlib/iconlib')
                        ->load($iconlibId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($iconlibIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

	public function downloadThisAction()
    {
		$id = $this->getRequest()->getParam('id');
		$IconData = Mage::getModel('iconlib/iconlib')->load($id)->getData();
        $fileName = $IconData['iconfilename'];		
		header('Content-type: application/file');
        header('Content-Disposition: attachment; filename='.$fileName);
        readfile(Mage::getBaseDir('media').DS.'admin_mconnect_iconlib_uploads'.DS.$id.DS.$fileName);
    }
	  
    public function exportCsvAction()
    {
        $fileName   = 'iconlib.csv';
        $content    = $this->getLayout()->createBlock('iconlib/adminhtml_iconlib_csvxmlgrid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'iconlib.xml';
        $content    = $this->getLayout()->createBlock('iconlib/adminhtml_iconlib_csvxmlgrid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}