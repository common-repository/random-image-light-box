<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class rilb_cls_registerhook {
	public static function rilb_activation() {
	
		global $wpdb;

		add_option('random-image-light-box', "1.0");

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();
	
		$rilb_default_tables = "CREATE TABLE {$wpdb->prefix}randomimage_lb (
										ri_id INT unsigned NOT NULL AUTO_INCREMENT,
										ri_image VARCHAR(1024) NOT NULL default '',
										ri_link VARCHAR(1024) NOT NULL default '',
										ri_title VARCHAR(1024) NOT NULL default '',
										ri_width int(11) NOT NULL default '0',
										ri_group VARCHAR(10) NOT NULL default 'Group1',
										ri_status VARCHAR(3) NOT NULL default 'Yes',
										ri_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
										PRIMARY KEY (ri_id)
										) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $rilb_default_tables );
		
		$rilb_default_tablesname = array( 'randomimage_lb' );
	
		$rilb_errors = false;
		$rilb_missing_tables = array();
		foreach($rilb_default_tablesname as $table_name) {
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $wpdb->prefix.$table_name . "'")) != strtoupper($wpdb->prefix.$table_name)) {
				$rilb_missing_tables[] = $wpdb->prefix.$table_name;
			}
		}
		
		if($rilb_missing_tables) {
			$errors[] = __( 'These tables could not be created on installation ' . implode(', ',$rilb_missing_tables), 'random-image-light-box' );
			$rilb_errors = true;
		}
		
		if($rilb_errors) {
			wp_die( __( $errors[0] , 'random-image-light-box' ) );
			return false;
		} 
		else {
			rilb_cls_dbquery::rilb_default();
		}
		
		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_rilb_activation_redirect', 1, 30 );
		}
		
		return true;
	}

	public static function rilb_deactivation() {
		// do not generate any output here
	}

	public static function rilb_adminoptions() {
	
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		
		switch($current_page) {
			case 'edit':
				require_once(RILBP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-edit.php');
				break;
			case 'add':
				require_once(RILBP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-add.php');
				break;
			default:
				require_once(RILBP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-show.php');
				break;
		}
	}
	
	public static function rilb_frontscripts() {
		if (!is_admin()) {
			wp_enqueue_style( 'random-image-light-box',  plugin_dir_url( __DIR__ ) . '/css/lightbox.css','','','');
			wp_enqueue_script( 'jquery');
			wp_enqueue_script( 'random-image-light-box', plugin_dir_url( __DIR__ ) . '/js/lightbox.js');
		}	
	}

	public static function rilb_addtomenu() {
	
		if (is_admin()) {
			add_options_page( __('Random image', 'random-image-light-box'), 
								__('Random image', 'random-image-light-box'), 'manage_options', 
									'random-image-light-box', array( 'rilb_cls_registerhook', 'rilb_adminoptions' ) );
		}
	}
	
	public static function rilb_adminscripts() {
	
		if(!empty($_GET['page'])) {
			switch ($_GET['page']) {
				case 'random-image-light-box':
					wp_register_script( 'randomimage-adminscripts', plugin_dir_url( __DIR__ ) . '/pages/setting.js', '', '', true );
					wp_enqueue_script( 'randomimage-adminscripts' );
					$rilb_select_params = array(
						'rilb_image'  		=> __( 'Please enter the image path.', 'randomimage-select', 'random-image-light-box' ),
						'rilb_group'  		=> __( 'Please enter the image group.', 'randomimage-select', 'random-image-light-box' ),
						'rilb_width'  		=> __( 'Please enter thumbnail image width.', 'randomimage-select', 'random-image-light-box' ),
						'rilb_numletters'  	=> __( 'Please input numeric and letters only.', 'randomimage-select', 'random-image-light-box' ),
						'rilb_delete'  		=> __( 'Do you want to delete this record?', 'randomimage-select', 'random-image-light-box' ),
					);
					wp_localize_script( 'randomimage-adminscripts', 'rilb_adminscripts', $rilb_select_params );
					break;
			}
		}
	}
	
	public static function rilb_widgetloading() {
		register_widget( 'rilb_widget_register' );
	}
}

class rilb_widget_register extends WP_Widget 
{
	function __construct() {
		$widget_ops = array('classname' => 'widget_text randomimage-widget', 'description' => __('Random image light box', 'random-image-light-box'), 'random-image-light-box');
		parent::__construct('random-image-light-box', __('Random image light box', 'random-image-light-box'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		$ri_title 		= apply_filters( 'widget_title', empty( $instance['ri_title'] ) ? '' : $instance['ri_title'], $instance, $this->id_base );
		$ri_group		= $instance['ri_group'];
		$ri_folder		= $instance['ri_folder'];
		$ri_width		= $instance['ri_width'];
	
		echo $args['before_widget'];
		if (!empty($ri_title)) {
			echo $args['before_title'] . $ri_title . $args['after_title'];
		}
		
		$data = array(
			'group' 	=> $ri_group,
			'folder' 	=> $ri_folder,
			'width' 	=> $ri_width
		);
		
		rilb_cls_shortcode::rilb_render($data);
		
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) {		
		$instance 					= $old_instance;
		$instance['ri_title'] 		= ( ! empty( $new_instance['ri_title'] ) ) ? strip_tags( $new_instance['ri_title'] ) : '';
		$instance['ri_group'] 		= ( ! empty( $new_instance['ri_group'] ) ) ? strip_tags( $new_instance['ri_group'] ) : '';
		$instance['ri_folder'] 		= ( ! empty( $new_instance['ri_folder'] ) ) ? strip_tags( $new_instance['ri_folder'] ) : '';
		$instance['ri_width'] 		= ( ! empty( $new_instance['ri_width'] ) ) ? strip_tags( $new_instance['ri_width'] ) : '';
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array(
			'ri_title' 		=> '',
		    'ri_group' 		=> '',
			'ri_folder' 	=> '',
			'ri_width' 		=> ''
        );
		
		$instance 		= wp_parse_args( (array) $instance, $defaults);
		$ri_title 		= $instance['ri_title'];
        $ri_group 		= $instance['ri_group'];
		$ri_folder 		= $instance['ri_folder'];
		$ri_width 		= $instance['ri_width'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('ri_title'); ?>"><?php _e('Title', 'random-image-light-box'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('ri_title'); ?>" name="<?php echo $this->get_field_name('ri_title'); ?>" type="text" value="<?php echo $ri_title; ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('ri_group'); ?>"><?php _e('Image group', 'random-image-light-box'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('ri_group'); ?>" name="<?php echo $this->get_field_name('ri_group'); ?>">
			<option value="">Select (Use folder)</option>
			<?php
			$groups = array();
			$groups = rilb_cls_dbquery::rilb_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo $group['ri_group']; ?>" <?php $this->ri_selected($group['ri_group'] == $ri_group); ?>>
					<?php echo $group['ri_group']; ?>
					</option>
					<?php
				}
			}
			?>
			</select>
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('ri_folder'); ?>"><?php _e('Folder', 'random-image-light-box'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('ri_folder'); ?>" name="<?php echo $this->get_field_name('ri_folder'); ?>" type="text" value="<?php echo $ri_folder; ?>" />
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('ri_width'); ?>"><?php _e('Thumbnail width', 'random-image-light-box'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('ri_width'); ?>" name="<?php echo $this->get_field_name('ri_width'); ?>" type="text" value="<?php echo $ri_width; ?>" />
        </p>
		<?php
	}
	
	function ri_selected($var) {
		if ($var==1 || $var==true) {
			echo 'selected="selected"';
		}
	}
}

class rilb_cls_shortcode {
	public function __construct() {
	}
	
	public static function rilb_shortcode( $atts ) {
		ob_start();
		if (!is_array($atts)) {
			return '';
		}
		
		//[random-image-light-box group="Group1"]
		//[random-image-light-box folder="" width="100"]
		$atts = shortcode_atts( array(
				'group'			=> '',
				'folder'		=> '',
				'width'			=> ''
			), $atts, 'random-image-light-box' );

		$group 		= isset($atts['group']) ? $atts['group'] : '';
		$folder 	= isset($atts['folder']) ? $atts['folder'] : '';
		$width 		= isset($atts['width']) ? $atts['width'] : '';

		$data = array(
			'group' 	=> $group,
			'folder' 	=> $folder,
			'width' 	=> $width
		);
		
		self::rilb_render( $data );

		return ob_get_clean();
	}
	
	public static function rilb_render( $data = array() ) {	
		
		$ri = "";
		$datas = array();
		$imglist = "";
		
		if(count($data) == 0) {
			return $ri;
		}

		$group 		= $data['group'];
		$folder		= $data['folder'];
		$width		= $data['width'];
		
		if($group <> "") {
			$datas = rilb_cls_dbquery::rilb_select_bygroup_rand($group);
		}
		else if($folder <> "") {
			$siteurl_link = get_option('siteurl');
			if (rilb_cls_dbquery::endswith($siteurl_link, '/') == false) {
				$siteurl_link = $siteurl_link . "/";
			}
			
			if(is_dir($folder)) {		
				$dirhandle = opendir($folder);
				
				while ($file = readdir($dirhandle)) {
					if(!is_dir($file) && (strpos(strtoupper($file), '.JPG') > 0 or 
						strpos(strtoupper($file), '.GIF') > 0 or 
							strpos(strtoupper($file), '.JPEG') > 0 or 
								strpos(strtoupper($file), '.PNG') > 0) )
					{
						$imglist .= "$file ";
					}
				}
				
				$imglist = explode(" ", $imglist);
				$no = sizeof($imglist) - 2;
				if($no >= 0) {
					$random = mt_rand(0, $no);
					$image = $imglist[$random];
				}
				
				if (rilb_cls_dbquery::endswith($folder, '/') == false) {
					$folder = $folder . "/";
				}
				$datas['ri_link'] = $siteurl_link . $folder . $image;
				$datas['ri_title'] = "";
				$datas['ri_image'] = $siteurl_link . $folder . $image;
				$datas['ri_width'] = $width;
			}
			else {
				// Folder not exists
			}
		}
		
		if(count($datas) > 0 ) {
			$width = "";
			if ($datas['ri_width'] <> "" && is_numeric($datas['ri_width'])) {
				if($datas['ri_width'] > 10) {
					$width = 'width="' . $datas['ri_width'] . '"';
				}
			}
			
			if ($datas['ri_link'] == "") {
				$datas['ri_link'] = $datas['ri_image'];
			}
			
			$ri = '<div>';
				$ri .= '<a class="rilb-image-link" href="'.$datas['ri_link'].'" data-lightbox="rilb-1" data-title="'.$datas['ri_title'].'">';
					$ri .= '<img class="rilb-image" src="'.$datas['ri_image'].'" alt="'.$datas['ri_title'].'" ' . $width . ' />';
				$ri .= '</a>';
			$ri .= '</div>';
		}
		echo $ri;
	}
}
?>