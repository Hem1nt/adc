<?php
class Iksula_Notifyoutofstock_Model_Cron{
	public function Notifyoutofstockemail(){
		//do something
		// $abandonedcart = Mage::helper('abandoned');
		// $cust_email_id = 'manoj.chowrasiya@iksula.com';
		// $abandonedcart->synchCart();
		// $abandonedcart->mailsend();

		$templateId = 41;

		$recipients = Mage::getStoreConfig('custom_snippet_email/notifyoutofstock_group/notifyoutofstock_input',Mage::app()->getStore());
		$recipients = explode(",",$recipients);

		// Set sender information
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array('name' => $senderName,'email' => $senderEmail);

		// Set recepient information
		// $recepientEmail = 'moiz.k@iksula.com';//$email;
		$recepientPassword = $randomString;

		// Get Store ID
		$store = Mage::app()->getStore()->getId();

		// Set variables that can be used in email template
		$vars = array('customer' => $recepientEmail,
		  'cust' => $recepientPassword);

		$translate = Mage::getSingleton('core/translate');

		/**/
		$csv = '';
		$_columns = array(
		     "Notify Id",
		     "Product Name",
		     "Count",
		     "Date"
		);
		$data = array();
		// prepare CSV header...
		foreach ($_columns as $column) {
		       $data[] = '"'.$column.'"';
		}
		$csv .= implode(',', $data)."\n";

		$first = Mage::getModel('core/date')->date('Y-m-d 00:00:00');
		$last = Mage::getModel('core/date')->date('Y-m-d 23:59:59');

		$notifyoutofstockModel = Mage::getModel("notifyoutofstock/notifyoutofstock");
		$model = Mage::getModel("notifyoutofstock/notifyoutofstock")->getCollection();
		$model->addFieldToFilter('date', array(
			'from' => $first,
			'to' => $last,
			'date' => true,
			));
		$model->getSelect()->order('date','DESC');
		// echo "<pre/>";
		foreach ($model as $productData) {
		    $data = array();
		    // $data[] = $productData->getData('notify_id');
		    $data[] = $productData->getData('product_sku');
		    $data[] = '"'.$productData->getData('product_name').'"';
		    $data[] = $productData->getData('count');
		    $data[] = $productData->getData('date');
		    $csv .= implode(',', $data)."\n";
		   //now $csv varaible has csv data as string
		}

		// Send Transactional Email
		try{

		  // $file = Mage::getBaseDir().'/sitemap.xml';
		  // $fileContents = file_get_contents($file);
			 foreach($recipients as $recepientEmail) {
				  $vars = array('customerName' => 'Admin');
				  $transactionalEmail = Mage::getModel('core/email_template')->setDesignConfig(array('area'=>'frontend', 'store'=>1));
				  $transactionalEmail->getMail()->createAttachment($csv,'text/csv')->filename = 'product_report.csv';//$filename;
				  $transactionalEmail->sendTransactional($templateId, $sender, $recepientEmail, "Admin", $vars, $storeId);
			}
		  // echo 'mail send';
		}
		catch(Exception $e){
		  print_r($e);
		}

	}

	public function Notifyoutofstockdelete() {
      $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
      $currentdate = date('Y-m-d H:i:s', $currentTimestamp);

      $requiredDate = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime($currentdate."-2 days"));
      $model = Mage::getModel("notifyoutofstock/notifyoutofstock")->getCollection();
      try {
        $model->addFieldToFilter('date', array('lt' => $requiredDate ));
        $model->walk('delete');
        echo "Data deleted successfully.";

      } catch (Exception $e){
          echo $e->getMessage();
      }
    }
}


