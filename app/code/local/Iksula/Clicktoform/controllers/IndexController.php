<?php
class Iksula_Clicktoform_IndexController extends Mage_Core_Controller_Front_Action{

	public function IndexAction() {
		$this->loadLayout();   
		$this->renderLayout(); 
		
	}

	public function saveAction() {
		$result = '';
		$data = $this->getRequest()->getParams();
		//print_r($data);
		if(($data['username'] != '')&&($data['email'] != '')&&($data['mobileNumber'] != '')&&($data['timestamp'] != '')&&($data['comment']))
		{
			 if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) && filter_var($data['mobileNumber'], FILTER_VALIDATE_INT)){
						$loadingModel = Mage::getSingleton("core/resource")->getConnection("core_write");

						// Concatenated with . for readability
						$query = "insert into customerclickinfo "
						       . "(customer_name, customer_email, customer_mobileno, customer_time, customer_comment) values "
			       . "(:customer_name, :customer_email, :customer_mobileno, :customer_time, :customer_comment)";

						$binds = array(
						    'customer_name'    => $data['username'],
						    'customer_email'   => $data['email'],
						    'customer_mobileno' => $data['mobileNumber'],
						    'customer_time'    => $data['timestamp'],
						    'customer_comment'    => $data['comment']
						);
						$loadingModel->query($query, $binds);
						$result = 'ok';
					}
		}else{
			$result = 'error';
			return false;
		}
	}
}