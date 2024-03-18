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
 ///////    * @author     Suneet Kumar <suneet64@gmail.com>                ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\* @copyright  Copyright 2013 © www.extensionhut.com All right reserved\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

// we need to do this for the extension to show up in the `core_resources`, and actually being counted as installed, on every version of Magento.

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

// get customer entity table
$customerTable = $installer->getTable('customer_entity');

$connection = $this->getConnection();

// add "approved" culoumn to customer table
$connection->addColumn($customerTable, 'mp_cc_is_approved', "TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'");

// update all existing customer records to be "approved"
$installer->run("
	UPDATE `{$this->getTable('customer_entity')}` SET `mp_cc_is_approved` = '1';
");

$connection->insert($installer->getTable('cms/page'), array(
    'title'             => 'Account Awaiting Approval',
    'root_template'     => 'one_column',
    'identifier'        => 'account-awaiting-approval',
    'content'           => "<div class=\"page-title\">\r\n        <h1><a name=\"top\"></a>Your account is awaiting approval</h1>\r\n    </div>\r\n    <p>Your account has been created but needs to be approved by an administrator before you can sign in.</p>\r\n<p>An e-mail will be sent to the e-mail address you used when you registered this account once it has been approved.</p>\r\n<p>Thank you for your patience.</p>",
    'creation_time'     => now(),
    'update_time'       => now(),
));

$connection->insert($installer->getTable('cms/page_store'), array(
    'page_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));

$installer->endSetup();
