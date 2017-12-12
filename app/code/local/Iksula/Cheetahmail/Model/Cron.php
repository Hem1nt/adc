<?php
class Iksula_Cheetahmail_Model_Cron{	
	
	public function buildDatabase(){
		// echo "sdfgvfdxg";exit();
		$time = time();
		$to = date('Y-m-d H:i:s', $time);
		$lastTime = $time - 86400; // 60*60*24
		$from = date('Y-m-d H:i:s', $lastTime);
		$customers = Mage::getResourceModel('customer/customer_collection')
				->addAttributeToSelect('*')
				->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
				->load();

		$cheetahIds = explode("#", Mage::getModel('core/variable')->loadByCode('cheetah_newsletter')->getValue('plain'));
		$eid = $cheetahIds[0];
		$aid = $cheetahIds[1];
		$cheetahmail = Mage::helper('cheetahmail/data');

		foreach ($customers as $key => $customersdata) {
			$email= $customersdata->getEmail();
			$firstname= $customersdata->getFirstname();
			$lastname= $customersdata->getLastname();
			// print_r($customer->getData());exit();
			$customerName = $cheetahmail->getUserName($email);
			$cheetahmail->CheetaApi($email, $eid, $aid, $firstname, $lastname);
			// echo $email; 
		}

		//$to = "manoj.chowrasiya@iksula.com";
		$to = "hemant.r@iksula.com";
		$subject = "Sync Email with Cheethamail";
		$txt = "Hello world!";
		$headers = "From: info@alldaychemist.com" . "\r\n" .
		"CC: info@alldaychemist.com";

		mail($to,$subject,$txt,$headers);


	} 

}