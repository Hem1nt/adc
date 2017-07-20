<?php
class Iksula_ExtendedReview_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getChildComments($reviewId){
		
		$Comments = Mage::getModel('extendedreview/extendedreview')->getCollection()
					->addFieldToFilter('review_id',$reviewId)
					->addFieldToFilter('comment_id',0)
					->addFieldToFilter('status',2);
          // ->setPageSize(20)
          //   ->setCurPage(1);
					return $Comments;
    }

    public function getChildCommentslevel2($reviewId,$commentid){
		
		$Comments = Mage::getModel('extendedreview/extendedreview')->getCollection()
					->addFieldToFilter('review_id',$reviewId)
					->addFieldToFilter('comment_id',$commentid)
					->addFieldToFilter('status',2);
           // ->setPageSize(20)
          //   ->setCurPage(1);

					return $Comments;
    }

    public function getCustomerName($custId){
    	return Mage::getModel('customer/customer')->load($custId)->getName();
    }

    public function getAdminUserEmail()
    {
        if (Mage::getSingleton('admin/session')->getUser()->getId()) {
            return Mage::getSingleton('admin/session')->getUser()->getEmail();
        }
    }

    public function sendApprovalEmail($id){  	
    	$commentCollection = Mage::getModel('extendedreview/extendedreview')->load($id);
    	$IdOfCommentedUser = $commentCollection->getCustomerId();

      $cust= Mage::getModel('customer/customer')->load($IdOfCommentedUser);
      $EmailOfCommentedUser = $cust->getEmail();
      $NameOfCommentedUser = $cust->getFirstname()." ".$cust->getLastname();


      if($commentCollection->getCommentId()==0){

        $reviewId = $commentCollection->getReviewId();
        $reviewcollection = Mage::getModel('review/review')->load($reviewId);     
        $cust1= Mage::getModel('customer/customer')->load($reviewcollection->getCustomerId());
        $EmailOfReviewUser = $cust1->getEmail();
        $NameOfReviewUser = $cust1->getFirstname()." ".$cust1->getLastname();
        
        $msgForReview = " Mr.".$NameOfCommentedUser." has Commented on your review.";
        $this->sendTransactionalEmail($EmailOfReviewUser,$NameOfReviewUser,$msgForReview);

      }else{

        $commentId = $commentCollection->getCommentId();
        $commentCollection1 = Mage::getModel('extendedreview/extendedreview')->load($commentId);
        $IdOfCommentedUser1 = $commentCollection1->getCustomerId();
        $cust1= Mage::getModel('customer/customer')->load($IdOfCommentedUser1);
        $EmailOfReviewUser1 = $cust1->getEmail();
        $NameOfReviewUser1 = $cust1->getFirstname()." ".$cust1->getLastname();
        
        $msgForReview = " Mr.".$NameOfCommentedUser." has Commented on your review.";
        $this->sendTransactionalEmail($EmailOfReviewUser1,$NameOfReviewUser1,$msgForReview1);
      }
      

      $msgForComment = "Thanks for your suggession, Your Comment has been approved.";
      
    	$this->sendTransactionalEmail($EmailOfCommentedUser,$NameOfCommentedUser,$msgForComment);
      exit;
    }
    public function sendTransactionalEmail($recepientEmail,$recepientName,$msg)
    {
      // Transactional Email Template's ID
      // echo $templateId = Mage::getModel('core/email_template')->loadBy('Wallet_reminder');
      $emailTemplate  = Mage::getModel('core/email_template')->loadByCode('extended_review_approval_mail');
      $templateId = $emailTemplate->getId();
      // $templateId = 13;
     
      // Set sender information     
      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
      $sender = array('name' => $senderName,
                'email' => $senderEmail); 
      
      // Get Store ID   
      $storeId = Mage::app()->getStore()->getId();
     
      // Set variables that can be used in email template
      $vars = array('customerName' => $recepientName,
      				'msg' => $msg,  
              );

      $translate  = Mage::getSingleton('core/translate');
     
      // Send Transactional Email
      Mage::getModel('core/email_template')
        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
              
      $translate->setTranslateInline(true); 

    }
}
	 