<?php

class Custom_Careersbanner_Block_Adminhtml_Careersbanner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('careersbannerGrid');
      $this->setDefaultSort('careersbanner_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('careersbanner/careersbanner')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

    protected function _prepareColumns()
    {
        $this->addColumn('careersbanner_id', array(
            'header'    => Mage::helper('careersbanner')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'careersbanner_id',
        ));
        
        $this->addColumn('filename', array(
            'header'    => Mage::helper('careersbanner')->__('Image'),
            'align'     =>'left',
            'index'     => 'filename',
            'type'  => 'image',
            'renderer' => 'careersbanner/adminhtml_careersbanner_grid_renderer_image',  
            'width'  => '100px'
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('careersbanner')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
  
        /*
        $this->addColumn('content', array(
              'header'    => Mage::helper('careersbanner')->__('Item Content'),
              'width'     => '150px',
              'index'     => 'content',
        ));
        */
  
        $this->addColumn('status', array(
            'header'    => Mage::helper('careersbanner')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('careersbanner')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('careersbanner')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('careersbanner')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('careersbanner')->__('XML'));
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('careersbanner_id');
        $this->getMassactionBlock()->setFormFieldName('careersbanner');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('careersbanner')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('careersbanner')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('careersbanner/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('careersbanner')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('careersbanner')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}