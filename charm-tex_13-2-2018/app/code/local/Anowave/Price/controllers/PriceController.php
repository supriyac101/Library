<?php
class Anowave_Price_PriceController extends Mage_Adminhtml_Controller_Action 
{
	public function chooseAction()
	{
		$block = Mage::app()->getLayout()->createBlock
		(
	        'price/finder_grid'
	    );
        
	    if ($block) 
	    {
	    	
	    	
	        $this->getResponse()->setBody($block->toHtml());
	    }
	}
	
	public function gridAction()
	{
		/* Fork formkey (Required for AJAX based grids) */
		$this->getLayout()->createBlock('core/template', 'formkey')->setTemplate('formkey.phtml');
		
		$block = $this->getLayout()->createBlock
		(
	        'price/grid'
	    );

	    if ($block) 
	    {
	        $this->getResponse()->setBody($block->toHtml());
	    }
	}
	
	public function saveAction()
	{
		if ($this->getRequest()->getPost()) 
		{
			try 
			{
				$data = array_merge(array
				(
					'price_customer_id' 	=> 		 @$this->getRequest()->getParam('price_customer_id'),
					'price_product_id' 		=> 		 @$this->getRequest()->getParam('price_product_id'),
					'price' 				=> 		 @$this->getRequest()->getParam('price'),
					'price_type'			=> 		 @$this->getRequest()->getParam('price_type'),
					'price_special' 		=> 		 @$this->getRequest()->getParam('price_special'),
					'price_discount' 		=> 		 @$this->getRequest()->getParam('price_discount'),
					'price_apply_further' 	=> (int) @$this->getRequest()->getParam('price_apply_further')
				));

				$model = Mage::getModel('price/price');
				
				$model->setData($data);
				
				$model->save();
				
				/* Save categories */
				if ($this->getRequest()->getParam('price_category_id') && is_array($this->getRequest()->getParam('price_category_id')))
				{
					$categories = array_unique
					(
						$this->getRequest()->getParam('price_category_id')
					);
					
					if ($categories)
					{
						foreach ($categories as $entity)
						{
							$category = Mage::getModel('price/price_category');
							
							$category->setPriceId($model->getId());
							$category->setPriceCategoryId($entity);
							
							$category->save();
						}
					}
				}
				
				/* Trigger event */
				Mage::dispatchEvent('anowave_price_save_after', array
				(
					'controller' 	=> $this,
					'model' 		=> $model
				));
			}
			catch (Exception $e) 
			{
				return;
			}
		}
	}
	
	public function deleteAllAction()
	{
		$ids = $this->getRequest()->getParam('price_id');

		if (is_array($ids))
		{
			foreach ($ids as $id)
			{
				Mage::getModel('price/price')->load($id)->delete();
			}
			
			Mage::getSingleton('adminhtml/session')->addSuccess
			(
				$this->__('Total of %d price record(s) were deleted.', count($ids))
			);
		}

		$this->_redirect('adminhtml/customer/edit', array
		(
			'id' 	=> (int) $this->getRequest()->getParam('id'),
			'back'	=> 'edit',
			'tab' 	=> 'customer_info_tabs_tab'
		));
	}
	
	public function treeAction()
	{
    	$this->getResponse()->setBody
    	(
	        $this->getLayout()->createBlock('price/categories')->getCategoryChildrenJson($this->getRequest()->getParam('category'))
	    );
	}
	
	
	/**
	* Check allow or not access to ths page
	*
	* @return bool - is allowed to access this menu
	*/
	protected function _isAllowed()
	{
		return true;
	}
}