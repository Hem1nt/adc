/*
 * Velan Info Services India Pvt Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.velanapps.com/License.txt
 *
  /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * *************************************** */
/* This package designed for Magento COMMUNITY edition
 * Velan Info Services does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Velan Info Services does not provide extension support in case of
 * incorrect edition usage.
  /***************************************
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category   velanapps
 * @package    SD
 * @author     Velan Team 
 * @copyright  Copyright (c) 2013 - 2014 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
 * @license    http://store.velanapps.com/License.txt
 */

/*---------Santa Claus----------*/
var right_to_left_image = '';
var left_to_right_image = '';
function thatha_fly(type,new_speed,fly_image,next_image){
	if(new_speed){ani_speed = new_speed;}
	if(fly_image){right_to_left_image = fly_image;}
	if(next_image){left_to_right_image = next_image;}
	
	if(type == 'random'){
		jQuery('#christmas_thatha_image img').attr('src',fly_image);
		thatha_random_animation();
	} else {
		jQuery('#christmas_thatha_image img').attr('src',next_image);
		animate_vertical_horizontal(type);
	}
}

function animate_vertical_horizontal(flip_style){
	jQuery('#christmas_thatha_image').css('bottom',0).css('right',-jQuery('#christmas_thatha_image').width());
	var pos = jQuery(document).width();
	var thatha_image_width = jQuery('#christmas_thatha_image').width();

	if(flip_style == 'flip'){
	
		var bottom_pos = jQuery(document).height()/4.5;
				
		jQuery('#christmas_thatha_image').animate({ right:pos,bottom:(bottom_pos*1)}, ani_speed, function(e){
			set_flying_image(right_to_left_image);
			jQuery('#christmas_thatha_image').animate({ right:-(thatha_image_width),bottom:(bottom_pos*2)}, ani_speed, function(e){
				set_flying_image(left_to_right_image);
				jQuery('#christmas_thatha_image').animate({ right:pos,bottom:(bottom_pos*3)}, ani_speed, function(e){
					set_flying_image(right_to_left_image);
					jQuery('#christmas_thatha_image').animate({ right:-(thatha_image_width),bottom:(bottom_pos*4)}, ani_speed, function(e){
						set_flying_image(left_to_right_image);
						animate_vertical_horizontal(flip_style);
					});
				});
			});
		});
		
	}else{
	
		var bottom_pos = jQuery(document).height()/3.5;
				
		jQuery('#christmas_thatha_image').animate({ right:pos,bottom:(bottom_pos*1)}, ani_speed, function(e){
			set_flying_image(right_to_left_image);
			jQuery('#christmas_thatha_image').css('bottom',jQuery('#christmas_thatha_image').css('bottom')).css('right',-jQuery('#christmas_thatha_image').width());
			jQuery('#christmas_thatha_image').animate({ right:pos,bottom:(bottom_pos*2)}, ani_speed, function(e){
				set_flying_image(left_to_right_image);
				jQuery('#christmas_thatha_image').css('bottom',jQuery('#christmas_thatha_image').css('bottom')).css('right',-jQuery('#christmas_thatha_image').width());
				jQuery('#christmas_thatha_image').animate({ right:pos,bottom:(bottom_pos*3)}, ani_speed, function(e){
					set_flying_image(left_to_right_image);
					animate_vertical_horizontal(flip_style);
				});
			});
		});
		
	}
}

/* ------------------------Couple Effects------------------------- */
   function set_couple_width_height(new_width,new_height,pos,img){
		jQuery('#couple').css('max-width',new_width).css('max-height',new_height).addClass(pos);
         jQuery('#couple img').attr('src',img);
}

function set_flying_image(get_image){
	jQuery('#christmas_thatha_image img').attr('src',get_image);
}

function thatha_random_animation(){
	var h = jQuery(document).height() - 50;
    var w = jQuery(document).width() - 50;
    var nh = Math.floor(Math.random() * h);
    var nw = Math.floor(Math.random() * w);
    jQuery('#christmas_thatha_image').animate({ top: nh, left: nw },5000, function(){
      thatha_random_animation();        
    });
};


