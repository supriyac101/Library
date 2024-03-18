<?php

class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Grid extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setDefaultSort('ribbon_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ewpribbon/ribbon')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('ribbon_id', array(
        	//'type'		=> 'number',
            'header'    => $this->__('ID'),
        	'index'     => 'ribbon_id',
            'align'     => 'right',
            'width'     => '50px',
        ));
		
        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'index'     => 'status',
        	'type'      => 'options',
            'options'   => Mage::getModel('ewpribbon/ribbon')->getStatusOptionModel()->toGridOptionArray(),
        	'width'     => '80px',
        ));

        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name',
        ));
        
        $this->addColumn('category_ribbon_text', array(
            'header'    => $this->__('Category Text'),
            'index'     => 'category_text',
        	'width'		=> '125px',
        ));
        
         $this->addColumn('product_ribbon_text', array(
            'header'    => $this->__('Product Text'),
            'index'     => 'product_text',
         	'width'		=> '125px',
        ));
        
        $this->addColumn('category_ribbon_id', array(
            'header'    => $this->__('Category Image'),
            'index'     => 'category_image_id',
        	'renderer' => 'Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Grid_Renderer_Ribbon',
        	'sortable' => false,
        	'filter' => false,
        ));
        
        $this->addColumn('product_ribbon_id', array(
            'header'    => $this->__('Product Image'),
            'index'     => 'product_image_id',
        	'renderer' => 'Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Grid_Renderer_Ribbon',
        	'sortable' => false,
        	'filter' => false,
        ));
        
        $this->addColumn('priority', array(
            'header'    => $this->__('Piority'),
            'index'     => 'priority',
        	'width'		=> '50px',
        ));
        
        $this->addColumn('created_at', array(
            'header'    => $this->__('Created'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => '155px',
            'gmtoffset' => true,
            'default'	=> ' ---- ',
        ));
		
        $this->addColumn('action', array(
			'header' => $this->__('Action'),
			'width' => '50px',
			'type' => 'action',
			'getter' => 'getId',
			'actions' => array(
				array(
					'caption' => $this->__('Edit'),
					'url' => array('base' => '*/*/edit'), 'field' => 'id'
				)
				
			),
			'filter' => false,
			'sortable' => false,
			'is_system' => true
		));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

	protected function _prepareMassaction(){
        $this->setMassactionIdField('ribbon_id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem('status', array(
			'label' => $this->__('Change status'),
			'url' => $this->getUrl('*/*/massStatus'),
			'confirm' => $this->__('Are you sure?'),
			'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => $this->__('Status'),
                         'values' => Mage::getSingleton('ewpribbon/ribbon_data_option_status')->toGridMassActionOptionArray()
                     )
             )
		));
		
		$this->getMassactionBlock()->addItem('delete', array(
			'label' => $this->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete'),
			'confirm' => $this->__('Are you sure?')
		));
		
        return $this;
    }
}