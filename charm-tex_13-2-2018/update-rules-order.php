<?php
require_once 'app/Mage.php';
umask(0);
Mage::app('english');

$set_order = 99;
$set_order_zero = 0;
$restricted_ids = array(144,44,123,356);


$rules = Mage::getResourceModel('salesrule/rule_collection')->load();

$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');

if(count($rules) > 0 )
{
    foreach ($rules as $rule) { 
        if (!in_array($rule->getId(), $restricted_ids)) {
            $query = 'UPDATE `salesrule` SET `sort_order` = '.$set_order.' WHERE `rule_id` = '.$rule->getId().'';
            $writeConnection->query($query);
        }else{
            $query = 'UPDATE `salesrule` SET `sort_order` = '.$set_order_zero.' WHERE `rule_id` = '.$rule->getId().'';
            $writeConnection->query($query);
        }
    }
}

$msg  = "We have set order ".$set_order." for all shopping cart rules";
if(count($restricted_ids) > 0){
    $msg .= " except these ids - ";
    foreach($restricted_ids as $id){
        $msg .= $id.' ';
        
    }
}
echo $msg.'.';

?>