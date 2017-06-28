<?php
class Iksula_ExtendedReview_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List{

    public function getReviewsCollection(){
    	$collection = parent::getReviewsCollection();
    	return $collection;
    }

}
			