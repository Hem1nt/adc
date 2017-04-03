<?php
class Manoj_Abandoned_Model_Cron{	
	public function Abandonedemail(){
		//do something
		// $to = "manoj.chowrasiya@iksula.com";
		// $subject = "Test mail";
		// $message = "Hello! This is a simple email message.";
		// $from = "manojiksula@gmail.com";
		// $headers = "From:" . $from;
		// mail($to,$subject,$message,$headers);
		// echo "Mail Sent.";
		$abandonedcart = Mage::helper('abandoned');
		// $cust_email_id = 'manoj.chowrasiya@iksula.com'; 
		$abandonedcart->Synchronize();   
		$abandonedcart->mailsend();

	} 
}