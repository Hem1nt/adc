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
        $block = Mage::app()->getLayout()->createBlock('trustpilotreviews/reviews')->getReviews();
        foreach ($block['links'] as $links) {
          if($links['rel'] == 'next-page'){
            $url = $links['href'];
          }
        }
        $html = '';
        foreach ($block['reviews'] as $reviews){
          $html .=  $reviews['consumer']['displayName'].'<br>';
          $html .=  '<image src="http://images-static.trustpilot.com/api/stars/'.$reviews['stars'].'/130x24.png" >'; 
          $html .=  $reviews['title'].'<br>';
          $html .=  $reviews['text'].'<br>';
          $html .=  $reviews['companyReply']['text'].'<br>';
          
          }
        echo json_encode(array('success'=>'true','apiurl'=>$url,'data'=>$html));
      }
    }
}