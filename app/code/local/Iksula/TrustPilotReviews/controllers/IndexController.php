<?php
class Iksula_TrustPilotReviews_IndexController extends Mage_Core_Controller_Front_Action{

    public function IndexAction() {
      
	   $this->loadLayout();   
	   $this->getLayout()->getBlock("head")->setTitle($this->__("Trustpilot Reviews"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("trustpilot", array(
                "label" => $this->__("Trustpilot Reviews"),
                "title" => $this->__("Trustpilot Reviews")
		   ));

      $this->renderLayout(); 
	  
    }

    public function ajaxReviewAction(){
      $nextpage_apiurl = $this->getRequest()->getPost('apiurl');
      if($nextpage_apiurl){
        Mage::getSingleton('core/session')->setNextPageApiUrl($nextpage_apiurl);
        $reviewsCollection = Mage::app()->getLayout()->createBlock('trustpilotreviews/reviews')->getReviews();
        foreach ($reviewsCollection['links'] as $links) {
          if($links['rel'] == 'next-page'){
            $url = $links['href'];
          }
        }
        // unset($reviewsCollection);      
        $class = '';
        if(array_key_exists('reviews',$reviewsCollection)){
          $html = '';
          foreach ($reviewsCollection['reviews'] as $reviews){
            $html .=  '<div class="trustpilot_reviews_inner">';

            $html .=  '<div class="review_background">';

            $html .=  '<div class="review_img"><image src="http://images-static.trustpilot.com/api/stars/'.$reviews['stars'].'/130x24.png" ></div>';
            $html .=  '<div class="consumer_name">'.$reviews['consumer']['displayName'].'</div>';
            $timpestamp =  strtotime($reviews['createdAt']);
            $html .=  "<div class='review_date'>".date('M d, Y',$timpestamp)."</div>";

            
            $html .=  '</div>';
            if($reviews['companyReply']['text'] != ''):$class = 'review_wrap_highlight';endif;
            $html .=  '<div class="review_wrap '.$class.'">';
            $html .=  "<div class='review_title'>".$reviews['title'].'</div>';
            $html .=  "<div class='review_txt'>".$reviews['text'].'</div>';
            $html .=  '</div>';
            if($reviews['companyReply']['text'] != ''):$html .=  "<div class='companyReply'><div style='color:red;font-weight: normal;
            color: #506f00;font-weight: 600;'>AllDayChemist Reply </div><br>".$reviews['companyReply']['text'].'</div>';endif;
            $html .=  '</div>';
          }
          echo json_encode(array('success'=>'true','apiurl'=>$url,'data'=>$html));
        }else{
          echo json_encode(array('success'=>'false','message'=>'No More Reviews'));
        }
        
      }
    }
}




// <script type="text/javascript">
// jQuery(document).ready(function(){

//   if( jQuery('.trustpilot_reviews_inner').hasClass('companyReply') ) {
//     alert(123);
//     // $('.review_wrap').addClass('sonal');
//     }
//    });
// </script>
