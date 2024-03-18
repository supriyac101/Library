<?php
class Bcs_Dailyfeature_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		
        $todaysDeal = Mage::getModel('dailyfeature/dailyfeature')->getTodaysDeal();
		$_product = Mage::getModel('catalog/product')->load($todaysDeal['product']);		
        //echo $_product->getSku();

		if (!Mage::registry('product'))
		 Mage::register('product', $_product);
		
		if (!Mage::registry('current_product'))
		 Mage::register('current_product', $_product);
		 
		if (!Mage::registry('deal_product'))
		 Mage::register('deal_product', $todaysDeal);
		 			
		$this->loadLayout();
		$this->renderLayout();
    }
}