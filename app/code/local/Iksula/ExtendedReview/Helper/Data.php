<?php
class Iksula_ExtendedReview_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getChildComments($reviewId){
		
		$Comments = Mage::getModel('extendedreview/extendedreview')->getCollection()
					->addFieldToFilter('review_id',$reviewId)
					->addFieldToFilter('comment_id',0);
					return $Comments;
    }
    public function getChildCommentslevel2($reviewId,$commentid){
		
		$Comments = Mage::getModel('extendedreview/extendedreview')->getCollection()
					->addFieldToFilter('review_id',$reviewId)
					->addFieldToFilter('comment_id',$commentid);

					return $Comments;
    }
    public function getCustomerName($custId){
    	return Mage::getModel('customer/customer')->load($custId)->getName();
    }
}
	 