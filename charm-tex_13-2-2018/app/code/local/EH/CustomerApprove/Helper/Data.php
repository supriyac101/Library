<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\  Customer Approve/Disapprove  \\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   EH                            ///////
 \\\\\\\                      * @package    EH_CustomerApprove            \\\\\\\
 ///////    * @author     Extension Hut <info@extensionhut.com>                ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\* @copyright  Copyright 2015 © www.extensionhut.com All right reserved\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class EH_CustomerApprove_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * States of approval
     */
    const STATE_APPROVED = 1;
    const STATE_UNAPPROVED = 0;

	/**
     * system config options
     */
    const MP_CC_ENABLED = 'customerapprove/general/enabled';
	const MP_CC_AUTO_APPROVE = 'customerapprove/general/auto_approve';

    const MP_CC_REDIRECT_ENABLED 	= 'customerapprove/redirect/enabled';
	const MP_CC_REDIRECT_CMS_PAGE 	= 'customerapprove/redirect/cms_page';
	const MP_CC_REDIRECT_USE_CUSTOM = 'customerapprove/redirect/use_custom_url';
	const MP_CC_REDIRECT_CUSTOM_URL = 'customerapprove/redirect/custom_url';

	const MP_CC_ERROR_MSG_ENABLED	= 'customerapprove/error_msg/enabled';
	const MP_CC_ERROR_MSG_TEXT		= 'customerspprove/error_msg/text';

	/**
	 * Whether or not the extension is enabled
	 *
	 * @var boolean
	 */
	protected $_enabled;
	
	/**
	 * Auto approve new customers, those sign-up to store
	 *
	 * @var boolean
	 */
	protected $_autoApprove;
	
	/**
	 * Whether or not error messages is enabled
	 *
	 * @var boolean
	 */
	protected $_errorMsgEnabled;

	/**
	 * Error message text
	 *
	 * @var string
	 */
	protected $_errorMsgText;

	/**
	 * Whether or not redirect is enabled
	 *
	 * @var boolean
	 */
	protected $_redirectEnabled;

	/**
	 * Store id
	 *
	 * @var int
	 */
	protected $_storeId;

	/**
	 * Redirect URL for unapproved customers attempting to sign in
	 *
	 * @var string
	 */
	protected $_redirectURL;


	/**
	 * Retrieve whether or not the extension is enabled
	 *
	 * @return boolean
	 */
	public function getIsEnabled()
	{
		if(is_null($this->_enabled)) {
			$this->_enabled = intval(Mage::getStoreConfig(self::MP_CC_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_enabled;
	}

	/**
	 * Retrieve the auto approve setting
	 *
	 * @return boolean
	 */
	public function getIsAutoApprove()
	{
		if(is_null($this->_autoApprove)) {
			$this->_autoApprove = intval(Mage::getStoreConfig(self::MP_CC_AUTO_APPROVE, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_autoApprove;
	}	
	
	
	/**
	 * Get current store id
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if(is_null($this->_storeId)) {
			$this->_storeId = intval(Mage::app()->getStore()->getId());
		}

		return $this->_storeId;
	}

	/**
	 * Get whether or not error messages is enabled
	 *
	 * @return boolean
	 */
	public function getErrorMsgEnabled()
	{
		if(is_null($this->_errorMsgEnabled)) {
			$this->_errorMsgEnabled = intval(Mage::getStoreConfig(self::MP_CC_ERROR_MSG_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_errorMsgEnabled;
	}

	/**
	 * Error message to be displayed, if any
	 *
	 * @return string
	 */
	public function getErrorMsgText()
	{
		if(is_null($this->_errorMsgText)) {
			$this->_errorMsgText = Mage::getStoreConfig(self::MP_CC_ERROR_MSG_TEXT, $this->getStoreId());
		}
		return $this->_errorMsgText;
	}

	/**
	 * Get whether or not redirection is enabled
	 *
	 * @return boolean
	 */
	public function getRedirectEnabled()
	{
		if(is_null($this->_redirectEnabled)) {
			$this->_redirectEnabled = intval(Mage::getStoreConfig(self::MP_CC_REDIRECT_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_redirectEnabled;
	}

	/**
	 * Retrieve redirection URL for unapproved customers
	 *
	 * @return boolean
	 */
	public function getRedirectUrl()
	{
		if(is_null($this->_redirectURL)) {
			// get store id
			$storeId = $this->getStoreId();

			if ($this->getRedirectEnabled()) {
				// check if we should use a custom URL or CMS page
				$useCustomUrl = intval(Mage::getStoreConfig(self::MP_CC_REDIRECT_USE_CUSTOM, $storeId))==1 ? true : false;

				if ($useCustomUrl) {
					$this->_redirectURL = Mage::getStoreConfig(self::MP_CC_REDIRECT_CUSTOM_URL, $storeId);
				}
				else {
					// get CMS page identifier
					$pageId = Mage::getStoreConfig(self::MP_CC_REDIRECT_CMS_PAGE, $storeId);

					if (!empty($pageId)) {
						// check if id includes a delimiter
						$delPos = strrpos($pageId, '|');

						// get page id by delimiter position
						if ($delPos) {
							$pageId = substr($pageId, 0, $delPos);
						}

						// retrieve redirect URL
						$this->_redirectURL = Mage::helper('cms/page')->getPageUrl($pageId);
					}
				}
			}
		}

		return $this->_redirectURL;
	}

	/**
	 * Get states of approval
	 *
	 * @return array
	 */
	public function getApprovalStates()
	{
		return array(
			self::STATE_APPROVED => $this->__('Yes'),
			self::STATE_UNAPPROVED => $this->__('No'),
		);
	}

	/**
	 * Retrieve readable version of running Magento installation
	 *
	 * @return float
	 */
	public function getMagentoVersion()
	{
		// get current magento version
    	$version = Mage::getVersion();

    	// get position of first '.'
    	$pos = strpos($version,'.');

    	// remove all '.' after the first one
    	$version1 = substr($version,0,$pos+1);
    	$version2 = str_replace('.','',substr($version,$pos+1));

    	// parse the version number to a float number
    	$version = floatval("{$version1}{$version2}");

    	return $version;
	}

}
