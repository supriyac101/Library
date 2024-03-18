<?php
/**
 * Anowave Magento Price Per Customer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Price
 * @copyright 	Copyright (c) 2016 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
class Anowave_Price_Block_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	* Prepare form before rendering HTML
	*
	* @return Mage_Adminhtml_Block_Widget_Form
	*/
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form
        (
            array
            (
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array
                (
                	'id' => $this->getRequest()->getParam('id'))
                ),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        
       
        $form->setUseContainer(false);
      

        $fieldset = $form->addFieldset
        (
            'base_fieldset', array
            (
                'legend' 	=> Mage::helper('core')->__('Add product price'),
                'class'  	=> 'fieldset-wide',
                'expanded' 	=> false
            )
        );

        $fieldset->addType('finder', Mage::getConfig()->getBlockClassName('price/finder'));
        $fieldset->addType('handle', Mage::getConfig()->getBlockClassName('price/handle'));
        
		$fieldset->addField('trigger', 'finder', array
		(
			'name' 		=> 'trigger',
			'label' 	=> Mage::helper('core')->__('Product'),
			'value' 	=> Mage::helper('core')->__('Product'),
			'class' 	=> 'button',
			'onclick'	=> '',
			'style'		=> 'width:700px'
		));
		
		$fieldset->addField('price_product_id', 'hidden', array
		(
			'name' 		=> 'price_product_id',
			'class' 	=> 'button'
		));
		
		$fieldset->addType('tree', 'Anowave_Price_Block_Renderer_Tree');
		
		$fieldset->addField('price_categories', 'tree', array
		(
			'name' 		=> 'price_categories',
			'label'		=> 'Apply for categories'
		));
		
		$fieldset->addField('price_customer_id', 'hidden', array
		(
			'name' 		=> 'price_customer_id',
			'class' 	=> 'button',
			'value'		=> $this->getRequest()->getParam('id')
		));
		
	 	$t = $fieldset->addField('price_type', 'select', array
	 	(
            'name'  => 'price_type',
            'label' => Mage::helper('adminhtml')->__('Price type'),
            'title' => Mage::helper('adminhtml')->__('Price type'),
            'required' => false,
            'values' => array
            (
            	Anowave_Price_Model_Observer::PRICE_FIXED  			=> __('Fixed price'),
            	Anowave_Price_Model_Observer::PRICE_DISCOUNT 		=> __('Discount')
            ),
            'after_element_html' => '<p class="nm"><small>Select price type.</small></p>'
        )); 
		
        $f = $fieldset->addField
        (
            'price', 'text',array
            (
                'label' 			 => Mage::helper('core')->__('Fixed Price'),
                'name' 				 => 'price',
                'after_element_html' => '<p class="nm"><small>Leave blank to use original price</small></p>'
            )
        );

        $p = $fieldset->addField
        (
            'price_discount', 'text',array
            (
                'label' 				=> Mage::helper('core')->__('Discount in %'),
                'name' 					=> 'price_discount',
                'after_element_html' 	=> '<p class="nm"><small>% between 0% - 100%</small></p>'
            )
        );
        
        $fieldset->addField
        (
        	'price_special', 'text',array
        	(
        		'label' 			 => Mage::helper('core')->__('Special price'),
        		'name' 				 => 'price_special',
        		'after_element_html' => '<p class="nm"><small>Leave blank to use original special price</small></p>'
        	)
        );
        
		Mage::dispatchEvent('anowave_price_form_prepare_after', array
		(
			'fieldset' => $fieldset
		));
        
		$further = $fieldset->addField
		(
			'price_apply_further', 'checkbox',array
			(
				'label' 			 => Mage::helper('core')->__('Apply futher discount'),
				'name' 				 => 'price_apply_further',
				'after_element_html' => Mage::app()->getLayout()->createBlock('price/checkbox')->toHtml(),
				'value'				 => 1
			)
		);
		
		$fieldset->addField
		(
			'button', 'handle', array
			(
				'value'  				=> 'Submit',
				'class'					=> 'scalable',
				'tabindex' 				=> 1
			)
		);

		$this->setForm($form)->setChild
		(
			'form_after', Mage::app()->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
							->addFieldMap($t->getHtmlId(),$t->getName())
							->addFieldMap($f->getHtmlId(),$f->getName())
							->addFieldMap($p->getHtmlId(),$p->getName())
							->addFieldMap($further->getHtmlId(),$further->getName())
							->addFieldDependence($f->getName(),$t->getName(),Anowave_Price_Model_Observer::PRICE_FIXED)
							->addFieldDependence($p->getName(),$t->getName(),Anowave_Price_Model_Observer::PRICE_DISCOUNT)
							->addFieldDependence($further->getName(),$t->getName(),Anowave_Price_Model_Observer::PRICE_FIXED)
		);

        return parent::_prepareForm();
    }
}