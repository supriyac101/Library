<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.54
 * @build     780
 * @copyright Copyright (C) 2017 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Processor_Requestcacheid extends Mage_Core_Helper_Abstract
{
    /**
     * @var bool|array
     */
    protected $_custom;

    /**
     * @var Mirasvit_Fpc_Helper_Request
     */
    protected $_requestHelper;

    public function __construct()
    {
        $this->_custom = Mage::helper('fpc/custom')->getCustomSettings();
        $this->_requestHelper = Mage::helper('fpc/request');
    }

    /**
     * Cache id for current request (md5)
     *
     * @return string
     */
    public function getRequestCacheId()
    {
        return Mirasvit_Fpc_Model_Config::REQUEST_ID_PREFIX . md5($this->_getRequestId());
    }

    /**
     * Build request id for current request
     *
     * @return string
     */
    protected function _getRequestId()
    {
        if ($customerId = $this->getLoggedCustomerId()) {
            $customerGroupId =  $this->_getCustomerGroupId($customerId); //for logged in user
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        // Use the same cache for all groups
        if ($customerGroupId != 0
            && $this->getConfig()->isUseSameCacheForAllGroups()) {
            $customerGroupId = 1;
        }

        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode(); // Mage::getStoreConfig('currency/options/default', Mage::app()->getStore()->getId());

        $url = Mage::helper('fpc')->getNormalizedUrl();

        $dependencies = array(
            $url,
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('layout'),
            Mage::app()->getStore()->getCode(),
            Mage::app()->getLocale()->getLocaleCode(),
            $currentCurrencyCode,
            $customerGroupId,
            intval(Mage::app()->getRequest()->isXmlHttpRequest()),
            Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getSingleton('core/design_package')->getTheme('frontend'),
            Mage::getSingleton('core/design_package')->getPackageName(),
        );

        $action = Mage::helper('fpc')->getFullActionCode();

        switch ($action) {
            case 'catalog/category_view':
            case 'splash/page_view':
                $data = Mage::getSingleton('catalog/session')->getData();
                $paramsMap = array(
                    'display_mode'   => 'mode',
                    'limit_page'     => 'limit',
                    'sort_order'     => 'order',
                    'sort_direction' => 'dir',
                );
                foreach ($paramsMap as $sessionParam => $queryParam) {
                    if (isset($data[$sessionParam])) {
                        $dependencies[] = $queryParam . '_' . $data[$sessionParam];
                    }
                }
                break;
        }

        foreach ($this->getConfig()->getUserAgentSegmentation() as $segment) {
            if ($segment['useragent_regexp']
                && preg_match($segment['useragent_regexp'], Mage::helper('core/http')->getHttpUserAgent())) {
                    $dependencies[] = $segment['cache_group'];
            }
        }

        if ($this->_requestHelper->isCrawler()) {
            $dependencies = array_merge($dependencies, $this->getWarmerParams($url));
        }

        if (Mage::helper('mstcore')->isModuleInstalled('AW_Mobile2')
            && Mage::helper('aw_mobile2')->isCanShowMobileVersion()
        ) {
            $dependencies[] = 'awMobileGroup';
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Mediarocks_RetinaImages')
            && Mage::getStoreConfig('retinaimages/module/enabled')) {
                $retinaValue  = Mage::getModel('core/cookie')->get('device_pixel_ratio');
                $dependencies[] = (!$retinaValue) ? false : $retinaValue;
        }

        if ($deviceType = Mage::helper('fpc/mobile')->getMobileDeviceType()) {
            $dependencies[] = $deviceType;
        }

        if ($this->_custom && in_array('getRequestIdDependencies', $this->_custom)) {
            $dependencies[] = Mage::helper('fpc/customDependence')->getRequestIdDependencies();
        }

        if ($action == 'catalog/product_view'
            && $this->getConfig()->getUpdateStockMethod() == Mirasvit_Fpc_Model_Config::UPDATE_STOCK_METHOD_FRONTEND) {
                $dependencies[] = Mage::helper('fpc/processor_stock')->getProductStock();
        }

        $requestId = strtolower(implode('/', $dependencies));

        if ($this->getConfig()->isDebugLogEnabled()) {
            Mage::log('Request ID (url from cache): ' . $requestId, null, Mirasvit_Fpc_Model_Config::DEBUG_LOG);
        }


        return $requestId;
    }

    /**
     * Add params data in cache id for cralwer
     *
     * @param string $url
     * @return array
     */
    protected function getWarmerParams($url)
    {
        $dependencies = array();
        $paramsMap = array(
            'display_mode'   => 'mode',
            'limit_page'     => 'limit',
            'sort_order'     => 'order',
            'sort_direction' => 'dir',
        );
        if (strpos($url, '?') !== false
            && ($urlParams = preg_replace('/(.*?)\?/', '', $url))) {
                $urlParamsData = explode('&', $urlParams);
                foreach ($urlParamsData as $param) {
                    if (strpos($param, '=') !== false
                        && ($paramData = explode('=', $param))
                        && isset($paramData[0]) && isset($paramData[1])
                        && (array_search($paramData[0], $paramsMap))) {
                            $dependencies[] = $paramData[0] . '_' . $paramData[1];
                    }
                }
        }

        return $dependencies;
    }


    /**
     * @return Mirasvit_Fpc_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('fpc/config');
    }

    /**
     * Get customer group id by customer id
     *
     * @param int $customerId
     * @return bool, int
     */
    protected function _getCustomerGroupId($customerId)
    {
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $bind    = array('entity_id' => (int)$customerId);
        $select  = $adapter->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('customer/entity'), 'group_id')
            ->where('entity_id = :entity_id')
            ->limit(1);

        $result = $adapter->fetchOne($select, $bind);

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get legged in customer id
     *
     * @return bool, int
     */
    public function getLoggedCustomerId()
    {
        $edition = Mage::helper('mstcore/version')->getEdition();

        if ($edition == 'ee'
            && isset($_SESSION['customer']['id'])) {
                $customerId = $_SESSION['customer']['id'];
        } elseif ($edition == 'ee'
            && ($storeCode = Mage::app()->getStore()->getWebsite()->getCode())
            && isset($_SESSION['customer_' . $storeCode]['id'])) {
                $customerId = $_SESSION['customer_' . $storeCode]['id'];
        } else {
                $customerId = Mage::getSingleton('customer/session')->getId();
        }

        return $customerId;
    }
}