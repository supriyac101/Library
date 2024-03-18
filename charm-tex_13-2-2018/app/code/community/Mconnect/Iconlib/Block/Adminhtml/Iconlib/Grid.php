<?php
  /**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */ 
?>
<?php

class Mconnect_Iconlib_Block_Adminhtml_Iconlib_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('iconlibGrid');
      $this->setDefaultSort('iconlib_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('iconlib/iconlib')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('iconlib_id', array(
          'header'    => Mage::helper('iconlib')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'iconlib_id',
      ));

      $this->addColumn('iconlabel', array(
          'header'    => Mage::helper('iconlib')->__('Icon label'),
		  'width'     => '150px',
          'align'     =>'left',
          'index'     => 'iconlabel',
      ));
	  
      $this->addColumn('image', array(
          'header'    => Mage::helper('iconlib')->__('Icon Image'),
		  'width'     => '150px',
          'align'     =>'center',
          'index'     => 'icon',
		  'renderer'  => 'iconlib/adminhtml_iconlib_renderer_image',	
          'inlinecss' => 'style="width:100px; border:1px solid #000;"',	
          'filter'    => false,
          'sortable'  => false,		  
      ));	  

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('iconlib')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('iconlib')->__('Status'),
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
                'header'    =>  Mage::helper('iconlib')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('iconlib')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('iconlib')->__('Download'),
                        'url'       => array('base'=> '*/*/downloadThis'),
                        'field'     => 'id'
                    )					
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('iconlib')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('iconlib')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('iconlib_id');
        $this->getMassactionBlock()->setFormFieldName('iconlib');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('iconlib')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('iconlib')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('iconlib/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('iconlib')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('iconlib')->__('Status'),
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