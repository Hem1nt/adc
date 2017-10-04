<?php

Class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
    	
    	$reviewId =  $row->getReviewId();
    	$commentId= $row->getComentId();
    	$customer_id=$row->getCustomerId();
    	//print_r($customer_id);exit;
  
        $commentCollection=Mage::getModel("extendedreview/extendedreview")->getCollection()
        ->addFieldToFilter('review_id',$reviewId)
        ->addFieldToFilter('comment_id',array('in' => array($commentId)))
        ->addFieldToFilter('customer_id',$customer_id)
         					//->addFieldToFilter('id',$id);
        ;
        $count=0;
       foreach ($commentCollection as $value) {
       	$count++;
       	echo $count;
       	echo $value->getComment()."<br>";
       }

        
    }
}

?>