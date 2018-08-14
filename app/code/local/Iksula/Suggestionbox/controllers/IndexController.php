<?php
class Iksula_Suggestionbox_IndexController extends Mage_Core_Controller_Front_Action{

	public function IndexAction() {      
		$this->loadLayout();   
		$this->renderLayout(); 	  
	}

	public function newsuggestionAction(){
		$wholedata = $this->getRequest()->getParams();
		if($wholedata["suggestion"] != ""){
			$model= Mage::getModel("suggestionbox/suggestionbox")
			->setEmail($wholedata["email"])
			->setSboxMessage($wholedata["suggestion"])
			->setSboxName($wholedata["name"])
			->setCreatedDate(NOW())
			->save();

			/*$storeId = Mage::app()->getStore()->getId();
			$templateId = Mage::getStoreConfig('suggestion/general/suggestion_template');
		
			$sendername = Mage::getStoreConfig('trans_email/ident_general/name');
            $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
            $sender = array('name' => $sendername,'email' => $senderemail);
                       
            $email = $wholedata["email"];
            $emailName = $wholedata["name"];

            $info = 'info@alldaychemist.com';
            $vars = array('username' => $wholedata["name"],'useremail' => $wholedata["email"],'suggestion'=>$wholedata["suggestion"]);
            $storeId = Mage::app()->getStore()->getId();
            $translate = Mage::getSingleton('core/translate');
            Mage::getModel('core/email_template')
                       ->sendTransactional($templateId, $sender, $info, $emailName, $vars, $storeId);
                       $translate->setTranslateInline(true);*/
		}
	}

}