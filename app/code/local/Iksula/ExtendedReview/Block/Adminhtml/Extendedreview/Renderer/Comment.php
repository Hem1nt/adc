<?php

Class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   public function render(Varien_Object $row){

    $id=$row->getId();
    $reviewId =  $row->getReviewId();
    $review = Mage::getModel('review/review')->load($reviewId);
    $commentId= $row->getCommentId();
    $count=1;
    if($commentId != 0){
      $Comment_detail=$review->getDetail();
      $customer_id=$row->getCustomerId();
      $commentCollection=Mage::getModel("extendedreview/extendedreview")
                      ->getCollection()->addFieldToSelect('*')
                      ->addFieldToFilter('review_id',$reviewId)
                      ->addFieldToFilter('customer_id',$customer_id);
                        echo "<p>".'<b>Actual Review : </b>'.$Comment_detail."</p>";
                        echo "-by <span style='color:#ff3300;'>".$review->getNickname()."</span><hr>";                       
        foreach ($commentCollection as $value) {
        $customerFirstName = Mage::getModel('customer/customer')->load($value->getCustomerId())->getFirstname();
        $customerEmail = Mage::getModel('customer/customer')->load($value->getCustomerId())->getEmail();          
            echo "<br/>Comment ".$count++.":".'<b style="margin:0 6px 20px 27px; color:#000000; ">'.$value->getComment().'</b>';
            if ($customerFirstName){
            echo  "-by <b>".$customerFirstName."</b>";            
          }else{
            echo  "-by <b>".$customerEmail."</b>";                        
          }
        }
    }
    else{
      $Comment_detail=$review->getDetail();
      $customer_id=$row->getCustomerId();
      $commentCollection=Mage::getModel("extendedreview/extendedreview")
                      ->getCollection()->addFieldToSelect('*')
                      ->addFieldToFilter('review_id',$reviewId)
                      ->addFieldToFilter('customer_id',$customer_id)
                      ->addFieldToFilter('comment_id',$commentId);
                        echo "<p>".'<b>Actual Review : </b>'.$Comment_detail."</p>";
                        echo "-by <span style='color:#ff3300;'>".$review->getNickname()."</span><hr>";                       
                        //echo $count++;
        foreach ($commentCollection as $value) { 
        $customerFirstName = Mage::getModel('customer/customer')->load($value->getCustomerId())->getFirstname();
        $customerEmail = Mage::getModel('customer/customer')->load($value->getCustomerId())->getEmail();          
          echo "<br/> Comment ".$count.":".'<strong style="margin:0 6px 16px 18px; color:#000000;">'.$value->getComment().'</strong>';
          if ($customerFirstName){
            echo  "-by <b>".$customerFirstName."</b>";            
          }else{
            echo  "-by <b>".$customerEmail."</b>";                        
          }
        }
    }
      $count++;   
   }
}

?>