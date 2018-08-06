<?php
class Iksula_Clicktoform_IndexController extends Mage_Core_Controller_Front_Action{

	public function IndexAction() {
		$this->loadLayout();   
		$this->renderLayout(); 
		
	}

	public function saveAction() {
		$data = $this->getRequest()->getParams();
		//print_r($data);
		try{
			if(($data['username'] != '')&&($data['email'] != '')&&($data['mobileNumber'] != '')&&($data['timestamp'] != '')&&($data['comment']))
			{
				 if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) && filter_var($data['mobileNumber'], FILTER_VALIDATE_INT)){
							$binds = array(
							    'customer_name'    => $data['username'],
							    'customer_email'   => $data['email'],
							    'customer_mobileno' => $data['mobileNumber'],
							    'customer_time'    => $data['timestamp'],
							    'customer_comment'    => $data['comment'],
							    'customer_calling_status' => '0'
							);
				 		$model = Mage::getModel('clicktoform/clicktoform');
				 		$model->setData($binds);
				 		$model->save();
				 		$this->helper("clicktoform")->sendEmail($model->getId());
				 		echo $result = 'submit';
				 		return true;
						}
			}else{
				echo $result = 'error';
				return false;
			}
		}catch (Exception $e) {
                return false;
        }
	}
	
}