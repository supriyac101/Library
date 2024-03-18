<?php
class Anowave_Price_Model_Soap_Api extends Mage_Api_Model_Resource_Abstract
{
	public function myapimethod($message)
	{
		return "This is the message: ".$message;
	}
}