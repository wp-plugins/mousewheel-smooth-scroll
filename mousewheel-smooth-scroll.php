<?php
/*
	Plugin Name: MouseWheel Smooth Scroll
	Plugin URI: http://kubiq.sk
	Description: MouseWheel smooth scrolling for your WordPress website
	Version: 1.0.4
	Author: Jakub Novák
	Author URI: http://kubiq.sk
*/

if (!class_exists('wpmss')) {
	class wpmss {
		var $domain = 'wpmss';
		var $plugin_admin_page;
		var $settings;
		var $tab;
		
		function wpmss_func(){ $this->__construct(); }	
		
		function __construct(){
			$mo = plugin_dir_path(__FILE__) . 'languages/' . $this->domain . '-' . get_locale() . '.mo';
			load_textdomain($this->domain, $mo);
			add_action( 'admin_menu', array( &$this, 'plugin_menu_link' ) );
			add_action( 'init', array( &$this, "plugin_init" ) );
		}
		
		function filter_plugin_actions($links, $file) {
		   $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
		   array_unshift( $links, $settings_link );
		   return $links;
		}
		
		function plugin_menu_link() {
			$this->plugin_admin_page = add_submenu_page(
				'options-general.php',
				__( 'MouseWheel Smooth Scroll', $this->domain ),
				__( 'MouseWheel Smooth Scroll', $this->domain ),
				'manage_options',
				basename(__FILE__),
				array( $this, 'admin_options_page' )
			);
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'filter_plugin_actions'), 10, 2 );
		}
		
		function plugin_init(){
			$this->settings = get_option('wpmss_settings');
			add_action( 'wp_enqueue_scripts', array($this, 'plugin_scripts_load') );
		}

		function plugin_scripts_load() {
			wp_enqueue_script( 'wpmss_jquery_mousewheel', plugins_url( 'js/jquery.mousewheel.min.js' , __FILE__ ));
			wp_enqueue_script( 'wpmss_jquery_easing', plugins_url( 'js/jquery.easing.1.3.js' , __FILE__ ));
			$options = array(
				'step' => isset( $this->settings['general']['step'] ) && trim( $this->settings['general']['step'] ) != "" ? $this->settings['general']['step'] : 120,
				'speed' => isset( $this->settings['general']['speed'] ) && trim( $this->settings['general']['step'] ) != "" ? $this->settings['general']['speed'] : 800,
				'ease' => isset( $this->settings['general']['ease'] ) ? $this->settings['general']['ease'] : 'easeOutCubic',
				'enableAll' => isset( $this->settings['general']['enable_mac'] ) ? 1 : 0
			);
			wp_enqueue_script( 'wpmss_simplr_smoothscroll', plugins_url( 'js/jquery.simplr.smoothscroll.js' , __FILE__ ));
			wp_enqueue_script( 'wpmss_script', plugins_url( 'js/wpmss.php?'.http_build_query($options) , __FILE__ ));
		}
		
		function plugin_admin_tabs( $current = 'general' ) {
			$tabs = array( 'general' => __('General'), 'info' => __('Help') ); ?>
			<h2 class="nav-tab-wrapper">
			<?php foreach( $tabs as $tab => $name ){ ?>
				<a class="nav-tab <?php echo ( $tab == $current ) ? "nav-tab-active" : "" ?>" href="?page=<?php echo basename(__FILE__) ?>&amp;tab=<?php echo $tab ?>"><?php echo $name ?></a>
			<?php } ?>
			</h2><br><?php
		}

		function admin_options_page() {
			if ( get_current_screen()->id != $this->plugin_admin_page ) return;
			$this->tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';
			if(isset($_POST['plugin_sent'])) $this->settings[ $this->tab ] = $_POST;
			update_option( "wpmss_settings", $this->settings ); ?>
			<div class="wrap">
				<h2><?php _e( 'MouseWheel Smooth Scroll', $this->domain ); ?></h2>
				<?php if(isset($_POST['plugin_sent'])) echo '<div id="message" class="below-h2 updated"><p>'.__( 'Settings saved.' ).'</p></div>'; ?>
				<form method="post" action="<?php admin_url( 'options-general.php?page=' . basename(__FILE__) ); ?>">
					<input type="hidden" name="plugin_sent" value="1"><?php
					$this->plugin_admin_tabs( $this->tab );
					switch ( $this->tab ) :
						case 'general' :
							$this->plugin_general_options();
							break;
						case 'info' :
							$this->plugin_info_options();
							break;
					endswitch; ?>
				</form>
			</div><?php
		}
		
		function plugin_general_options(){ ?>
			<table class="form-table">
				<tr>
					<th>
						<label for="q_field_1"><?php _e("Step:", $this->domain) ?></label> 
					</th>
					<td>
						<input type="text" name="step" placeholder="120" value="<?php echo $this->settings[ $this->tab ]["step"]; ?>" id="q_field_1">
					</td>
				</tr>
				<tr>
					<th>
						<label for="q_field_2"><?php _e("Speed:", $this->domain) ?></label> 
					</th>
					<td>
						<input type="text" name="speed" placeholder="800" value="<?php echo $this->settings[ $this->tab ]["speed"]; ?>" id="q_field_2">
					</td>
				</tr>
				<tr>
					<th>
						<label for="q_field_3"><?php _e("Ease:", $this->domain) ?></label> 
					</th>
					<td><?php
						$this->q_select(array(
							"name" => "ease",
							"id" => "q_field_3",
							"value" => isset( $this->settings[ $this->tab ]["ease"] ) ? $this->settings[ $this->tab ]["ease"] : 'easeOutCubic',
							"options" => array(
								'linear' => 'linear',
								'swing' => 'swing',
								'easeInQuad' => 'easeInQuad',
								'easeOutQuad' => 'easeOutQuad',
								'easeInOutQuad' => 'easeInOutQuad',
								'easeInCubic' => 'easeInCubic',
								'easeOutCubic' => 'easeOutCubic',
								'easeInOutCubic' => 'easeInOutCubic',
								'easeInQuart' => 'easeInQuart',
								'easeOutQuart' => 'easeOutQuart',
								'easeInOutQuart' => 'easeInOutQuart',
								'easeInQuint' => 'easeInQuint',
								'easeOutQuint' => 'easeOutQuint',
								'easeInOutQuint' => 'easeInOutQuint',
								'easeInExpo' => 'easeInExpo',
								'easeOutExpo' => 'easeOutExpo',
								'easeInOutExpo' => 'easeInOutExpo',
								'easeInSine' => 'easeInSine',
								'easeOutSine' => 'easeOutSine',
								'easeInOutSine' => 'easeInOutSine',
								'easeInCirc' => 'easeInCirc',
								'easeOutCirc' => 'easeOutCirc',
								'easeInOutCirc' => 'easeInOutCirc',
								'easeInElastic' => 'easeInElastic',
								'easeOutElastic' => 'easeOutElastic',
								'easeInOutElastic' => 'easeInOutElastic',
								'easeInBack' => 'easeInBack',
								'easeOutBack' => 'easeOutBack',
								'easeInOutBack' => 'easeInOutBack',
								'easeInBounce' => 'easeInBounce',
								'easeOutBounce' => 'easeOutBounce',
								'easeInOutBounce' => 'easeInOutBounce'
							)
						)); ?>&emsp;<small><a href="http://easings.net/" target="_blank">(<?php _e("try easings", $this->domain) ?>)</a></small>
					</td>
				</tr>
				<tr>
					<th>
						<label for="q_field_4"><?php _e("Enable for MAC and non-WebKit browsers (experimental)", $this->domain) ?></label> 
					</th>
					<td>
						<input type="checkbox" name="enable_mac" value="checked" id="q_field_4" <?php echo isset( $this->settings[ $this->tab ]["enable_mac"] ) ? $this->settings[ $this->tab ]["enable_mac"] : "" ?>>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php _e( 'Save' ) ?>"></p><?php
		}
		
		function plugin_info_options(){ ?>
			<p><?php _e('Any ideas, problems, issues?', $this->domain) ?></p>
			<p>Ing. Jakub Novák</p>
			<p><a href="mailto:info@kubiq.sk" target="_blank">info@kubiq.sk</a></p>
			<p><a href="http://kubiq.sk/" target="_blank">http://kubiq.sk</a></p><?php
		}

		function q_select( $field_data = array(), $print = 1 ){
			if(!is_object($field_data)) $field_data = (object)$field_data;
			$field_data->value = is_array($field_data->value) ? $field_data->value : array($field_data->value);
			$select = "<select name='{$field_data->name}' id='{$field_data->id}'".( isset($field_data->multiple) ? " multiple" : "").( isset($field_data->size) ? " size='{$field_data->size}'" : "").">";
			if( isset($field_data->placeholder) ) $select .= "<option value='' disabled>{$field_data->placeholder}</option>";
			foreach($field_data->options as $option => $value) $select .= "<option value='{$value}'".( in_array($value, $field_data->value) ? " selected" : "").">{$option}</option>";
			$select .= "</select>";
			if($print)
				echo $select;
			else
				return $select;
		}
	}
}

if (class_exists('wpmss')) { 
	$wpmss_var = new wpmss();
} ?>