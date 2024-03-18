<?php
class Extendware_EWPRibbon_Block_Adminhtml_Ribbon_Edit_Tab_Category_Ribbon_Image_Chooser extends Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Grid
{
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $this->setItemLabelJs('trElement.down("td").next().next().next().innerHTML');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('ewpribbon/image_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
    	$this->addColumn('image_id', array(
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
        
        $this->addColumn('image', array(
            'header'    => $this->__('Image'),
            'index'     => 'path',
        	'renderer' => 'Extendware_EWPRibbon_Block_Adminhtml_Image_Grid_Renderer_Image',
        	'sortable' => false,
        	'filter' => false,
        ));
        
        $this->addColumn('path', array(
            'header'    => $this->__('Path'),
            'index'     => 'path',
        	'width'		=> '200px',
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
        
        return parent::_prepareColumns();
    }
}
