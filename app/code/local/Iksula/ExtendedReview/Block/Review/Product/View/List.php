<?php
class Iksula_ExtendedReview_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List{

    public function getReviewsCollection(){
    	$collection = parent::getReviewsCollection();
    	// $collection->getSelect()->joinLeft(
     //        array('extre' => $collection->getTable('extendedreview/extendedreview')),
     //        'extre.review_id = main_table.review_id');
    	// echo "<pre>";
    	// print_r($collection->getData());
    	// exit();
    	return $collection;
    // $collection = parent::getReviewsCollection();
    //     $collection->getSelect()->joinLeft(
    //         array('extended_review'),
    //         'extended_review.review_id = main_table.review_id')
    //         ->group('main_table.review_id');
    //     return $collection;
    }
    


}
			