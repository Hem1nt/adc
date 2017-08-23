<?php
/*
Plugin Name: DD Last Viewed
Version: 3.0.2
Plugin URI: http://dijkstradesign.com
Description: Shows the users recently viewed/visited Posts, Pages, Custom Types and even Terms in a widget.
Author: Wouter Dijkstra
Author URI: http://dijkstradesign.com
*/


/*  Copyright 2016  WOUTER DIJKSTRA  (email : info@dijkstradesign.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class lastViewed extends WP_Widget {
	
	function __construct() {

		$widget_ops = array(
			'classname'   => 'dd_last_viewed',
			'description' => __( "A list of the recently viewed posts, pages or custom posttypes." )
		);
		parent::__construct( 'dd_last_viewed', _x( 'DD Last Viewed', 'DD Last Viewed widget' ), $widget_ops );

		add_action( 'customize_preview_init', array( $this, 'my_preview_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'dd_lastviewed_add_front' ) );
		add_action( 'admin_init', array( $this, 'dd_lastviewed_admin' ) );
		add_action( 'get_header', array( $this, 'add_lastviewed_id' ) );
		add_shortcode( 'dd_lastviewed', array( $this, 'shortcode_lastviewed' ) );
	}

	function my_preview_js() {
		wp_enqueue_script( 'dd_js_admin-lastviewed', plugins_url( '/js/admin-lastviewed.js', __FILE__ ), array( 'jquery' ), '' );
	}

	function dd_lastviewed_add_front() {
		wp_register_style( 'dd_lastviewed_css', plugins_url( '/css/style.css', __FILE__ ) );
		wp_enqueue_style( 'dd_lastviewed_css' );
	}

	function dd_lastviewed_admin() {
		wp_register_style( 'select-2', plugins_url( '/css/select2.min.css', __FILE__ ) );
		wp_enqueue_style( 'select-2' );
		wp_register_style( 'dd_lastviewed_admin_styles', plugins_url( '/css/admin-style.css', __FILE__ ) );
		wp_enqueue_style( 'dd_lastviewed_admin_styles' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'select-2', plugins_url( '/js/select2.min.js', __FILE__ ), array( 'jquery' ), '' );
		wp_enqueue_script( 'dd_js_admin-lastviewed', plugins_url( '/js/admin-lastviewed.js', __FILE__ ), array(
			'jquery',
			'select-2'
		), '' );
	}

	function add_lastviewed_id() {

		if ( is_singular() ) {

			global $post;
			$post_id             = $post->ID;
			$post_type           = get_post_type();
			$lastviewed_widgets  = get_option( 'widget_dd_last_viewed' );
			$post_selected_terms = $this->get_all_selected_terms( $post_id );
			array_push( $post_selected_terms, $post_type );

			foreach ( $lastviewed_widgets as $id => $lastviewed_widget ) {

				if ( $id != '_multiwidget' ) :

					$selection          = $lastviewed_widget["selection"] ? $lastviewed_widget["selection"] : array( );
					$matching_selection = array_intersect( $selection, $post_selected_terms );
					$exclude_ids        = explode( ',', $lastviewed_widget["lastviewed_excl_ids"] );
					$exclude_post       = in_array( $post_id, $exclude_ids ); //true/false

					if ( ! empty( $matching_selection ) && ! $exclude_post ) {

						$cookieName = "cookie_data_lastviewed_widget_" . $id;
						$cookieVal  = $_COOKIE[ $cookieName ];
						$oldList    = explode( ',', $cookieVal );
						$newList    = isset( $cookieVal ) ? array_diff( $oldList, array( $post_id ) ) : array( );
						array_push( $newList, $post_id );
						$newList = implode( ",", $newList );
						setcookie( $cookieName, $newList, time() + ( 60 * 60 * 24 * 30 ), "/" ); // 30 days
					}
				endif;
			}
		}
	}

	function shortcode_lastviewed( $atts ) {

		$args = array(
			'widget_id'    => $atts['widget_id'],
			'by_shortcode' => 'shortcode_',
		);

		ob_start();
		the_widget( 'lastviewed', '', $args );
		$output = ob_get_clean();

		return $output;
	}

	function get_all_selected_terms( $post_id ) {

		$selected_terms = array( );
		$args           = array( 'hide_empty' => 1, 'fields' => 'ids' );
		$taxonomies     = get_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			$termID         = wp_get_post_terms( $post_id, $taxonomy, $args );
			$selected_terms = array_merge( $selected_terms, $termID );
		}

		return $selected_terms;
	}

	function form( $instance ) {
		$args_custom_types          = array(
			'public'   => true,
			'_builtin' => false
		);//grab the post_types active in theme
		$args_default_types         = array(
			'public'   => true,
			'_builtin' => true
		);
		$lastviewedTitle            = isset( $instance['lastviewedTitle'] ) ? $instance['lastviewedTitle'] : "Last Viewed";
		$widgetID                   = str_replace( 'dd_last_viewed-', '', $this->id );
		$fieldID                    = $this->get_field_id( 'lastviewedTitle' );
		$fieldName                  = $this->get_field_name( 'lastviewedTitle' );
		$output                     = 'names'; // names or objects, note names is the default
		$operator                   = 'and'; // 'and' or 'or'
		$custom_post_types          = get_post_types( $args_custom_types, $output, $operator );
		$default_post_types         = get_post_types( $args_default_types, $output, $operator );
		$post_types                 = array_merge( $custom_post_types, $default_post_types );
		$lastViewed_total           = isset( $instance['lastViewed_total'] ) ? $instance['lastViewed_total'] : 5;
		$lastViewed_total           = esc_attr( $lastViewed_total );
		$lastViewed_truncate        = isset( $instance['lastViewed_truncate'] ) ? $instance['lastViewed_truncate'] : 78;
		$lastViewed_truncate        = esc_attr( $lastViewed_truncate );
		$lastViewed_linkname        = isset( $instance['lastViewed_linkname'] ) ? $instance['lastViewed_linkname'] : "More";
		$lastViewed_linkname        = esc_attr( $lastViewed_linkname );
		$lastViewed_showPostTitle   = isset( $instance['lastViewed_showPostTitle'] ) ? (bool) $instance['lastViewed_showPostTitle'] : false;
		$lastViewed_showThumb       = isset( $instance['lastViewed_showThumb'] ) ? (bool) $instance['lastViewed_showThumb'] : false;
		$lastViewed_thumbSize       = isset( $instance['lastViewed_thumbSize'] ) ? $instance['lastViewed_thumbSize'] : "thumbnail";
		$lastViewed_thumbSize       = esc_attr( $lastViewed_thumbSize );
		$lastViewed_showExcerpt     = isset( $instance['lastViewed_showExcerpt'] ) ? (bool) $instance['lastViewed_showExcerpt'] : false;
		$lastViewed_content_type    = isset( $instance['lastViewed_content_type'] ) ? $instance['lastViewed_content_type'] : "excerpt";
		$lastViewed_showTruncate    = isset( $instance['lastViewed_showTruncate'] ) ? (bool) $instance['lastViewed_showTruncate'] : false;
		$lastViewed_showMore        = isset( $instance['lastViewed_showMore'] ) ? (bool) $instance['lastViewed_showMore'] : false;
		$lastviewed_excl_ids        = isset( $instance['lastviewed_excl_ids'] ) ? $instance['lastviewed_excl_ids'] : "";
		$excl_ids_fieldID           = $this->get_field_id( 'lastviewed_excl_ids' );
		$excl_ids_fieldName         = $this->get_field_name( 'lastviewed_excl_ids' );
		$lastViewed_lv_link_title   = isset( $instance['lastViewed_lv_link_title'] ) ? (bool) $instance['lastViewed_lv_link_title'] : false;
		$lastViewed_lv_link_thumb   = isset( $instance['lastViewed_lv_link_thumb'] ) ? (bool) $instance['lastViewed_lv_link_thumb'] : false;
		$lastViewed_lv_link_excerpt = isset( $instance['lastViewed_lv_link_excerpt'] ) ? (bool) $instance['lastViewed_lv_link_excerpt'] : false;

		?>
		<p>
			<label for="<?php echo $fieldID; ?>">Titel:</label>
			<input id="<?php echo $fieldID; ?>" class=" widefat textWrite_Title" type="text"
			       value="<?php echo esc_attr( $lastviewedTitle ); ?>" name="<?php echo $fieldName; ?>">
		</p>

		<p>
			<label>Number of items to show: <label>
					<input type="number" name="<?php echo $this->get_field_name( 'lastViewed_total' ); ?>" min="1"
					       max="10" value="<?php echo $lastViewed_total; ?>">
		</p>
		<hr>

		<?php

		$selection = isset( $instance['selection'] ) ? $instance['selection'] : array( );

		?>
		<p class="selectholder"><label for="id_label_multiple">Filter on Posttypes/Terms:</label><br/>
			<select class="js-types-and-terms types-and-terms" id="id_label_multiple" multiple="multiple" tabindex="-1"
			        aria-hidden="true" name="<?php echo $this->get_field_name( 'selection' ) . '[]'; ?>">
				<optgroup label="Post Types">
					<?php foreach ( $post_types as $post_type ) :
						$selected = in_array( $post_type, $selection ) ? 'selected' : '';
						$obj = get_post_type_object( $post_type );
						$RealName = $obj->labels->name;

						?>
						<option <?php echo $selected; ?>
							value="<?php echo $post_type; ?>"><?php echo $RealName; ?></option>
					<?php endforeach; ?>
				</optgroup>
				<?php

				$args_taxonomies = array(
					'public' => 1
				);

				$args_terms = array(
					'hide_empty' => 0
				);
				$taxonomies = get_taxonomies( $args_taxonomies );

				foreach ( $taxonomies as $taxonomy ) :
					$terms = get_terms( $taxonomy, $args_terms );
					?>
					<optgroup label="<?php echo ucfirst( $taxonomy ); ?>">
						<?php foreach ( $terms as $term ) :
							$selected = in_array( $term->term_id, $selection ) ? 'selected' : '';
							?>
							<option <?php echo $selected; ?>
								value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="exclude_ids">
			<label for="<?php echo $excl_ids_fieldID; ?>">Exclude IDs (Separate with commas):</label>
			<input id="<?php echo $excl_ids_fieldID; ?>" class=" widefat textWrite_Title" type="text"
			       value="<?php echo esc_attr( $lastviewed_excl_ids ); ?>" placeholder="eg: 34,65,87"
			       name="<?php echo $excl_ids_fieldName; ?>">
		</p>
		<hr>

		<div class="showTitle LV_setting_row">
			<?php

			$checked            = $lastViewed_showPostTitle == true ? 'checked="checked"' : '';
			$checked_link_title = $lastViewed_lv_link_title == true ? 'checked="checked"' : '';
			$class_lv_link      = $checked_link_title ? 'button-primary' : '';
			$value              = $lastViewed_showPostTitle;
			$status             = $value == '1' ? 'on' : '';

			?>
			<div class="dd-switch <?php echo $status; ?>">
				<div class="switchHolder">
					<div class="onSquare button-primary"></div>
					<div class="buttonSwitch"></div>
					<div class="offSquare"></div>
				</div>
			</div>
			<input id="lastViewed_showPostTitle"
			       name="<?php echo $this->get_field_name( 'lastViewed_showPostTitle' ); ?>"
			       type="checkbox" <?php echo $checked; ?> title="Show Title"/>
			<div class="button lv_link <?php echo $class_lv_link; ?>"></div>
			<input id="lastViewed_lv_link_title"
			       name="<?php echo $this->get_field_name( 'lastViewed_lv_link_title' ); ?>"
			       type="checkbox" <?php echo $checked_link_title; ?> title="Link"/>
			<?php echo __( 'Title' ); ?>
		</div>

		<div class="showThumb LV_setting_row">

			<?php
			$checked            = $lastViewed_showThumb == true ? 'checked="checked"' : '';
			$checked_link_thumb = $lastViewed_lv_link_thumb == true ? 'checked="checked"' : '';
			$class_lv_link      = $checked_link_thumb ? 'button-primary' : '';
			$all_sizes          = get_intermediate_image_sizes();
			$value              = $lastViewed_showThumb;
			$status             = $value == '1' ? 'on' : '';

			?>
			<div class="dd-switch <?php echo $status; ?>">
				<div class="switchHolder">
					<div class="onSquare button-primary"></div>
					<div class="buttonSwitch"></div>
					<div class="offSquare"></div>
				</div>
			</div>
			<input id="lastViewed_showThumb" name="<?php echo $this->get_field_name( 'lastViewed_showThumb' ); ?>"
			       type="checkbox" <?php echo $checked; ?> title="Show"/>
			<div class="button lv_link <?php echo $class_lv_link; ?>"></div>
			<input id="lastViewed_lv_link_thumb"
			       name="<?php echo $this->get_field_name( 'lastViewed_lv_link_thumb' ); ?>"
			       type="checkbox" <?php echo $checked_link_thumb; ?> title="Link Thumbnail"/>
			<label for="tumbsizes"><?php echo __( 'Thumbnail' ); ?></label>
			<select id="tumbsizes" name="<?php echo $this->get_field_name( 'lastViewed_thumbSize' ); ?>">
				<?php
				foreach ( $all_sizes as $size ) {
					$selected = $lastViewed_thumbSize == $size ? 'selected' : '';
					echo '<option value="' . $size . '" ' . $selected . '>' . $size . '</option>';
				}
				?>
			</select>
		</div>

		<div class="showExcerpt LV_setting_row">
			<?php

			$checked              = $lastViewed_showExcerpt == true ? 'checked="checked"' : '';
			$checked_link_excerpt = $lastViewed_lv_link_excerpt == true ? 'checked="checked"' : '';
			$class_lv_link        = $checked_link_excerpt ? 'button-primary' : '';
			$value                = $lastViewed_showExcerpt;
			$status               = $value == '1' ? 'on' : '';

			?>
			<div class="dd-switch <?php echo $status; ?>">
				<div class="switchHolder">
					<div class="onSquare button-primary"></div>
					<div class="buttonSwitch"></div>
					<div class="offSquare"></div>
				</div>
			</div>
			<input id="lastViewed_showExcerpt"
			       name="<?php echo $this->get_field_name( 'lastViewed_showExcerpt' ); ?>"
			       type="checkbox" <?php echo $checked; ?> title="Show"/>
			<div class="button lv_link <?php echo $class_lv_link; ?>"></div>
			<input id="lastViewed_lv_link_excerpt"
			       name="<?php echo $this->get_field_name( 'lastViewed_lv_link_excerpt' ); ?>"
			       type="checkbox" <?php echo $checked_link_excerpt; ?> title="Link"/>
			<label for="textformat"><?php echo __( 'Show' ) . '  '; ?></label>
			<select id="textformat" name="<?php echo $this->get_field_name( 'lastViewed_content_type' ); ?>">
				<?php
				$all_contentTypes = array( 'excerpt', 'plain content', 'rich content' );
				foreach ( $all_contentTypes as $type ) {
					$selected = $lastViewed_content_type == $type ? 'selected' : '';
					echo '<option value="' . $type . '" ' . $selected . '>' . $type . '</option>';
				}
				?>
			</select>
		</div>

		<div class="showTruncate LV_setting_row">
			<?php
			$checked = $lastViewed_showTruncate == true ? 'checked="checked"' : '';
			$value   = $lastViewed_showTruncate;
			$status  = $value == '1' ? 'on' : '';

			?>
			<div class="dd-switch <?php echo $status; ?>">
				<div class="switchHolder">
					<div class="onSquare button-primary"></div>
					<div class="buttonSwitch"></div>
					<div class="offSquare"></div>
				</div>
			</div>
			<input id="lastViewed_showTruncate"
			       name="<?php echo $this->get_field_name( 'lastViewed_showTruncate' ); ?>"
			       type="checkbox" <?php echo $checked; ?>/>
			<label for="lastViewed_showTruncate"><?php echo __( 'Truncate' ) . '  '; ?></label>
			<input id="truncatenumber" type="number"
			       name="<?php echo $this->get_field_name( 'lastViewed_truncate' ); ?>" min="1" max="10"
			       value="<?php echo $lastViewed_truncate ?>">
			<label for="truncatenumber"><?php echo '  ' . __( 'Characters' ); ?></label>
		</div>

		<div class="showMore LV_setting_row">
			<?php

			$checked = $lastViewed_showMore == true ? 'checked="checked"' : '';
			$value   = $lastViewed_showMore;
			$status  = $value == '1' ? 'on' : '';

			?>
			<div class="dd-switch <?php echo $status; ?>">
				<div class="switchHolder">
					<div class="onSquare button-primary"></div>
					<div class="buttonSwitch"></div>
					<div class="offSquare"></div>
				</div>
			</div>
			<input id="lastViewed_showMore" name="<?php echo $this->get_field_name( 'lastViewed_showMore' ); ?>"
			       type="checkbox" <?php echo $checked; ?>/>
			<label for="lastViewed_showMore"><?php echo __( 'Breaklink' ) . '   '; ?></label>
			<input id="<?php echo $this->get_field_id( 'lastViewed_linkname' ); ?>" title="Breaklink label"
			       class="textWrite_Title" type="text"
			       value="<?php echo esc_attr( $lastViewed_linkname ); ?>"
			       name="<?php echo $this->get_field_name( 'lastViewed_linkname' ); ?>">
		</div>
		<hr>
		<?php if ( is_numeric( $widgetID ) ): ?>
			<p style="font-size: 11px; opacity:0.6">
				<span class="shortcodeTtitle">Shortcode:</span>
				<span class="shortcode">[dd_lastviewed widget_id="<?php echo $widgetID; ?>"]</span>
			</p>
		<?php endif; ?>
		<hr>
		<div class="donateReview">
			<a href="#" class="js-collapse collapse-trigger">Donate & Review</a>
			<div class="js-collapse-content collapse-content">
				<p>This software is free as in beer and as in freedom; however...........</p>
				<p>Donations allow me to spend more time developing all aspects of this plugin and providing the <a
						href="https://wordpress.org/support/plugin/dd-lastviewed">free support</a> that so many people
					have enjoyed. </p>
				<p>It also keeps me motivated: it is a great feeling for someone to be willing to pay ( even a few Euros
					) for something they can get for free. So be kind and please consider donating.</p>
				<p>If donating ( even a small amount ) is too much for you, but still you feel a little guilty ( because
					in your heart this plugin is one of your favourites ) consider then at least a <a
						href="https://wordpress.org/support/view/plugin-reviews/dd-lastviewed#postform">review</a> (it's
					free btw). Your 'free' review keeps me motivated as well and helps prospects to choose for this
					plugin. </p>
				<p>You can't make me happier if you do both! ;)</p>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=5V2C94HQAN63C&amp;lc=US&amp;item_name=Dijkstra%20Design&amp;currency_code=EUR&amp;bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted"
				   target="_blank" class="beer button button-secondary" title="Donate the developer">Donate</a>
				<a href="https://wordpress.org/support/view/plugin-reviews/dd-lastviewed#postform" target="_blank"
				   class="beer button button-secondary" title="Review Plugin">Review</a>

			</div>
		</div>
		<?php

	}

	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance                       = $old_instance;
		$instance['lastviewedTitle']    = strip_tags( $new_instance['lastviewedTitle'] );
		$instance['selected_posttypes'] = $new_instance['selected_posttypes'];
		$instance['selection']          = $new_instance['selection'];

		$instance['lastViewed_thumb']           = strip_tags( $new_instance['lastViewed_thumb'] );
		$instance['lastViewed_total']           = strip_tags( $new_instance['lastViewed_total'] );
		$instance['lastViewed_truncate']        = strip_tags( $new_instance['lastViewed_truncate'] );
		$instance['lastViewed_linkname']        = strip_tags( $new_instance['lastViewed_linkname'] );
		$instance['lastViewed_showPostTitle']   = (bool) $new_instance['lastViewed_showPostTitle'];
		$instance['lastViewed_showThumb']       = (bool) $new_instance['lastViewed_showThumb'];
		$instance['lastViewed_thumbSize']       = strip_tags( $new_instance['lastViewed_thumbSize'] );
		$instance['lastViewed_showExcerpt']     = (bool) $new_instance['lastViewed_showExcerpt'];
		$instance['lastViewed_content_type']    = strip_tags( $new_instance['lastViewed_content_type'] );
		$instance['lastViewed_showTruncate']    = (bool) $new_instance['lastViewed_showTruncate'];
		$instance['lastViewed_showMore']        = (bool) $new_instance['lastViewed_showMore'];
		$instance['lastViewed_lv_link_thumb']   = (bool) $new_instance['lastViewed_lv_link_thumb'];
		$instance['lastViewed_lv_link_title']   = (bool) $new_instance['lastViewed_lv_link_title'];
		$instance['lastViewed_lv_link_excerpt'] = (bool) $new_instance['lastViewed_lv_link_excerpt'];
		$instance['lastviewed_excl_ids']        = strip_tags( $new_instance['lastviewed_excl_ids'] );

		return $instance;
	}

	function is_posttype_checked( $post_type, $selected_post_types = array() ) {
		$selected_post_types = array_values( $selected_post_types );

		return in_array( $post_type, $selected_post_types );
	}
	
	function widget( $args, $instance ) {
		$widgetID                 = $args['widget_id'];
		$widgetID                 = str_replace( 'dd_last_viewed-', '', $widgetID );
		$widgetOptions            = get_option( $this->option_name );
		$thisWidget               = $widgetOptions[ $widgetID ];
		$lastviewedTitle          = $thisWidget['lastviewedTitle'];
		$lastViewed_total         = $thisWidget['lastViewed_total'] ? $thisWidget['lastViewed_total'] : -1 ;
		$lastViewed_truncate      = $thisWidget['lastViewed_truncate'] ? $thisWidget['lastViewed_truncate'] : false;
		$lastViewed_linkname      = $thisWidget['lastViewed_linkname'];
		$lastViewed_showPostTitle = $thisWidget['lastViewed_showPostTitle'];
		$lastViewed_showThumb     = $thisWidget['lastViewed_showThumb'];
		$lastViewed_thumbSize     = $thisWidget['lastViewed_thumbSize'];
		$lastViewed_showExcerpt   = $thisWidget['lastViewed_showExcerpt'];
		$lastViewed_content_type  = $thisWidget['lastViewed_content_type'];
		$lastViewed_showTruncate  = $thisWidget['lastViewed_showTruncate'];
		$lastViewed_showMore      = $thisWidget['lastViewed_showMore'];

		$lastViewed_lv_link_title   = $thisWidget['lastViewed_lv_link_title'];
		$lastViewed_lv_link_thumb   = $thisWidget['lastViewed_lv_link_thumb'];
		$lastViewed_lv_link_excerpt = $thisWidget['lastViewed_lv_link_excerpt'];
		$cookie_name                = 'cookie_data_lastviewed_widget_' . $widgetID;
		$lastlist                   = ( $_COOKIE[ $cookie_name ] );
		$idList                     = explode( ",", $lastlist );
		$idList                     = array_reverse( $idList );
		$idList                     = is_singular() ? array_diff( $idList, array( get_the_ID() ) ) : $idList; // strip this id from idlist if on single

		extract( $args, EXTR_SKIP );

		$list_args = array(
			'post__in'       => $idList,
			'post_type'      => 'any',
			'post_status'    => 'publish',
			'orderby'        => 'post__in',
			'posts_per_page' => $lastViewed_total
		);

		$list_query = new WP_Query( $list_args );
		$hasThumb = $lastViewed_showThumb ? $lastViewed_showThumb : false;

		if ( isset($lastlist)) :

			echo $before_widget;

			if ( $lastviewedTitle ) {
				echo $before_title . $lastviewedTitle . $after_title;
			}

			if ( ! $lastViewed_showPostTitle && ! $lastViewed_showExcerpt && ! $hasThumb ): ?>
				<p>No options set yet! Set the options in the <a
						href="<?php echo esc_url( home_url( '/wp-admin/widgets.php' ) ); ?>">widget</a>.</p>';
			<?php endif; ?>
			<ul class="lastViewedList">

				<?php while ( $list_query->have_posts() ) : $list_query->the_post();

					$id            = get_the_ID();
					$title         = get_the_title();
					$strip_content = $lastViewed_content_type == 'plain content'; // 1/0
					$regex         = '/\[dd_lastviewed(.*?)\]/'; //avoid shortcode '[lastviewed] in order to prevent a loop
					$content       = preg_replace( $regex, '', get_the_content() );
					$content       = apply_filters( 'the_content', $content );
					$content       = $strip_content ? strip_shortcodes( $content ) : $content;
					$content       = $strip_content ? wp_strip_all_tags( $content, true ) : $content;
					$content       = $lastViewed_content_type === 'excerpt' ? get_the_excerpt() : $content;
					$content       = $lastViewed_showTruncate ? substr( $content, 0, strrpos( substr( $content, 0, $lastViewed_truncate ), ' ' ) ) : $content;
					$thumb         = get_the_post_thumbnail( $id, $lastViewed_thumbSize );
					$hasThumb      = $lastViewed_showThumb && has_post_thumbnail() ? $lastViewed_showThumb : false;
					$perma         = get_permalink();
					$class         = $hasThumb ? "clearfix" : "";

					?>
					<li class="<?php echo $class; ?>">
						<?php if ( $hasThumb && ! $lastViewed_lv_link_thumb ): ?>
							<div class="lastViewedThumb"><?php echo $thumb; ?></div>
						<?php elseif ( $hasThumb && $lastViewed_lv_link_thumb ) : ?>
							<a class="lastViewedThumb" href="<?php echo $perma; ?>"><?php echo $thumb; ?></a>
						<?php endif; ?>

						<div class="lastViewedcontent">
							<?php if ( $lastViewed_showPostTitle && $lastViewed_lv_link_title ) : ?>
								<a class="lastViewedTitle" href="<?php echo $perma; ?>"><?php echo $title; ?></a>
							<?php elseif ( $lastViewed_showPostTitle && ! $lastViewed_lv_link_title ) : ?>
								<h3 class="lastViewedTitle"><?php echo $title; ?></h3>
							<?php endif; ?>

							<?php if ( $lastViewed_lv_link_excerpt && $lastViewed_showExcerpt ) : ?>
								<a href="<?php echo $perma; ?>" class="lastViewedExcerpt">
									<div>
										<?php echo $content; ?>
										<?php if ( $lastViewed_showMore ) : ?>
											<span class="more"><?php echo $lastViewed_linkname; ?></span>
										<?php endif; ?>
									</div>
								</a>
							<?php elseif ( ! $lastViewed_lv_link_excerpt && $lastViewed_showExcerpt ) : ?>
								<div class='lastViewedExcerpt'>
									<?php echo $content; ?>
									<?php if ( $lastViewed_showMore ) : ?>
										<a href="<?php echo $perma; ?>"
										   class="more"><?php echo $lastViewed_linkname; ?></a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php echo $after_widget; ?>
		<?php endif; ?>
		<?php wp_reset_query();
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget("lastviewed");' ) );