<?php

class Best4Mage_FrontendConfigurableProductMatrix_IndexController extends Mage_Core_Controller_Front_Action
{
	
	public function thirdgridAction(){
		$html = '';
		if($pid = $this->getRequest()->getParam('pid',false)){
			$responseArr = array();
			$product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app()->getStore()->getId() )->load ($pid);
			$posts = $this->getRequest()->getParams();
			$third = array();
			unset($posts['pid']);
			foreach($posts as $key => $pst){
				$keyArr = explode('::',$key);
				$third[$keyArr[0]] = array('id' => $keyArr[1],'option' => $pst);
			}
			Mage::register('current_product',$product);
			Mage::register('product',$product);
			$this->loadLayout();
			$fcpmThree = $this->getLayout()->createBlock('frontendconfigurableproductmatrix/product_view_matrix');
			$responseArr['html'] = $fcpmThree->setSuperAttrThird($third)->setTemplate('frontendconfigurableproductmatrix/configurablethird.phtml')->toHtml();
			$responseArr['evalJs'] = $fcpmThree->getFixedTblJs();
			$html = Mage::helper('core')->jsonEncode($responseArr);
		}
		$this->getResponse()->setBody($html);
	}
	
}
