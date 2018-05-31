<?php
class Magecomp_Recaptcha_Model_Source_Pagessource{
	public function toOptionArray(){
		return array(
			array('value'=>'0','label'=>'Contact Form'),
			array('value'=>'1','label'=>'Review Form'),
			array('value'=>'2','label'=>'Register Form'),
			array('value'=>'3','label'=>'Onepage Checkout'),
			array('value'=>'4','label'=>'Suggestion Box'),
			array('value'=>'5','label'=>'Product Request Form'),
			array('value'=>'6','label'=>'Share Wishlist'),
			array('value'=>'7','label'=>'Affliate MyInvitation')
		);	
	}
}
?>