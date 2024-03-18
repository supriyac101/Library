<?php

class Extendware_EWPRibbon_Block_Adminhtml_Image_Grid extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setDefaultSort('image_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ewpribbon/image')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('image_id', array(
        	'type'		=> 'number',
            'header'    => $this->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'image_id'
        ));
		
        $this->addColumn('namespace', array(
            'header'    => $this->__('Namespace'),
            'index'     => 'namespace',
        	'type'      => 'options',
            'options'   => Mage::getModel('ewpribbon/image')->getNamespaceOptionModel()->toGridOptionArray(),
        	'width'     => '100px',
        ));
        
        $this->addColumn('path', array(
            'header'    => $this->__('Path'),
            'index'     => 'path',
        	'width'		=> '200px',
        ));
        
        $this->addColumn('image', array(
            'header'    => $this->__('Image'),
            'index'     => 'path',
        	'renderer' => 'Extendware_EWPRibbon_Block_Adminhtml_Image_Grid_Renderer_Image',
        	'sortable' => false,
        	'filter' => false,
        ));
        
        $this->addColumn('width', array(
            'header'    => $this->__('Width'),
            'index'     => 'width',
        	'type'		=> 'number',
        	'default'	=> ' ---- ',
        ));
        
        $this->addColumn('height', array(
            'header'    => $this->__('Height'),
            'index'     => 'height',
        	'type'		=> 'number',
        	'default'	=> ' ---- ',
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
        $this->setMassactionIdField('image_id');
        $this->getMassactionBlock()->setFormFieldName('ids');

		$this->getMassactionBlock()->addItem('delete', array(
			'label' => $this->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete'),
			'confirm' => $this->__('Are you sure?')
		));
		
        return $this;
    }
}