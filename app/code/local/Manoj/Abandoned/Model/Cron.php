<?php
class Manoj_Abandoned_Model_Cron{	
	public function Abandonedemail(){
		  $abandonedcart = Mage::helper('abandoned');
		  $abandonedcart->synchCart(); 
  		  $abandonedcart->mailsend2();
	} 
}