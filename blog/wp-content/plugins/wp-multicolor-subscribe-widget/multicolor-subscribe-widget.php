<?php
/*
====================================================
---------------multicolor-subscribe-Widget----------
====================================================

Plugin Name: Multicolor Subcribe Widget

Plugin URI: http://www.designaeon.com/wp-multicolor-subscribe-widget

Description: WP Multicolor Subscribe Widget : Wordpress Sidebar Subscription Widget.Comes with Multiple color support so that you can colorize it according to your theme

Version: 2.1

Author: Ramandeep Singh

Author URI: http://www.designaeon.com/

License: GPLv2
*/
class MultiColorSubscribeWidget extends WP_Widget{
	
	function MultiColorSubscribeWidget(){
		$widget_ops = array('classname' => 'MultiColorSubscribeWidget', 'description' => __(' Wordpress Sidebar Subcription widget with multicolor support ,Created By Ramandeep Singh | Designaeon.com'));
		$control_ops = array('width' => 300, 'height' => 350);

		$this->WP_Widget('MultiColorSubscribeWidget', __('Multicolor Subscribe widget'), $widget_ops, $control_ops);
		//$instance = $this->get_settings();
		//$this->add_inline_style($instance);
	}
	function addcss(){
		wp_enqueue_script('jquery');
		$siteurl = get_option('siteurl');
		$url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/multicolor-subscribe-widget.css';
		//echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
		wp_enqueue_style('multi-color-sub',$url);		
	}
	
	function widget( $args, $instance )
	{
		extract($args);		
		echo $before_widget;
	    //$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$title=$instance['title'];
	    if (!empty($title)){
	      echo $before_title . $title . $after_title;
	      //echo "<h3 style='font-size:15px;border-bottom:1px solid #b3b3b3;margin-bottom:15px;'>". $title."</h3>";
	      }
	 $wid_detail=$instance['wid_detail'];
	 $feedbrr_id = $instance['feedbrr_id'];	 
	 $color_scheme=$instance['color_scheme'];
	 $fore_color=$instance['fore_color'];
	 $hover_color_scheme=$instance['hover_color_scheme'];
	 $hover_fore_color = $instance['hover_fore_color'];
	 $button_text=$instance['button_text'];
	// $textbox_width=$instance['textbox_width'];
	 $subbox_text=$instance['subbox_text'];
	  $extra_styles=$instance['extra_styles'];
	 $show_foot_link=$instance['show_foot_link'];
	 
	
	 //$override_styles= "#mcolor-button:hover{background:".$hover_color_scheme."!important;color:".$hover_fore_color."!important;}".$extra_styles."";
	//wp_add_inline_style('multi-color-sub',$override_styles);
	 
	
	    // WIDGET CODE GOES HERE
	    ?>
		<!--worpdress multicolor subscribe box www.designaeon.com -->
		<p>
		<em><?php if(!empty($wid_detail)){ echo $wid_detail;}  ?></em>
		</p>
		<?php $uid= uniqid(); ?>
		<div id="multicolor-subscribe-<?php echo $uid; ?>" class="multicolor-subscribe" style="background: <?php echo $ecolor_id; ?>;">
			<div class="max-email-box">
				<form id="multicolor-subscribe-form-<?php echo $uid; ?>" class="multicolor-subscribe-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $feedbrr_id; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">	
					<input id="mcolor-subbox-<?php echo $uid; ?>" class="email commons mcolor-subbox" type="email" style="border:1px solid #ddd" id="email" name="email" value="<?php echo $subbox_text; ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"/>		
					<input type="hidden" value="<?php echo $feedbrr_id; ?>" name="uri"/>
					<input type="hidden" name="loc" value="en_US"/>
					<input id="mcolor-button-<?php echo $uid; ?>" class="mcolor-button" style="background:<?php echo $color_scheme; ?>;border:1px solid<?php echo $color_scheme; ?>;color:<?php echo $fore_color; ?>;" class="subscribe commons" name="commit" type="submit" value="<?php echo $button_text; ?>"/>	
					<script type="text/javascript">
						(function($) {
						   $('#mcolor-button-<?php echo $uid; ?>').hover(function(){								
								$(this).css({backgroundColor:"<?php echo $hover_color_scheme; ?>",borderColor:"<?php echo $hover_color_scheme; ?>",color:"<?php echo $hover_fore_color; ?>"});							
						   },function(){
								$(this).css({backgroundColor:"<?php echo $color_scheme; ?>",borderColor:"<?php echo $color_scheme; ?>",color:"<?php echo $fore_color; ?>"});								
						   });
						})(jQuery);
					</script>
				</form>
			</div>	
		<?php if($show_foot_link): ?>	
		<a style="float:right;font-size:10px;margin-top:10px;font-style:italic" href="http://www.designaeon.com/wp-multicolor-subscribe-widget">Get This Widget >></a>		
		<?php endif; ?>
		</div>
		<!--worpdress multicolor subscribe box www.designaeon.com -->
		<?php
	    echo $after_widget;
	}
	function update($new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['wid_detail']=$new_instance['wid_detail'];
		$instance['feedbrr_id']=$new_instance['feedbrr_id'];
		$instance['color_scheme']=$new_instance['color_scheme'];
		$instance['fore_color']=$new_instance['fore_color'];
		$instance['hover_color_scheme']=$new_instance['hover_color_scheme'];
		$instance['hover_fore_color'] = $new_instance['hover_fore_color'];
		$instance['button_text']=$new_instance['button_text'];
		//$instance['extra_styles']=$new_instance['extra_styles'];
		$instance['subbox_text']=$new_instance['subbox_text'];
		$instance['show_foot_link'] =  (isset($new_instance['show_foot_link']) ?  1 : 0 );
		return $instance;
	}
	function form($instance)
	{
		 $instance = wp_parse_args( (array) $instance, array( 'title' => '','wid_detail'=>'' , 'feedbrr_id' => 'designaeon','color_scheme'=>'#26aaff','fore_color'=>'#ffffff','hover_color_scheme'=>'#000','hover_fore_color'=>'#fff','button_text'=>'GO','textbox_width'=>'70%','subbox_text'=>'Enter Your Email ID','show_foot_link'=>1));
		$title = $instance['title'];
		$wid_detail=$instance['wid_detail'];
		$feedbrr_id = $instance['feedbrr_id'];
		$color_scheme=$instance['color_scheme'];
		$fore_color=$instance['fore_color'];
		$hover_color_scheme=$instance['hover_color_scheme'];
		$hover_fore_color=$instance['hover_fore_color'];
		$button_text=$instance['button_text'];
		//$extra_styles = $instance['extra_styles'];
		
		$subbox_text=$instance['subbox_text'];
		$show_foot_link=$instance['show_foot_link'];
		?>
	<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function()
				{					
					jQuery('#widgets-right .multi-color-pick').wpColorPicker(); 
				});
			//]]>   
	  </script>		
	<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
	
	<p><label for="<?php echo $this->get_field_id('wid_detail'); ?>">A little Description(will show just above the subscription box): </label><textarea class="widefat" id="<?php echo $this->get_field_id('wid_detail'); ?>" name="<?php echo $this->get_field_name('wid_detail'); ?>" ><?php echo $wid_detail; ?></textarea></p>	
	
	<p>
	<label for="<?php echo $this->get_field_id('feedbrr_id'); ?>"><?php _e('Enter your Feedburner ID:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('feedbrr_id'); ?>" name="<?php echo $this->get_field_name('feedbrr_id'); ?>" type="text" value="<?php echo $feedbrr_id; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('subbox_text'); ?>"><?php _e('Enter Subscribe TextBox Text: Eg. Enter Your Email'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('subbox_text'); ?>" name="<?php echo $this->get_field_name('subbox_text'); ?>" type="text" value="<?php echo $subbox_text; ?>" />
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id('button_text'); ?>"><?php _e('Enter Button Text:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo $button_text; ?>" />
	</p>
	<div style="border-top:1px solid #575757;">
	<h4 style="text-decoration:underline;text-align:center;">Color Settings</h4>
		<p>
		<label for="<?php echo $this->get_field_id('color_scheme'); ?>"><?php _e('Choose Color Scheme:'); ?></label><br>
		<input class="widefat multi-color-pick" data-default-color="#33bffd" id="<?php echo $this->get_field_id('color_scheme'); ?>" name="<?php echo $this->get_field_name('color_scheme'); ?>" type="text" value="<?php echo $color_scheme; ?>" />
		
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('fore_color'); ?>"><?php _e('Choose ForeGround Color:'); ?></label><br>
		<input class="widefat multi-color-pick" id="<?php echo $this->get_field_id('fore_color'); ?>" name="<?php echo $this->get_field_name('fore_color'); ?>" type="text" value="<?php echo $fore_color; ?>" />
		
		</p>
		<strong>Hover State</strong>
		<p>
		<label for="<?php echo $this->get_field_id('hover_color_scheme'); ?>"><?php _e('On Hover Color:'); ?></label><br>
		<input class="widefat multi-color-pick" data-default-color="#000" id="<?php echo $this->get_field_id('hover_color_scheme'); ?>" name="<?php echo $this->get_field_name('hover_color_scheme'); ?>" type="text" value="<?php echo $hover_color_scheme; ?>" />
		
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('hover_fore_color'); ?>"><?php _e('Hovered ForeGround Color:'); ?></label><br>
		<input class="widefat multi-color-pick" data-default-color="#fff" id="<?php echo $this->get_field_id('hover_fore_color'); ?>" name="<?php echo $this->get_field_name('hover_fore_color'); ?>" type="text" value="<?php echo $hover_fore_color; ?>" />
		
		</p>
	</div>
	<hr/>
	<!--<p>
		<label for="<?php //echo $this->get_field_id('extra_styles'); ?>"><?php //_e('Extra Styles:'); ?></label><br>		
		<textarea class="widefat" id="<?php //echo $this->get_field_id('extra_styles'); ?>" name="<?php //echo $this->get_field_name('extra_styles'); ?>"><?php //echo $extra_styles; ?></textarea>
		<br/><small>Use These IDs : textbox - <span style="color:red;">#mcolor-subbox</span> , Button - <span style="color:red;">#mcolor-button</span> .</small>
	</p>
	<hr/>-->
	<div style="border:1px solid #b3b3b3;padding:5px;margin-left:10px;">
		<center><strong><u>Show/Hide Footer Link</u></strong></center><br />
		
			<span><input type="checkbox" <?php checked( $instance['show_foot_link'], 1 ); ?> id="<?php echo $this->get_field_id('show_foot_link'); ?>" name="<?php echo $this->get_field_name('show_foot_link'); ?>" value="<?php echo $show_foot_link; ?>" /> <label for="<?php echo $this->get_field_id('show_foot_link'); ?>"><?php _e('Show Get This Widget Link'); ?></label></span>&nbsp;&nbsp;			
	</div>
	<hr/>
	
	
	<div>
		<h4>If You Liked this Widget Please Contribute on Facebook ,Google + . widget powered By : <a href="http://www.designaeon.com">DesignAeon</a></h4>
		<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fdesignaeon&amp;send=false&amp;layout=standard&amp;width=280&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=80&amp;appId=175431785895681" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:280px; height:80px;" allowTransparency="true"></iframe>

		<iframe src="//www.facebook.com/plugins/subscribe.php?href=https%3A%2F%2Fwww.facebook.com%2Framandeep000&amp;layout=button_count&amp;show_faces=false&amp;colorscheme=light&amp;font&amp;width=120&amp;appId=102008056593077" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px;  height:21px;" allowTransparency="true"></iframe>

		<a href="https://plus.google.com/103049352972527333852?prsrc=3" rel="author" style="display:inline-block;text-decoration:none;color:#333;text-align:center;font:13px/16px arial,sans-serif;white-space:nowrap;"><span style="display:inline-block;font-weight:bold;vertical-align:top;margin-right:5px;margin-top:0px;">Follow</span><span style="display:inline-block;vertical-align:top;margin-right:13px;margin-top:0px;">on</span><img src="https://ssl.gstatic.com/images/icons/gplus-16.png" alt="" style="border:0;width:16px;height:16px;"/></a>

	</div>
	<hr/>
	<?php
	}
	
}
function sample_load_color_picker_script() {
	wp_enqueue_script('wp-color-picker');
}
function sample_load_color_picker_style() {
	wp_enqueue_style('wp-color-picker');	
}
add_action('admin_print_scripts-widgets.php', 'sample_load_color_picker_script');
add_action('admin_print_styles-widgets.php', 'sample_load_color_picker_style');
add_action('widgets_init', create_function('', 'return register_widget(\'MultiColorSubscribeWidget\');'));
add_action('wp_enqueue_scripts', array('MulticolorSubscribeWidget','addcss'));