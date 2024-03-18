<?php
class Anowave_Price_Block_Finder_Grid extends Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku
{
	public function __construct($arguments)
	{
		parent::__construct($arguments = array());

        if ($this->getRequest()->getParam('current_grid_id')) 
        {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } 
        else 
        {
            $this->setId('chooseGrid');
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
        $this->setCheckboxCheckCallback("$form.chooserGridCheckboxCheck.bind($form)");
        $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        $this->setDefaultSort('sku');
        
        $this->setUseAjax(true);
        
        if ($this->getRequest()->getParam('collapse')) 
        {
            $this->setIsCollapsed(true);
        }
	}
	

	/**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareCollection()
    {               
        $this->setCollection
        (
        	Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')
        );
        
        /**
         * Apply some of the filters manually.
         */
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _addColumnFilterToCollection($column)
    {
    	if ($this->getCollection()) 
    	{
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
   
            if ($column->getFilterConditionCallback()) 
            {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } 
            else 
            {
                $cond = $column->getFilter()->getCondition();
                
                if ($field && isset($cond)) 
                {
                    $this->getCollection()->addAttributeToFilter($field , $cond);
                }
            }
        }
        return $this;
    }
    
    /**
     * Add store filter 
     * 
     * @param unknown $collection
     * @param unknown $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
    	if (!$value = $column->getFilter()->getValue())
    	{
    		return;
    	}

    	$this->getCollection()->setStoreId($value);
    }

	public function _prepareColumns()
	{
		$this->addColumn('sku', array
		(
			'header'    => Mage::helper('sales')->__('Select'),
            'type'      => 'radio',
            'name'      => 'sku',
            'html_name'	=> 'sku',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'sku',
            'use_index' => true,
            'width'		=> '50px'
        ));

        $this->addColumn('entity_id', array
        (
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));

        $this->addColumn('chooser_sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '200px',
            'index'     => 'sku'
        ));
        
        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));
        
         $this->addColumn('price', array
         (
            'header'		=> Mage::helper('catalog')->__('Price'),
            'type'  		=> 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'index' 		=> 'price',
         	'width'     	=> '110px'
        ));
        
         $this->addColumn('special_price', array
         (
            'header'		=> Mage::helper('catalog')->__('Special price'),
            'type'  		=> 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'index' 		=> 'special_price',
         	'width'     	=> '110px'
        ));
         
         if (!Mage::app()->isSingleStoreMode()) 
         {
         	$this->addColumn('store_id', array(
         		'header'        => Mage::helper('catalog')->__('Store View'),
         		'index'         => 'store_id',
         		'type'          => 'store',
         		'store_all'     => true,
         		'store_view'    => true,
         		'sortable'      => true,
         		'filter_condition_callback' => array($this,'_filterStoreCondition'),
         	));
         }
	}
	
	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		
		return Mage::app()->getStore($storeId);
	}
}