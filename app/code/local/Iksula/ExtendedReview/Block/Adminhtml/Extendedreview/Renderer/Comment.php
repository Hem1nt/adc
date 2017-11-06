<?php

Class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){

      $id=$row->getId();
     	$reviewId =  $row->getReviewId();
      $review = Mage::getModel('review/review')->load($reviewId);
    	$commentId= $row->getComentId();
      $Comment_detail=$review->getDetail();
    	$customer_id=$row->getCustomerId();
    	
  
        $commentCollection=Mage::getModel("extendedreview/extendedreview")->getCollection()
        ->addFieldToSelect('*')
        //->addFieldToFilter('id',array('in' => array($id)))
        ->addFieldToFilter('review_id',$reviewId)
        //->addFieldToFilter('comment_id','0')
       // ->addFieldToFilter('comment_id',array('in' => array($commentId)))
        ->addFieldToFilter('customer_id',$customer_id)
        ;
   


        $count = '1';
        echo "<p><i>".'Actual Review :'.'<br/>'.$Comment_detail."</i></p>";


       foreach ($commentCollection as $value) {
        $comment_id=$value['comment_id'];
        if($comment_id=='0')
        {
           echo "<br/>";
        echo "Comment ".$count.":";
        
        echo '<strong style="margin:0 6px 16px 18px;">'.$value->getComment().'</strong>';
        $count++; 
        }
        else
        {
           echo "<br/>";
        echo "sub comment :";
        echo '<strong style="margin:0 6px 20px 27px; color:#ea7601 !important;">'.$value->getComment().'</strong>';
       // $count++;
        }
       
       
       }
        
    }
}

?>