<?php 
//require_once "Mage/Review/controllers/ProductController.php"; 
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
                            <img src='".$url_of_website.'extendedreview/index/index?rand='.rand()."' id='review_commment_captcha_img'/>                            
                            
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
    	//echo "happy deewali";exit;

           $this->loadLayout();
           $this->renderLayout();
       
    }	


	public function saveReplyToReviewAction(){
		$review_id = Mage::app()->getRequest()->getParam('hdnReviewId');
		$comment = Mage::app()->getRequest()->getParam('txtReviewComment');
		$comment_id = Mage::app()->getRequest()->getParam('hdnCommentId');
		$comment_id = Mage::app()->getRequest()->getParam('hdnCommentId');
		$comment_id = Mage::app()->getRequest()->getParam('hdnCommentId');
		$error_url = Mage::app()->getRequest()->getParam('hdnErrorUrl');
		
		$getProdId = Mage::getModel('review/review')->load($review_id)->getData('entity_pk_value');
		$getProdName = Mage::getModel('catalog/product')->load($getProdId)->getName();

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

		$loadCustomer = Mage::getModel('customer/customer')->load($customer_id);
		$getReviewerName = $loadCustomer->getData('firstname').' '.$loadCustomer->getData('lastname');
		// SAVE COMMENT FOR REVIEW
		if(!isset($result['err_code'])){
			if(isset($comment_id)){
				$model = Mage::getModel('extendedreview/extendedreview')
					->setData(array(
						'review_id'=>$review_id,
						'customer_id'=>$customer_id,
						'comment' => $comment,
						'comment_id' => $comment_id,
						'status' => 1,'reviewer_name' => $getReviewerName,'product_name' => $getProdName));

			}else{
				$model = Mage::getModel('extendedreview/extendedreview')
					->setData(array(
						'review_id'=>$review_id,
						'customer_id'=>$customer_id,
						'comment_id' => 0,
						'comment' => $comment,
						'status' => 1,'reviewer_name' => $getReviewerName,'product_name' => $getProdName));
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
		//$nurl = $url_of_website."extended_review_captcha_code_file.php?code=".rand();
		$nurl = $url_of_website."extendedreview/index/index?code=".rand();
		$Captcha = "<script type='text/javascript'>
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
	return $Captcha;
	}

	public function indexAction(){
		$check = isset($_GET['check']) ? $_GET['check'] : NULL;
		$code = isset($_GET['code']) ? $_GET['code'] : NULL;
		$lettercode = isset($_SESSION['6_letters_code']) ? $_SESSION['6_letters_code'] : NULL;
		if(isset($check))
		{
			//echo $_SESSION['6_letters_code']."==".$check;
			if($lettercode==$check) {
				echo 1;
			}
			else {
				echo 0;
			}
			exit;
		}

		if(isset($code))
		{
			echo "class_".$_SESSION['6_letters_code']."_capcha";
			exit;
		}
		//Settings: You can customize the captcha here
		$image_width = 100;
		$image_height = 40;
		$characters_on_image = 3;
		$font = './monofont.ttf';

		//The characters that can be used in the CAPTCHA code.
		//avoid confusing characters (l 1 and i for example)
		$possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
		$random_dots = 0;
		$random_lines = 15;
		$captcha_text_color="0x142864";
		$captcha_noice_color = "0x142864";

		$code = '';


		$i = 0;
		while ($i < $characters_on_image) { 
		$code .= substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
		$i++;
		}


		$font_size = $image_height * 0.75;
		$image = @imagecreate($image_width, $image_height);


		/* setting the background, text and noise colours here */
		$background_color = imagecolorallocate($image, 255, 255, 255);

		$arr_text_color = $this->hexrgb($captcha_text_color);
		$text_color = imagecolorallocate($image, $arr_text_color['red'], 
				$arr_text_color['green'], $arr_text_color['blue']);

		$arr_noice_color = $this->hexrgb($captcha_noice_color);
		$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], 
				$arr_noice_color['green'], $arr_noice_color['blue']);


		/* generating the dots randomly in background */
		for( $i=0; $i<$random_dots; $i++ ) {
		imagefilledellipse($image, mt_rand(0,$image_width),
		 mt_rand(0,$image_height), 2, 3, $image_noise_color);
		}


		/* generating lines randomly in background of image */
		for( $i=0; $i<$random_lines; $i++ ) {
		imageline($image, mt_rand(0,$image_width), mt_rand(0,$image_height),
		 mt_rand(0,$image_width), mt_rand(0,$image_height), $image_noise_color);
		}


		/* create a text box and add 6 letters code in it */
		$textbox = imagettfbbox($font_size, 0, $font, $code); 
		$x = ($image_width - $textbox[4])/2;
		$y = ($image_height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code);


		/* Show captcha image in the page html page */
		header('Content-Type: image/jpeg');// defining the image type to be shown in browser widow
		imagejpeg($image);//showing the image
		imagedestroy($image);//destroying the image instance
		$_SESSION['6_letters_code'] = $code;
	}

	public function hexrgb ($hexstr){
		  $int = hexdec($hexstr);

		  return array("red" => 0xFF & ($int >> 0x10),
		               "green" => 0xFF & ($int >> 0x8),
		               "blue" => 0xFF & $int);
	}
}
