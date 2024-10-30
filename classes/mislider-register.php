<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class mislider_cls_registerhook {
	public static function mislider_activation() {
	
		global $wpdb;

		add_option('moving-image-slider', "1.0");

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();
	
		$mislider_default_tables = "CREATE TABLE {$wpdb->prefix}moving_image_slider (
										mislider_id INT unsigned NOT NULL AUTO_INCREMENT,
										mislider_title VARCHAR(1024) NOT NULL default '',
										mislider_desc VARCHAR(1024) NOT NULL default '',
										mislider_img VARCHAR(1024) NOT NULL default '',
										mislider_slide VARCHAR(30) NOT NULL default '',
										mislider_group VARCHAR(10) NOT NULL default 'Group1',
										mislider_status VARCHAR(3) NOT NULL default 'Yes',
										mislider_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
										PRIMARY KEY (mislider_id)
										) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $mislider_default_tables );
		
		$mislider_default_tablesname = array( 'moving_image_slider' );
	
		$mislider_errors = false;
		$mislider_missing_tables = array();
		foreach($mislider_default_tablesname as $table_name) {
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $wpdb->prefix.$table_name . "'")) != strtoupper($wpdb->prefix.$table_name)) {
				$mislider_missing_tables[] = $wpdb->prefix.$table_name;
			}
		}
		
		if($mislider_missing_tables) {
			$errors[] = __( 'These tables could not be created on installation ' . implode(', ',$mislider_missing_tables), 'moving-image-slider' );
			$mislider_errors = true;
		}
		
		if($mislider_errors) {
			wp_die( __( $errors[0] , 'moving-image-slider' ) );
			return false;
		} 
		else {
			mislider_cls_dbquery::mislider_default();
		}
				
		return true;
	}

	public static function mislider_deactivation() {
		// do not generate any output here
	}

	public static function mislider_adminoptions() {
	
		global $wpdb;
		$current_page = isset($_GET['ac']) ? sanitize_text_field($_GET['ac']) : '';
		
		switch($current_page) {
			case 'edit':
				require_once(MISLIDERS_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-edit.php');
				break;
			case 'add':
				require_once(MISLIDERS_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-add.php');
				break;
			default:
				require_once(MISLIDERS_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-show.php');
				break;
		}
	}
	
	public static function mislider_frontscripts() {
		if (!is_admin()) {
			$folderpath = plugin_dir_url( __DIR__ );
			if (mislider_cls_dbquery::mislider_endswith($folderpath, '/') == false) {
				$folderpath = $folderpath . "/";
			}
			wp_enqueue_style( 'moving-image-slider', $folderpath . 'inc/moving-image-slider.css');
			wp_enqueue_script( 'jquery');
			wp_enqueue_script( 'moving-image-slider', $folderpath . 'inc/jquery-moving-image-slider.js');		
		}	
	}

	public static function mislider_addtomenu() {
	
		if (is_admin()) {
			add_options_page( __('Moving image slider', 'moving-image-slider'), 
								__('Moving image slider', 'moving-image-slider'), 'manage_options', 
									'moving-image-slider', array( 'mislider_cls_registerhook', 'mislider_adminoptions' ) );
		}
	}
	
	public static function mislider_adminscripts() {
	
		if(!empty($_GET['page'])) {
			switch (sanitize_text_field($_GET['page'])) {
				case 'moving-image-slider':
					wp_register_script( 'moving-image-adminscripts', plugin_dir_url( __DIR__ ) . '/pages/setting.js', '', '', true );
					wp_enqueue_script( 'moving-image-adminscripts' );
					$mislider_select_params = array(
						'mislider_title'  		=> __( 'Please enter image title.', 'mis-select', 'moving-image-slider' ),
						'mislider_desc'  		=> __( 'Please enter image description.', 'mis-select', 'moving-image-slider' ),
						'mislider_image'  		=> __( 'Please enter image path.', 'mis-select', 'moving-image-slider' ),
						'mislider_group'  		=> __( 'Please enter image group.', 'mis-select', 'moving-image-slider' ),
						'mislider_slide'  		=> __( 'Please select slide option.', 'mis-select', 'moving-image-slider' ),
						'mislider_numletters'  	=> __( 'Please input numeric and letters only.', 'mis-select', 'moving-image-slider' ),
						'mislider_delete'  		=> __( 'Do you want to delete this record?', 'mis-select', 'moving-image-slider' ),
					);
					wp_localize_script( 'moving-image-adminscripts', 'mislider_adminscripts', $mislider_select_params );
					break;
			}
		}
	}
	
	public static function mislider_widgetloading() {
		register_widget( 'mislider_widget_register' );
	}
}

class mislider_widget_register extends WP_Widget 
{
	function __construct() {
		$widget_ops = array('classname' => 'widget_text moving-image-widget', 'description' => __('Moving image slider', 'moving-image-slider'), 'moving-image-slider');
		parent::__construct('moving-image-slider', __('Moving image slider', 'moving-image-slider'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		$mislider_title 	= apply_filters( 'widget_title', empty( $instance['mislider_title'] ) ? '' : $instance['mislider_title'], $instance, $this->id_base );
		$mislider_group		= $instance['mislider_group'];
		$mislider_id		= $instance['mislider_id'];
		$mislider_desc		= $instance['mislider_desc'];
		$mislider_width		= $instance['mislider_width'];
		$mislider_height	= $instance['mislider_height'];
	
		echo $args['before_widget'];
		if (!empty($mislider_title)) {
			echo $args['before_title'] . $mislider_title . $args['after_title'];
		}
		
		$data = array(
			'group' => $mislider_group,
			'id' 	=> $mislider_id,
			'desc'	=> $mislider_desc,
			'width'	=> $mislider_width,
			'height'=> $mislider_height
		);
		
		mislider_cls_shortcode::mislider_render($data);
		
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) {	
		$instance 					= $old_instance;
		$instance['mislider_title'] = ( ! empty( $new_instance['mislider_title'] ) ) ? strip_tags( $new_instance['mislider_title'] ) : '';
		$instance['mislider_group'] = ( ! empty( $new_instance['mislider_group'] ) ) ? strip_tags( $new_instance['mislider_group'] ) : '';
		$instance['mislider_id'] 	= ( ! empty( $new_instance['mislider_id'] ) ) ? strip_tags( $new_instance['mislider_id'] ) : '';
		$instance['mislider_desc'] 	= ( ! empty( $new_instance['mislider_desc'] ) ) ? strip_tags( $new_instance['mislider_desc'] ) : '';
		$instance['mislider_width'] = ( ! empty( $new_instance['mislider_width'] ) ) ? strip_tags( $new_instance['mislider_width'] ) : '';
		$instance['mislider_height'] = ( ! empty( $new_instance['mislider_height'] ) ) ? strip_tags( $new_instance['mislider_height'] ) : '';
		return $instance;	
	}
	
	function form( $instance ) {
		$defaults = array(
			'mislider_title' => '',
		    'mislider_group' => '',
			'mislider_id' 	 => '',
			'mislider_desc' => '',
			'mislider_width' => '',
			'mislider_height' => ''
        );
		
		$instance 	= wp_parse_args( (array) $instance, $defaults);
		$mislider_title = $instance['mislider_title'];
        $mislider_group = $instance['mislider_group'];
		$mislider_id 	= $instance['mislider_id'];
		$mislider_desc 	= $instance['mislider_desc'];
		$mislider_width = $instance['mislider_width'];
		$mislider_height = $instance['mislider_height'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('mislider_title'); ?>"><?php _e('Title', 'moving-image-slider'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('mislider_title'); ?>" name="<?php echo $this->get_field_name('mislider_title'); ?>" type="text" value="<?php echo $mislider_title; ?>" />
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('mislider_group'); ?>"><?php _e('Image Group', 'moving-image-slider'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('mislider_group'); ?>" name="<?php echo $this->get_field_name('mislider_group'); ?>">
			<option value="">Select (Use Image Id)</option>
			<?php
			$groups = array();
			$groups = mislider_cls_dbquery::mislider_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo $group['mislider_group']; ?>" <?php $this->mislider_selected($group['mislider_group'] == $mislider_group); ?>>
					<?php echo $group['mislider_group']; ?>
					</option>
					<?php
				}
			}
			?>
			</select>
        </p>
			
		<p>
			<label for="<?php echo $this->get_field_id('mislider_id'); ?>"><?php _e('Image ID', 'moving-image-slider'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('mislider_id'); ?>" name="<?php echo $this->get_field_name('mislider_id'); ?>" type="text" value="<?php echo $mislider_id; ?>" />
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('mislider_desc'); ?>"><?php _e('Display Description', 'moving-image-slider'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('mislider_desc'); ?>" name="<?php echo $this->get_field_name('mislider_desc'); ?>">
			<option value="No">No</option>
			<option value="Yes">Yes</option>
			</select>
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('mislider_width'); ?>"><?php _e('Width', 'moving-image-slider'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('mislider_width'); ?>" name="<?php echo $this->get_field_name('mislider_width'); ?>" type="text" value="<?php echo $mislider_width; ?>" />
        	<span><?php _e('Example:  500px or 100%', 'moving-image-slider'); ?></span>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('mislider_height'); ?>"><?php _e('Height', 'moving-image-slider'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('mislider_height'); ?>" name="<?php echo $this->get_field_name('mislider_height'); ?>" type="text" value="<?php echo $mislider_height; ?>" />
			<span><?php _e('Example:  200px', 'moving-image-slider'); ?></span>
        </p>
		<?php
	}
	
	function mislider_selected($var) {
		if ($var==1 || $var==true) {
			echo 'selected="selected"';
		}
	}
}

class mislider_cls_shortcode {
	public function __construct() {
	}
	
	public static function mislider_shortcode( $atts ) {
		ob_start();
		if (!is_array($atts)) {
			return '';
		}
		
		//[moving-image-slider group="Group1" desc="Yes" width = "336px" height = "200px"]
		//[moving-image-slider id="1" desc="Yes" width = "336px" height = "200px"]
		$atts = shortcode_atts( array(
				'group'	=> '',
				'id'	=> '',
				'desc'	=> '',
				'width' => '',
				'height'=> ''
			), $atts, 'moving-image-slider' );

		$group 	= isset($atts['group']) ? $atts['group'] : '';
		$id 	= isset($atts['id']) ? $atts['id'] : '';
		$desc 	= isset($atts['desc']) ? $atts['desc'] : 'Yes';
		$width 	= isset($atts['width']) ? $atts['width'] : '100%';
		$height = isset($atts['height']) ? $atts['height'] : '200px';
		
		$data = array(
			'group' => $group,
			'id' 	=> $id,
			'desc'	=> $desc,
			'width'	=> $width,
			'height'=> $height
		);
		
		self::mislider_render( $data );

		return ob_get_clean();
	}
	
	public static function mislider_render( $input = array() ) {	
		
		$mis = "";
		$playlist = "";
		$slide = "";
		$datas = array();
		
		$group = "";
		$id = "";
		$desc = "";
		$width = "";
		$height = "";
		
		if(count($input) == 0) {
			return $mis;
		}
		
		$group 	= sanitize_text_field($input['group']);
		$id		= intval($input['id']);
		$desc 	= sanitize_text_field($input['desc']);
		$width 	= sanitize_text_field($input['width']);
		$height = sanitize_text_field($input['height']);
		
		if ( $width == "") {
			$width = "100%";
		}
		
		if ( $height == "") {
			$height = "200px";
		}
			
		$datas = mislider_cls_dbquery::mislider_select_shortcode($id, $group);
		
		if(count($datas) > 0 ) {
			
			$zoomin = '"z1":1, "z2":1.5';
			$zoomout = '"z1":2, "z2":1';
			$panup = '"y1":0, "y2":200';
			$panleft = '"x1":0, "x2":20, "z1":1';

			foreach ( $datas as $data ) 
			{
				if ($data["mislider_slide"] == "Random") {
					$mislider_slide = array("Zoom In", "Zoom Out", "Pan Up", "Pan Left");
					$random_val = array_rand($mislider_slide, 2);
					$data["mislider_slide"] = $mislider_slide[$random_val[0]];
				}
				
				if ($data["mislider_slide"] == "Zoom In") {
					$slide = $zoomin;
				}
				elseif ($data["mislider_slide"] == "Zoom Out") {
					$slide = $zoomout;
				}
				elseif ($data["mislider_slide"] == "Pan Up") {
					$slide = $panup;
				}
				elseif ($data["mislider_slide"] == "Pan Left") {
					$slide = $panleft;
				}
				else {
					$slide = $zoomin;
				}
				
				if ($desc == "No") {
					$data['mislider_desc'] = "";
				}
				
				$playlist .= '{"url":"' . $data['mislider_img'] . '", "caption":"' . $data['mislider_desc'] . '", "slide":{' . $slide . '}}, ';
			}
			
			$loading = plugin_dir_url( __DIR__ );
			if (mislider_cls_dbquery::mislider_endswith($loading, '/') == false) {
				$loading = $loading . "/";
			}
			$loading = $loading  . 'inc/loading.gif';
					
			$mis .= '<style>';
			$mis .= '.mislider_container{';
				$mis .= 'width: ' . $width . ';';
				$mis .= 'height: ' . $height . ';';
				$mis .= 'position: relative;';
				$mis .= 'overflow: hidden;';
			$mis .= '}';
			$mis .= '</style>';
			
			$mis .= '<script>';
				$mis .= 'jQuery(function() {';
					$mis .= 'jQuery("#mislider_images").smoothslider("install", {';
						$mis .= '"playlist":[' . $playlist . '],';
						$mis .= '"onimage":function(caption, image_url) { ';
								$mis .= 'var area= jQuery("#mislider_message").find("span");';
								$mis .= 'area.animate({"opacity": 0}, 500, "swing", function() {';
									$mis .= 'area.text(caption);';
									$mis .= 'area.animate({"opacity": 1}, 500);';
								$mis .= '});';
							$mis .= '},';
						$mis .= '"hold_time":4,';
						$mis .= '"transition_time":2,';
						$mis .= '"loops": 5,';
						$mis .= '"loading":jQuery("#mislider_loading"),';
					$mis .= '});';
				$mis .= '});';
			$mis .= '</script>';

			$mis .= '<div class="mislider_container">';
				$mis .= '<div id="mislider_loading"><img src="' . $loading  . '"></div>';
				$mis .= '<div id="mislider_images"></div>';
				if (strtoupper($desc) == "YES" || strtoupper($desc) == "TRUE") {
					$mis .= '<div id="mislider_message"><span></span></div>';
				}
			$mis .= '</div>';
		}
		
		echo $mis;
	}
}
?>