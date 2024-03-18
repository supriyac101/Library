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
 
class Anowave_Price_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
        
        $this->setId('priceGrid');
        $this->setDefaultSort('price_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
	}

	/**
     * Prepare grid collection object
     *
     * @return Anowave_Price_Block_Price_Grid
     */
    protected function _prepareCollection()
    {
    	$collection = Mage::getModel('price/price')->getCollection();
    	
    	$collection->addProductData()->addFieldToFilter('price_customer_id', (int) $this->getRequest()->getParam('id'))->addProductFilter($this->getRequest());
    	$collection->getSelect()->group('price_id');
    	
        $this->setCollection($collection);

      	return parent::_prepareCollection();
    }
    
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('price_id');
		$this->getMassactionBlock()->setFormFieldName('price_id');
		$this->getMassactionBlock()->addItem('delete', array
		(
			'label'		=> $this->__('Delete'),
			'url'  		=> $this->getUrl('adminhtml/price/deleteAll', array
				(
					'id' => (int) $this->getRequest()->getParam('id')
				)
			),
			'confirm' 	=> $this->__('Are you sure?')
		));
		
		return $this;
	}

    protected function _prepareColumns()
    {
        $this->addColumn('price_product_id', array
        (
            'header'    => $this->__('Product'),
            'align'     => 'left',
            'index'     => 'price_product_id',
            'renderer'	=> 'Anowave_Price_Block_Renderer_Product'
        ));
        
        $this->addColumn('price_category_id', array
        (
            'header'    => $this->__('Category'),
            'align'     => 'left',
            'index'     => 'price_category_id',
            'renderer'	=> 'Anowave_Price_Block_Renderer_Category',
            'searchable' => false,
            'filter'	=> false
        ));
        
        $this->addColumn('price', array
        (
            'header'    => $this->__('Custom price'),
            'align'     => 'left',
            'index'     => 'price',
            'type'      => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode()
        ));
        
        $this->addColumn('price_discount', array
        (
            'header'    => $this->__('Discount'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'price_discount',
            'type'      => 'number',
            'renderer'	=> 'Anowave_Price_Block_Renderer_Discount'
        ));
        
        return parent::_prepareColumns();
    }

    
    public function getGridUrl()
    {
    	return $this->getUrl('adminhtml/price/grid', array
    	(
    		'id' => (int) $this->getRequest()->getParam('id')
    	));
    }
    
    public function getRowUrl($row)
    {
        return null;
    }
    
    protected function _getStore()
    {
        return Mage::app()->getStore
        (
        	(int) $this->getRequest()->getParam('store', 0)
        );
    }
}