<?php

class Potato_PrintAnyOrder_Model_Source_Font
{
    const USE_DEFAULT_CODE = 0;
    const USE_DEFAULT_LABEL = 'Use default';
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => self::USE_DEFAULT_CODE,
                'label' => $this->_getHelper()->__(self::USE_DEFAULT_LABEL),
            )
        );
        $collection = new Varien_Data_Collection_Filesystem();
        $collection
            ->addTargetDir($this->_getConfig()->getFontFolderPath())
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->loadData()
        ;
        foreach ($collection as $item) {
            $options[] = array(
                'value' => $item->getBasename(),
                'label' => $item->getBasename(),
            );
        }
        return $options;
    }

    /**
     * @return Potato_PrintAnyOrder_Helper_Config
     */
    protected function _getConfig()
    {
        return Mage::helper('po_pao/config');
    }

    /**
     * @return Potato_PrintAnyOrder_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('po_pao');
    }
}