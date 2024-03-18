<?php
	require_once('app/Mage.php');
	Mage::app()->getCacheInstance()->flush();
?>