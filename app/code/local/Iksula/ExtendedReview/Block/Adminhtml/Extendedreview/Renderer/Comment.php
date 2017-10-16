<?php

Class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
    
     	$reviewId =  $row->getReviewId();
      $review = Mage::getModel('review/review')->load($reviewId);
    	$commentId= $row->getComentId();
      $Comment_detail=$review->getDetail();
    	$customer_id=$row->getCustomerId();
    	
  
        $commentCollection=Mage::getModel("extendedreview/extendedreview")->getCollection()
        ->addFieldToSelect('*')
        ->addFieldToFilter('review_id',array('in' => array($reviewId)))
        ->addFieldToFilter('customer_id',$customer_id)
        ;
        $count = 1;
        echo "<p>".'Actual Review :'.'<br/>'.$Comment_detail."</p>";
       foreach ($commentCollection as $value) {
          //$count++;
        echo "<br/>";
        echo "Comment ".$count.":";
        echo '<strong style="margin:0 6px 16px 44px;">'.$value->getComment().'</strong>';
        $count++; 
       }

        
    }
}

?>