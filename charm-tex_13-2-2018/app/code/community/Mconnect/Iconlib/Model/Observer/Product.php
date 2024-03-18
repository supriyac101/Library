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
 
class Mconnect_Iconlib_Model_Observer_Product
{
    /**
     * Inject one tab into the product edit page in the Magento admin
     *
     * @param Varien_Event_Observer $observer
     */
    public function injectTabs(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        //var_dump(Mage::getStoreConfig('advanced/modules_disable_output/Mconnect_Iconlib',Mage::app()->getStore())); exit;
        if (!Mage::getStoreConfig('advanced/modules_disable_output/Mconnect_Iconlib',Mage::app()->getStore()) && $block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            if ($this->_getRequest()->getActionName() == 'edit' || $this->_getRequest()->getParam('type')) {
                $block->addTab('associate-to-iconlib-tab', array(
                    'label'     => 'Associate to Icon-Lib',
                    'content'   => $block->getLayout()->createBlock('adminhtml/template', 'custom-tab-content', array('template' => 'iconlib/iconlibcontent.phtml'))->toHtml(),
                ));
            }
        }
    } 
 
    /**
     * This method will run when the product is saved
     * Use this function to update the product model and save
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveIconLibTabData(Varien_Event_Observer $observer)
    {
        
        if(!Mage::getStoreConfig('advanced/modules_disable_output/Mconnect_Iconlib',Mage::app()->getStore()))
        {
		
		if(Mage::registry('product'))
		{
	     $icon2productAssociationLimit = Mage::getStoreConfig('iconlib/general/icontoproductassociationlimit',Mage::app()->getStore());
            if($this->_getRequest()->getPost()) {
            $iconIds = array(); 
			$iconCnt = 0;   
			$saturatedIconCnt = 0;        
            // Load the current product model  
            $product = Mage::registry('product');
            $productid = $product->getId();

			$iconIds = $this->_getRequest()->getPost('mcsiconlibarr');
			$prevIconIds = $this->_getRequest()->getPost('mcsiconlib_prev_stat_arr');
			
			$resultantCommonIconIds = array_intersect($prevIconIds,$iconIds);
			$resultantNewRequestedIconIds = array_diff($iconIds,$prevIconIds);
			$resultantDeleteRequestedIconIds = array_diff($prevIconIds,$resultantCommonIconIds);
			if(count($resultantDeleteRequestedIconIds) == 0 && count($iconIds) == 0 && count($prevIconIds) > 0)
			{
			$resultantDeleteRequestedIconIds = $prevIconIds;				
			}
           
            if(count($iconIds) > 0 && count($prevIconIds) == 0 && count($resultantNewRequestedIconIds) == 0)
            {
                $resultantNewRequestedIconIds = $iconIds;
            }
            
									
			/*echo '<pre>'; print_r($iconIds); echo '<br />========';
			echo '<pre>'; print_r($prevIconIds); echo '<br />========';
						
			echo '<pre>'; print_r($resultantCommonIconIds); echo '<br />========';
			echo '<pre>'; print_r($resultantNewRequestedIconIds); echo '<br />========';
			echo '<pre>'; print_r($resultantDeleteRequestedIconIds); echo '<br />========';
	              exit;*/			


				$iconRelationModel = Mage::getModel('iconlibtoproducts/iconlibtoproducts');
				$iconRelationModel->getResource()->beginTransaction();
				try {							

					// Removes the Relations
					foreach(array_unique($resultantDeleteRequestedIconIds) as $iconId)
					{
						try {

							$iconRelationModelCollectionToDelete = $iconRelationModel->getCollection()
								->addFieldToFilter('productid',$productid)
								->addFieldToFilter('iconid',$iconId)
								->getData();
								
							foreach($iconRelationModelCollectionToDelete as $relationId)
							{	
								try {
									$iconRelationModel->load($relationId['iconlib_to_products_relation_id'])->delete();
									//$iconCnt--;
								} catch (Exception $e) {
									$iconRelationModel->getResource()->rollback();
									Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
									return;
								}																
							}
							
						} catch (Exception $e) {
							$iconRelationModel->getResource()->rollback();
							Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
							return;
						}
					}
					// Removes the Relations

					$iconCnt = $iconRelationModel->getCollection()
					->addFieldToFilter('productid',$productid)->count();

									
					foreach(array_unique($resultantNewRequestedIconIds) as $iconId)
					{
						try {
						  
						  if($iconCnt < $icon2productAssociationLimit)
						  {
							$data = array();            			
							$data['productid'] = $productid;
							$data['iconid'] = $iconId;
							$iconRelationModel->setData($data);
							if ($iconRelationModel->getCreatedTime == NULL || $iconRelationModel->getUpdateTime() == NULL) {
								$iconRelationModel->setCreatedTime(now())
									->setUpdateTime(now());
							} else {
								$iconRelationModel->setUpdateTime(now());
							}							

							$iconRelationModel->save();
							$iconCnt++;					
						  }
						  else
						  {
						  	$saturatedIconCnt++;
						  }
						  
						} catch (Exception $e) {
							$iconRelationModel->getResource()->rollback();
							Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
							return;
						}
					}
					
					if($saturatedIconCnt > 0)
					{
						Mage::getSingleton('adminhtml/session')->addNotice('No. of Requested icons to associated with product, exceeded the limit set in configuration.<br />Remaining Icons Count: '.$saturatedIconCnt);					
					}
					/*echo $iconCnt,'<br />';   
					echo $saturatedIconCnt,'<br />';
					exit;*/					
					$iconRelationModel->getResource()->commit();					
										

				} catch (Exception $e) {
						$iconRelationModel->getResource()->rollback();
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        return;
				}				
			            
          }                   
        }
		
		}
        else
        {
             Mage::getSingleton('adminhtml/session')->addError("Icon Library Extension utility is Disabled.");
             return;
        }
    
    }     
    /*** Shortcut to getRequest ***/
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
}