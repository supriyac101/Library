<?php

class Bcs_Dailyfeature_Block_Adminhtml_Dailyfeature_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('dailyfeature_form', array('legend'=>Mage::helper('dailyfeature')->__('Item information')));
     
	 $products = Mage::getModel('catalog/product')->getCollection();                       
	 $products->addAttributeToSelect('id');
	 $products->addAttributeToSelect('name');
	 $products->addAttributeToSort('name', 'ASC');					
	 
	 $productValues = array();
	 foreach ($products as $prodModel) {
		$productValues[$prodModel->getId()] = array(
			'value'=> $prodModel->getId(),
			'label'=> $prodModel->getName().'('.$prodModel->getTypeId().'-'.$prodModel->getId().')'
		);
		
	 }
	 //ksort($productValues);
	//Mage::getModel('dailyfeature/dailyfeature')->test();
	//exit;
	  
	  $fieldset->addField('product', 'select', array(
          'label'     => Mage::helper('dailyfeature')->__('Product'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product',
		  'values'	  => $productValues
      ));
	       
		$dateFormatIso = Mage::app()->getLocale() ->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);	
		$dateFormatIso='M-d-yy';
		$fieldset->addField('deal_date_from', 'date', array(
		'label' => Mage::helper('dailyfeature')->__('Deal Date From'),
		'title' => Mage::helper('dailyfeature')->__('Deal Date From'),
		'name' => 'deal_date_from',
		'image' => $this->getSkinUrl('images/grid-cal.gif'),
		'format' => $dateFormatIso,
		'required' => true,
		'class'     => 'required-entry',
		));		
		$dateFormatIso='M-d-yy';
		$fieldset->addField('deal_date_to', 'date', array(
		'label' => Mage::helper('dailyfeature')->__('Deal Date To'),
		'title' => Mage::helper('dailyfeature')->__('Deal Date To'),
		'name' => 'deal_date_to',
		'image' => $this->getSkinUrl('images/grid-cal.gif'),
		'format' => $dateFormatIso,
		'required' => true,
		'class'     => 'required-entry',
		));
	  
	  if (!Mage::app()->isSingleStoreMode()) {
		  $fieldset->addField('store_id', 'select', array(
			  'name' => 'stores[]',
			  'label' => Mage::helper('banner')->__('Store View'),
			  'title' => Mage::helper('banner')->__('Store View'),
			  'required' => true,
			  'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		  ));
	  } else {
		  $fieldset->addField('store_id', 'hidden', array(
			  'name' => 'stores[]',
			  'value' => Mage::app()->getStore(true)->getId(),
		  ));
	  }
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('dailyfeature')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('dailyfeature')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('dailyfeature')->__('Disabled'),
              ),
          ),
      ));
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('dailyfeature')->__('Additional text'),
          'title'     => Mage::helper('dailyfeature')->__('Additional text'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
	  	   
     
      if ( Mage::getSingleton('adminhtml/session')->getDailyfeatureData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDailyfeatureData());
          Mage::getSingleton('adminhtml/session')->setDailyfeatureData(null);
      } elseif ( Mage::registry('dailyfeature_data') ) {
          $form->setValues(Mage::registry('dailyfeature_data')->getData());
      }
      return parent::_prepareForm();
  }
}
