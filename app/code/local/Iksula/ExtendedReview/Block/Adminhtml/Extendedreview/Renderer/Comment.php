<?php

Class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){

      $id=$row->getId();
       //print_r($Id);exit;
     	$reviewId =  $row->getReviewId();
      $review = Mage::getModel('review/review')->load($reviewId);
    	$commentId= $row->getComentId();
      $Comment_detail=$review->getDetail();
    	$customer_id=$row->getCustomerId();
    	
  
        $commentCollection=Mage::getModel("extendedreview/extendedreview")->getCollection()
        ->addFieldToSelect('*')
        ->addFieldToFilter('id',array('in' => array($id)))
        //->addFieldToFilter('comment_id',$commentId)
       // ->addFieldToFilter('comment_id',array('in' => array($commentId)))
        ->addFieldToFilter('customer_id',$customer_id)
        ;
        //$count = 1;
        echo "<p><i>".'Actual Review :'.'<br/>'.$Comment_detail."</i></p>";

       foreach ($commentCollection as $value) {

        echo "<br/>";
        echo "Comment ".$count.":";
        echo '<strong style="margin:0 6px 16px 18px;">'.$value->getComment().'</strong>';
        $count++; 
       
       }

        
    }
}

?>