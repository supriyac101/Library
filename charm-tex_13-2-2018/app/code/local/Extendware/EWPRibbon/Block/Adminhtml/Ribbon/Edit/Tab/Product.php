<?php

class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Product extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    	if ($this->getRibbon()->getId()) {
    		$selectedIds = $this->getSelectedProductIds();
    		if (empty($selectedIds) === false) {
				$this->setDefaultFilter(array('in_products'=>1));
    		}
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

	protected function _beforeToHtmlHtml() {
		$html = null;
		if ($this->canDisplayContainer()) {
			$html = '<div id="messages"><ul class="messages"><li class="notice-msg"><ul><li><span>';
			$html .= $this->__('Product overrides allow you to always include / exclude a product despite other conditions.');
			$html .= '</span></li></ul></li></ul></div>';
		}
    	return $html;
    }
    
    protected function getRibbon()
    {
        return Mage::registry('ew:current_ribbon');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedProductIds();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else if(empty($productIds) === false) {
				$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $collection->getSelect()->joinLeft(
        	array('ewpr_product' => $collection->getTable('ewpribbon/ribbon_product')), 
        	'`ewpr_product`.`product_id` = `e`.`entity_id` AND `ewpr_product`.`ribbon_id` = "' . (int)$this->getRibbon()->getId() . '"', 
        	array('ewstate' => 'state', 'ewfrom_date' => 'from_date', 'ewto_date' => 'to_date')
        );
        if ($this->isReadonly()) {
            $productIds = $this->getSelectedProductIds();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->getSelectedProductIds(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => $this->__('ID'),
            'sortable'  => true,
            'width'     => '50px',
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name',
        ));

        $this->addColumn('type', array(
            'header'    => $this->__('Type'),
            'width'     => '100px',
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => $this->__('Attrib. Set Name'),
            'width'     => '100px',
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'width'     => '70px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => $this->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => $this->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price',
        	'width' => '100px',
        ));
        
         $this->addColumn('from_date', array(
            'header'    => $this->__('From Date'),
			'index'     => 'ewfrom_date',
        	'type'		=> 'datetime',
        	'editable'	=> true,
         	'edit_only'	=> true,
        	'filter'	=> false,
			'sortable'	=> false,
        	'input_width' => '60px',
        	'width' => '110px',
        ));
        
        $this->addColumn('to_date', array(
            'header'    => $this->__('To Date'),
			'index'     => 'ewto_date',
        	'type'		=> 'datetime',
        	'editable'	=> true,
        	'edit_only'	=> true,
			'filter'	=> false,
			'sortable'	=> false,
        	'input_width' => '60px',
        	'width' => '100px',
        ));
        
        $this->addColumn('state', array(
            'header'    => $this->__('State'),
			'index'     => 'ewstate',
        	'type'		=> 'select',
        	'editable'	=> true,
        	'edit_only'	=> true,
        	'options'	=> Mage::getModel('ewpribbon/ribbon_product')->getStateOptionModel()->toGridOptionArray(),
			'filter'	=> false,
			'sortable'	=> false,
        	'width'		=> '80px'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

	protected function getSelectedProductIds()
    {
    	$ids = $this->getRequest()->getPost('selected_products');
    	if (!is_array($ids) and $this->canDisplayContainer()) {
    		$ids = $this->getProductIds();
    	}
        return $ids;
    }
    
	public function getProductIds()
	{
		if ($this->hasData(__FUNCTION__) === false) {
			$ids = $this->getRibbon()->getProductCollection()->getColumnValues('product_id');
			$this->setData(__FUNCTION__, $ids);
		}
		
		return $this->getData(__FUNCTION__);
	}
	
	public function getProductsForSerializer()
	{
		if ($this->hasData(__FUNCTION__) === false) {
			$products = array();
			$collection = $this->getRibbon()->getProductCollection();
			foreach ($collection as $item) {
				$products[$item->getProductId()] = array();
				foreach (array('state', 'from_date', 'to_date') as $key) {
					$products[$item->getProductId()][$key] = $item->getData($key);
				}
			}
			$this->setData(__FUNCTION__, $products);
		}
		
		return $this->getData(__FUNCTION__);
	}
	
	public function isReadonly()
    {
        return false;
    }
}