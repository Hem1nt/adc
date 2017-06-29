<?php 
class Iksula_ExtendedReview_IndexController extends Mage_Core_Controller_Front_Action{
	
	public function getReplyToReviewFormAction(){
		
		$review_id = Mage::app()->getRequest()->getParam('review');
		$error_url = Mage::app()->getRequest()->getParam('error_url');
		$html = "";

		if(strlen($review_id)){
			$html .= "<a href='javascript:void(0)' class='lnkExtendedReview' data-review-id='".$review_id."'>close</a>";
			$html .= "<form id='frmReview".$review_id."'>";
			$html .= "<input name='txtReviewComment'>";
			$html .= "<input type='hidden' name='hdnReviewId' value=".$review_id.">";
			$html .= "<input type='hidden' name='hdnErrorUrl' value=".$error_url.">";
			$html .= "<button type='button' class='btnExtendedReview' data-request-url='".Mage::getUrl('extendedreview/index/saveReplyToReview')."' data-review-id='".$review_id."'> Reply </button>";
			$html .= "</form>";
		}

		if(strlen($html))
			$result =array('success'=>true,'html'=>$html);
		else
			$result =array('success'=>false);

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($result));
	}


	public function getReplyToReviewCommentFormAction(){
		
		$comment_id = Mage::app()->getRequest()->getParam('commentid');
		$review_id = Mage::app()->getRequest()->getParam('review');
		$error_url = Mage::app()->getRequest()->getParam('error_url');
		$html = "";

		if(strlen($comment_id)){
			$html .= "<a href='javascript:void(0)' class='lnkExtendedReviewComment' data-review-comment-id='".$comment_id."'>close</a>";
			$html .= "<form id='frmReview".$comment_id."'>";
			$html .= "<input name='txtReviewComment'>";
			$html .= "<input type='hidden' name='hdnCommentId' value=".$comment_id.">";
			$html .= "<input type='hidden' name='hdnReviewId' value=".$review_id.">";
			$html .= "<input type='hidden' name='hdnErrorUrl' value=".$error_url.">";
			$html .= "<button type='button' class='btnExtendedReview' data-request-url='".Mage::getUrl('extendedreview/index/saveReplyToReview')."' data-review-id='".$comment_id."'> Reply </button>";
			$html .= "</form>";
		}

		if(strlen($html))
			$result =array('success'=>true,'html'=>$html);
		else
			$result =array('success'=>false);

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($result));
	}	


	public function saveReplyToReviewAction(){
		$review_id = Mage::app()->getRequest()->getParam('hdnReviewId');
		$comment = Mage::app()->getRequest()->getParam('txtReviewComment');
		echo "--!".$comment_id = Mage::app()->getRequest()->getParam('hdnCommentId');
		$error_url = Mage::app()->getRequest()->getParam('hdnErrorUrl');

		$customer_id = null;
		$result = array('success'=>false);
		
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		    $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		}

		if(!strlen($customer_id)){
			$result['err_code'] = 100;
			$result['error_url'] = $error_url;
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(json_encode($result));
			return;
		}

		if(!strlen(trim($review_id)) || !strlen(trim($comment))){
			$result['err_code'] = 101;
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(json_encode($result));
			return;
		}

		// SAVE COMMENT FOR REVIEW
		if(!isset($result['err_code'])){
			if(isset($comment_id)){
				$model = Mage::getModel('extendedreview/extendedreview')
					->setData(array(
						'review_id'=>$review_id,
						'customer_id'=>$customer_id,
						'comment' => $comment,
						'comment_id' => $comment_id,
						'status' => 0 ));

			}else{
				$model = Mage::getModel('extendedreview/extendedreview')
					->setData(array(
						'review_id'=>$review_id,
						'customer_id'=>$customer_id,
						'comment_id' => 0,
						'comment' => $comment,
						'status' => 0 ));
			}
			try{
				$model->save();
				$result['success'] = true;
			}catch(Exception $e){
				$result['err_code'] = 102;
			}
		}

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($result));
	}
}
