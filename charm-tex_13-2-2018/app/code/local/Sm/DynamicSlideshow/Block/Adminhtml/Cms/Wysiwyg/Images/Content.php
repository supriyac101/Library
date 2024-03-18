<?php
/**
 * @package SM Dynamic Slideshow 
 * @version 2.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright Copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.magentech.com
 */

class Sm_DynamicSlideshow_Block_Adminhtml_Cms_Wysiwyg_Images_Content extends Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content
{
    public function getFilebrowserSetupObject()
    {
        $setupObject = new Varien_Object();
        $setupObject->setData( array(
             'newFolderPrompt' => $this->helper( 'cms' )->__( 'New Folder Name:' ),
            'deleteFolderConfirmationMessage' => $this->helper( 'cms' )->__( 'Are you sure you want to delete current folder?' ),
            'deleteFileConfirmationMessage' => $this->helper( 'cms' )->__( 'Are you sure you want to delete the selected file?' ),
            'targetElementId' => $this->getTargetElementId(),
            'contentsUrl' => $this->getContentsUrl(),
            'onInsertUrl' => $this->getOnInsertUrl(),
            'newFolderUrl' => $this->getNewfolderUrl(),
            'deleteFolderUrl' => $this->getDeletefolderUrl(),
            'deleteFilesUrl' => $this->getDeleteFilesUrl(),
            'headerText' => $this->getHeaderText(),
            'onInsertCallback' => $this->getOnInsertCallback(),
            'onInsertCallbackParams' => $this->getOnInsertCallbackParams(),
            'popupId' => $this->getPopupId() 
        ) );
        return Mage::helper( 'core' )->jsonEncode( $setupObject );
    }
    
    public function getOnInsertCallback()
    {
        return $this->getRequest()->getParam( 'onInsertCallback' );
    }
    
    public function getOnInsertCallbackParams()
    {
        return $this->getRequest()->getParam( 'onInsertCallbackParams' );
    }
    
    public function getPopupId()
    {
        return $this->getRequest()->getParam( 'popupId' );
    }
}