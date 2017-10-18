<?php 
class Iksula_ExtendedReview_IndexController extends Mage_Core_Controller_Front_Action{
	
	public function getReplyToReviewFormAction(){
		
		$review_id = Mage::app()->getRequest()->getParam('review');
		$error_url = Mage::app()->getRequest()->getParam('error_url');
		if (Mage::app()->getStore()->isCurrentlySecure()) {
        $url_of_website = Mage::getUrl('',array('_secure'=>true));
	    }else{
	        $url_of_website = Mage::getUrl();
	    }
		$html = "";

		if(strlen($review_id)){
			// $html .= "<a href='javascript:void(0)' class='lnkExtendedReview' data-review-id='".$review_id."'>close</a>";
			// $html .= "<form id='frmReview".$review_id."'>";
			$html .= "<form id='frmReview' class='add_review_form1'>";
			$html .= "<div class='field'>
                        <!--<label for='review_comment_captcha_code' class='required'>".$this->__('Captcha')."<em>*</em></label>-->
                        <textarea name='txtReviewComment' class='required-entry' placeholder='Your message'></textarea>                      
                        <div class='input-box sinput captcha_input_box'>
                            <img src='".$url_of_website.'extended_review_captcha_code_file.php?rand='.rand()."' id='review_commment_captcha_img'/>                            
                            
                            <div id='cap_box' class='cap_box_div'>
                            <input id='review_comment_captcha_code' class='input-text required-entry validate_captcha sb_input_field' name='review_comment_captcha_code' type='text'>
                            </div>
                            <p class='captcha_small'>Click <a href='javascript: register_refresh_Captcha();'>here</a> to refresh Image !</p>
                            <input type='hidden' id='check-me' />
                        </div>                        
                    </div>";
			//$html .= "<textarea name='txtReviewComment' class='required-entry' placeholder='Your message'></textarea>";
			$html .= "<input type='hidden' name='hdnReviewId' value=".$review_id.">";
			$html .= "<input type='hidden' name='hdnErrorUrl' value=".$error_url.">";
			$html .= "<button type='button' class='btnExtendedReview' data-request-url='".Mage::getUrl('extendedreview/index/saveReplyToReview')."' data-review-id='".$review_id."'> <span>submit comment </span></button>";
			$html .= "<a href='javascript:void(0)' class='lnkExtendedReview close_review' data-review-id='".$review_id."'>close</a>";
			$html .= "</form>";
			$html .= "<script type='text/javascript'>
				    //<![CDATA[
				        var dataForm = new VarienForm('frmReview', true);        
				    //]]>
				    </script>";
				    $html .=$this->addCustomJs();
		}

		if(strlen($html))
			$result =array('success'=>true,'html'=>$html);
		else
			$result =array('success'=>false);

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($result));
	}


	public function getReplyToReviewCommentFormAction(){
		
		$comment_id = Mage::app()->getRequest()->getParam('commentid');
		$review_id = Mage::app()->getRequest()->getParam('review');
		$error_url = Mage::app()->getRequest()->getParam('error_url');

		if (Mage::app()->getStore()->isCurrentlySecure()) {
        $url_of_website = Mage::getUrl('',array('_secure'=>true));
	    }else{
	        $url_of_website = Mage::getUrl();
	    }

		$html = "";

		// echo $this->addCustomJs();
		// exit;

		if(strlen($comment_id)){
			
			// $html .= "<form id='frmReview".$comment_id."'>";
			$html .= "<form id='frmReview' class='add_review_form1'>";
			$html .= "<div class='field'>
                        <!--<label for='review_comment_captcha_code' class='required'>".$this->__('Captcha')."<em>*</em></label>-->                        
                        <textarea name='txtReviewComment' class='required-entry'></textarea>
                        <div class='input-box sinput captcha_input_box'>
                            <img src='".$url_of_website.'extended_review_captcha_code_file.php?rand='.rand()."' id='review_commment_captcha_img'/>                            
                        	<div id='cap_box' class='cap_box_div'>
                            <input id='review_comment_captcha_code' class='input-text required-entry validate_captcha sb_input_field' name='review_comment_captcha_code' type='text'>
                            </div>
                            <p class='captcha_small'>Click <a href='javascript: register_refresh_Captcha();'>here</a> to refresh Image !</p>
                            <input type='hidden' id='check-me' />";
                                            
			//$html .= "<textarea name='txtReviewComment' class='required-entry'></textarea>";
			$html .= "<input type='hidden' name='hdnCommentId' value=".$comment_id.">";
			$html .= "<input type='hidden' name='hdnReviewId' value=".$review_id.">";
			$html .= "<input type='hidden' name='hdnErrorUrl' value=".$error_url.">";
			$html .= "<button type='button' class='btnExtendedReview' data-request-url='".Mage::getUrl('extendedreview/index/saveReplyToReview')."' data-review-id='".$comment_id."'> <span>submit comment </span></button>";
			$html .= "<a href='javascript:void(0)' class='lnkExtendedReviewComment close_review' data-review-comment-id='".$comment_id."'>close</a>
                    </div></div>";
			$html .= "</form>";
			$html .= "<script type='text/javascript'>
				    //<![CDATA[
				        var dataForm = new VarienForm('frmReview', true);        
				    //]]>
				    </script>";
			$html .=$this->addCustomJs();
		}

		if(strlen($html))
			$result =array('success'=>true,'html'=>$html);
		else
			$result =array('success'=>false);

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($result));
	}
	public function reviewpageAction()
    {
       
           $this->loadLayout();
           $this->renderLayout();
       
    }	


	public function saveReplyToReviewAction(){
		$review_id = Mage::app()->getRequest()->getParam('hdnReviewId');
		$comment = Mage::app()->getRequest()->getParam('txtReviewComment');
		$comment_id = Mage::app()->getRequest()->getParam('hdnCommentId');
		$error_url = Mage::app()->getRequest()->getParam('hdnErrorUrl');

		$customer_id = null;
		$result = array('success'=>false);
		
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		    $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		}

		if(!strlen($customer_id)){
			$result['err_code'] = 100;
			$result['error_url'] = $error_url;
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(json_encode($result));
			return;
		}

		if(!strlen(trim($review_id)) || !strlen(trim($comment))){
			$result['err_code'] = 101;
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(json_encode($result));
			return;
		}

		// SAVE COMMENT FOR REVIEW
		if(!isset($result['err_code'])){
			if(isset($comment_id)){
				$model = Mage::getModel('extendedreview/extendedreview')
					->setData(array(
						'review_id'=>$review_id,
						'customer_id'=>$customer_id,
						'comment' => $comment,
						'comment_id' => $comment_id,
						'status' => 1 ));

			}else{
				$model = Mage::getModel('extendedreview/extendedreview')
					->setData(array(
						'review_id'=>$review_id,
						'customer_id'=>$customer_id,
						'comment_id' => 0,
						'comment' => $comment,
						'status' => 1 ));
			}
			try{
				$model->save();
				$result['success'] = true;
			}catch(Exception $e){
				$result['err_code'] = 102;
			}
		}

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(json_encode($result));
	}
	public function addCustomJs(){
		if (Mage::app()->getStore()->isCurrentlySecure()) {
        $url_of_website = Mage::getUrl('',array('_secure'=>true));
	    }else{
	        $url_of_website = Mage::getUrl();
	    }
		$url = Mage::getUrl("frontend/index/captcha");
		$nurl = $url_of_website."extended_review_captcha_code_file.php?code=".rand();
		$aaa = "<script type='text/javascript'>
    var new_form = new VarienForm('frmReview');
    jQuery('#frmReview').submit(function(){
        var review_commment_captcha_img =jQuery('#review_comment_captcha_code').val();
        if(new_form.validator.validate()){
            jQuery.ajax({
                type : 'POST',
                success:function(content){
                    jQuery('#review_comment_captcha_code').val('');
                    register_refresh_Captcha();
                }
            }); 
        }
            
    }); 
</script>
<!-- Captcha Validation referesh -->
<script language='JavaScript' type='text/javascript'>
    function register_refresh_Captcha(){
        var img = document.images['review_commment_captcha_img'];
        img.src = img.src.substring(0,img.src.lastIndexOf('?'))+'?rand='+Math.random()*1000;
        setTimeout('RegisterRefreshCaptcha()', 500);
    }
</script>
<!-- Captcha Validation to match the input with image -->
<script type='text/javascript'>
	jQuery(document).ready(function(){
		register_refresh_Captcha();
	});
    Validation.add('validate_captcha','Enter valid code',function(field_val){
       if(jQuery('#check-me').val() != '')   {
            var me = jQuery('#check-me').val().split('_');
            if(me[1] == field_val) {
                return true;
            }
            return false;
        }
        return false;
    });

    jQuery(window).load(function(){
        RegisterRefreshCaptcha();
    });
    function RegisterRefreshCaptcha(){
       jQuery.post('".$nurl."', function(captcha){
            if(captcha!=''){
                jQuery('#check-me').val(captcha);
                var split = captcha.split('_');
                var capValue =(split[1]);
                checkCaptcha(capValue);              
            }
        });
    }
    /*Captcha validation*/
    function checkCaptcha(capValue){
        jQuery.ajax({
                    type : 'POST',
                    url : '".$url."',
                    data : 'captcha='+capValue,
                    success:function(result){
                    return result;
                    }
                });
    }
</script>";
	return $aaa;
	}
}
