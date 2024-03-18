<?php

class Bcs_Dailyfeature_Block_Adminhtml_Dailyfeature_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('dailyfeatureGrid');
      $this->setDefaultSort('update_time');
	  //print_r(get_class_methods($this));
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('dailyfeature/dailyfeature')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('dailyfeature_id', array(
          'header'    => Mage::helper('dailyfeature')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'dailyfeature_id',
      ));

      $this->addColumn('product', array(
          'header'    => Mage::helper('dailyfeature')->__('Product id'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'product',
      ));
	  
	  $this->addColumn('dealdatefrom', array(
          'header'    => Mage::helper('dailyfeature')->__('Scheduled Date From'),
          'align'     =>'left',
          'index'     => 'deal_date_from',
		  'width'     => '140px',
/*		  'type' => 'datetime',
		  'format' => 'M-d-yy',*/
      ));
	  
	  
	  	  $this->addColumn('dealdateto', array(
          'header'    => Mage::helper('dailyfeature')->__('Scheduled Date To'),
          'align'     =>'left',
          'index'     => 'deal_date_to',
		  'width'     => '140px',
/*		  'type' => 'datetime',
		  'format' => 'M-d-yy',*/
      ));
	  
      $this->addColumn('content', array(
          'header'    => Mage::helper('dailyfeature')->__('Content'),
          'align'     =>'left',
          'index'     => 'content',
      ));
	  

	  /*	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('dailyfeature')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  */
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('dailyfeature')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('dailyfeature')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		$this->setDefaultSort('dealdate')
                     ->setDefaultDir("desc");
		
		//$this->addExportType('*/*/exportCsv', Mage::helper('dailyfeature')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('dailyfeature')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('dailyfeature_id');
        $this->getMassactionBlock()->setFormFieldName('dailyfeature');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('dailyfeature')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('dailyfeature')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('dailyfeature/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('dailyfeature')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('dailyfeature')->__('Status'),
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
