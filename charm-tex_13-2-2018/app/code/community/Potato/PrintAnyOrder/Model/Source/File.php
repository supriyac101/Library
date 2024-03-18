<?php

class Potato_PrintAnyOrder_Model_Source_File extends Mage_Core_Model_Config_Data
{
    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $_maxFileSize = 0;

    /**
     * Save uploaded file before saving config value
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_File
     */
    protected function _beforeSave()
    {
        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){
            $uploadDir = $this->_getUploadDir();
            try {
                $file = array();
                $tmpName = $_FILES['groups']['tmp_name'];
                $file['tmp_name'] = $tmpName[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $name = $_FILES['groups']['name'];
                $file['name'] = $name[$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = new Varien_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);

            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }
            $filename = $result['file'];
            if ($filename) {
                $this->setValue($filename);
            }
        }
        return $this;
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @throws Mage_Core_Exception
     */
    public function validateMaxSize($filePath)
    {
        if ($this->_maxFileSize > 0 && filesize($filePath) > ($this->_maxFileSize * 1024)) {
            throw Mage::exception('Mage_Core', Mage::helper('po_pao')->__('Uploaded file is larger than %.2f kilobytes allowed by server', $this->_maxFileSize));
        }
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('ttf');
    }

    protected function _getUploadDir()
    {
        return $this->_getConfig()->getFontFolderPath();
    }

    /**
     * @return Potato_PrintAnyOrder_Helper_Config
     */
    protected function _getConfig()
    {
        return Mage::helper('po_pao/config');
    }
}