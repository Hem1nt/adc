<?php
class Iksula_Clicktoform_IndexController extends Mage_Core_Controller_Front_Action{

	public function IndexAction() {
		$this->loadLayout();   
		$this->renderLayout(); 
		
	}

	public function saveAction() {
		$data = $this->getRequest()->getParams();
		$this->verifyCaptcha($data['responseCaptcha']);
		if($data['comment'] != ''){
			$comment = $data['comment'];
		}else{
			$comment = '';
		}
		try{
			if(($data['username'] != '')&&($data['email'] != '')&&($data['mobileNumber'] != '')&&($data['timestamp'] != ''))
			{
				 if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) && filter_var($data['mobileNumber'], FILTER_VALIDATE_INT)){
							$binds = array(
							    'customer_name'    => $data['username'],
							    'customer_email'   => $data['email'],
							    'customer_mobileno' => $data['mobileNumber'],
							    'customer_time'    => $data['timestamp'],
							    'customer_comment'    => $comment,
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
	
	public function verifyCaptcha($g_response) 
	{
		 if(isset($g_response) && !empty($g_response)):
            if (!(Mage::helper('recaptcha')->Validate_captcha($g_response))):
                Mage::getSingleton('core/session')->addError('Please click on the reCAPTCHA box.123');
                $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
                Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                Mage::app()->getResponse()->sendResponse();
                echo $result = 'error';
				return false;
            endif;
        else:
            $observer->getEvent()->setData(null);
            Mage::getSingleton('core/session')->addError('Please click on the reCAPTCHA box.456');
            $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            echo $result = 'error';
			return false;
        endif;
	}
}