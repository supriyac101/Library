<?php

class Bcs_Dailyfeature_Adminhtml_DailyfeatureController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('dailyfeature/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('dailyfeature/dailyfeature')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('dailyfeature_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('dailyfeature/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('dailyfeature/adminhtml_dailyfeature_edit'))
				->_addLeft($this->getLayout()->createBlock('dailyfeature/adminhtml_dailyfeature_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailyfeature')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($data['stores'])) {
				if(in_array('0',$data['stores'])){
					$data['store_id'] = '0';
				}
				else{
					$data['store_id'] = implode(",", $data['stores']);
				}
				unset($data['stores']);
			}
	  		 //echo $data['store_id']; exit;
			 
			//if(empty($data['limit']))
			//$data['limit']=1000;
			

			$_dealDate=explode('-',$data['deal_date_from']);
			$_dealDate = $_dealDate[2].'-'.$_dealDate[0].'-'.$_dealDate[1];
			$data['deal_date_from']=date('Y-m-d',strtotime($_dealDate));
			
			$_dealDate=explode('-',$data['deal_date_to']);
			$_dealDate = $_dealDate[2].'-'.$_dealDate[0].'-'.$_dealDate[1];
			$data['deal_date_to']=date('Y-m-d',strtotime($_dealDate));
			
						
			$model = Mage::getModel('dailyfeature/dailyfeature');
			//print_r($data);
			//exit;
			
			if($this->getRequest()->getParam('id')){
				$model->setData($data)->setId($this->getRequest()->getParam('id'));
			}else{
				$model->setData($data);
			}
			
			try {			
							
				/*if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}*/	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailyfeature')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailyfeature')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('dailyfeature/dailyfeature');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $dailyfeatureIds = $this->getRequest()->getParam('dailyfeature');
        if(!is_array($dailyfeatureIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($dailyfeatureIds as $dailyfeatureId) {
                    $dailyfeature = Mage::getModel('dailyfeature/dailyfeature')->load($dailyfeatureId);
                    $dailyfeature->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($dailyfeatureIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $dailyfeatureIds = $this->getRequest()->getParam('dailyfeature');
        if(!is_array($dailyfeatureIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($dailyfeatureIds as $dailyfeatureId) {
                    $dailyfeature = Mage::getSingleton('dailyfeature/dailyfeature')
                        ->load($dailyfeatureId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($dailyfeatureIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'dailyfeature.csv';
        $content    = $this->getLayout()->createBlock('dailyfeature/adminhtml_dailyfeature_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'dailyfeature.xml';
        $content    = $this->getLayout()->createBlock('dailyfeature/adminhtml_dailyfeature_grid')
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