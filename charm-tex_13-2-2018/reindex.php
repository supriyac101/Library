<?php
//Following 3 lines will load the magento admin environment.
require_once 'app/Mage.php';
$app = Mage::app('admin');
umask(0);
 
// to reindex the processes we require their ids
// for default magento there are 9 processes to reindex, numbered 1 to 9. 
$ids = array(1,2,3,4,5,6,7,8,9);
foreach($ids as $id)
{
//load each process through its id
try
{
$process = Mage::getModel('index/process')->load($id);
$process->reindexAll();
echo "Indexing for Process ID # ".$id." Done<br />";
}
catch(Exception $e)
{
echo $e->getMessage();
}
}
?>