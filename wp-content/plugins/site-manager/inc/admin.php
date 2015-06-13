<?php

if ( ! defined('ABSPATH') ) {
	die();
}

class Site_Manager_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'current_screen', array( $this, 'register_scripts' ), 1 );
		add_action( 'current_screen', array( $this, 'register_settings' ), 1 );
	}

	public function register_menu() {
		add_submenu_page(
			'edit.php?post_type=update_log',
			__( 'Settings', 'sitemanager' ),
			__( 'Settings', 'sitemanager' ),
			'manage_options',
			'site-manager',
			array( $this, 'option_page' ),
			'dashicons-update'
		);
	}

	public function register_scripts() {
		wp_register_style( 'sitemanager_settings', plugins_url( 'css/settings.css', dirname( __FILE__ ) ), array(), Site_Manager::$version );
		wp_register_script( 'sitemanager_admin', plugins_url( 'js/admin.js', dirname( __FILE__ ) ), array('jquery'), Site_Manager::$version );
	}

	public function register_settings( $screen ) {
		if ( 'update_log_page_site-manager' != $screen->base && 'options' != $screen->base ) {
			return;
		}

		register_setting( 'site-manager-group', 'site-manager', array( $this, 'sanitize_checkboxes' ) );

		if ( Site_Manager_Update::can_auto_update() || Site_Manager_Update::can_auto_update('theme') || Site_Manager_Update::can_auto_update('plugin') || Site_Manager_Update::can_auto_update('translation') ) {
			add_settings_section( 'site-manager-update', '', array( $this, 'setting_update_description' ), 'site-manager' );

			$updates = get_option( 'site-manager', array() );

			if ( Site_Manager_Update::can_auto_update() ) {
				add_settings_field( 'site-manager-update-major', __( 'Major updates', 'site-manager' ), array( $this, 'switch_on_off' ), 'site-manager', 'site-manager-update', array( 'type' => 'major', 'value' => $updates['major'], 'description' => __( 'Major updates add features, improvements and awesomeness.', 'sitemanager' ) ) );
			}

			if ( Site_Manager_Update::can_auto_update('theme') ) {
				add_settings_field( 'site-manager-update-theme', __( 'Theme updates', 'site-manager' ), array( $this, 'switch_on_off' ), 'site-manager', 'site-manager-update', array( 'type' => 'themes', 'value' => $updates['themes'], 'description' => __( 'Note: Automatic theme updates will <strong>overwrite your theme customizations</strong>.', 'sitemanager' ) ) );
			}

			if ( Site_Manager_Update::can_auto_update('plugin') ) {
				add_settings_field( 'site-manager-update-plugin', __( 'Plugin updates', 'site-manager' ), array( $this, 'switch_on_off' ), 'site-manager', 'site-manager-update', array( 'type' => 'plugins', 'value' => $updates['plugins'] ) );
			}
		}
	}


	public static function default_option_values() {
		return array(
			'minor'        => 'on',
			'major'        => 'off',
			'themes'       => 'off',
			'plugins'      => 'off',
			'translations' => 'on',
		);
	}

	public function option_page() {
		global $wp_settings_fields;

		echo '<div class="wrap">';
		echo '<h2>' . get_admin_page_title() . '</h2>';

		echo '<form action="options.php" method="post">';
		settings_fields( 'site-manager-group' );
		do_settings_sections( 'site-manager' );


		$plugin_slug = 'background-update-tester';
		$plugin_file = 'background-update-tester/background-update-tester.php';

		if ( ! isset( $wp_settings_fields['site-manager']['site-manager-update'] ) ) {
			echo '<p><strong>';

			$vcs_dir = $this->is_vcs_checkout();

			if ( $vcs_dir ) {
				printf(
					__( 'Your installation appears to be under version control (%s). This prevents the plugin from managing your updates.' , 'background-update-tester' ),
					'<code>' . ABSPATH . '</code>',
					'<code>' . $vcs_dir . '</code>'
				);
			}
			else if( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
				$url = wp_nonce_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug, 'install-plugin_' . $plugin_slug );

				printf(
					__( 'Your installation is currently blocking automatic updates. Install %s to find the cause.', 'sitemanager' ),
					'<a href="' . $url . '">Automatic Update Tester</a>'
				);
			}
			else if( ! is_plugin_active( $plugin_file ) ) {
				$url = wp_nonce_url( 'plugins.php?action=activate&plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file );

				printf(
					__( "Your installation is currently blocking automatic updates. Activate %s and go to 'Update Tester' under 'Dashboard' to find the cause.", 'sitemanager' ),
					'<a href="' . $url . '">Automatic Update Tester</a>'
				);
			}
			else {
				printf(
					__( 'Your installation is currently blocking automatic updates. Check %s for more details.', 'sitemanager' ),
					'<a href="' . admin_url( 'index.php?page=background-updates-debugger' ) . '">Automatic Update Tester</a>'
				);
			}

			echo '</strong></p>';
		}


		echo '<p class="submit">';
		submit_button( __( 'Save Changes', 'sitemanager' ), 'primary', 'submit', false );
		echo ' &nbsp; ';
		submit_button( __( 'Use Default Settings', 'sitemanager' ), 'secondary', 'reset', false );
		echo '</p>';

		echo '</form>';

		echo '</div>';


		$this->set_labels_and_colors();

		wp_enqueue_style( 'sitemanager_settings' );
		wp_enqueue_script( 'sitemanager_admin' );
	}

	private function set_labels_and_colors() {
		global $_wp_admin_css_colors;

		$color = get_user_option('admin_color');

		if ( empty( $color ) || ! isset($_wp_admin_css_colors[ $color ] ) ) {
			$color = 'fresh';
		}

		echo '<style>';
		echo '.onoffswitch-inner:before { content: "' . __( 'On', 'sitemanager' ) . '"; }';
		echo '.onoffswitch-inner:after { content: "' . __( 'Off', 'sitemanager' ) . '"; }';
		echo '.form-table .onoffswitch-label { border-color: ' . $_wp_admin_css_colors[$color]->colors[2] . '; }';
		echo '.form-table .onoffswitch-inner:before { background-color: ' . $_wp_admin_css_colors[$color]->colors[3] . '; }';
		echo '.form-table .onoffswitch-switch { background-color: ' . $_wp_admin_css_colors[$color]->colors[2] . '; }';
		echo '</style>';
	}


	public function setting_update_description() {
		_e( 'Manage your update preferences.', 'sitemanager' );
	}

	public function sanitize_checkboxes( $values ) {
		$new_values = $this->default_option_values();

		if ( isset( $_POST['reset'] ) ) {
			return $new_values;
		}

		foreach ( $new_values as $key => $value ) {
			if ( ! isset( $values[ $key ] ) ) {
				$new_values[ $key ] = 'off';
			}
			else {
				$new_values[ $key ] = 'on';
			}
		}

		if ( 'on' == $new_values['major'] ) {
			$new_values['minor'] = 'on';
		}

		return $new_values;
	}


	public function switch_on_off( $args ) {
		if ( ! isset( $args['type'] ) ) {
			return;
		}

		if ( ! isset( $args['value'] ) || 'off' != $args['value'] ) {
			$args['value'] = 'on';
		}

		if ( ! isset( $args['name'] ) ) {
			$args['name'] = 'site-manager';
		}

		?>

		<div class="onoffswitch">
			<input type="checkbox" name="<?php echo $args['name']; ?>[<?php echo $args['type']; ?>]" value="on" class="onoffswitch-checkbox" id="switch-<?php echo $args['type']; ?>" <?php checked( 'on', $args['value'] ); ?>>
			<label class="onoffswitch-label" for="switch-<?php echo $args['type']; ?>">
				<div class="onoffswitch-inner"></div>
				<div class="onoffswitch-switch"></div>
			</label>
		</div>

		<?php

		if ( isset( $args['description'] ) ) {
			echo '<p class="description">' . $args['description'] . '</p>';
		}
	}


	private function is_vcs_checkout() {
		$context_dirs = array( ABSPATH );
		$vcs_dirs     = array( '.svn', '.git', '.hg', '.bzr' );
		$check_dirs   = array();

		foreach ( $context_dirs as $context_dir ) {
			// Walk up from $context_dir to the root.
			do {
				$check_dirs[] = $context_dir;

				// Once we've hit '/' or 'C:\', we need to stop. dirname will keep returning the input here.
				if ( $context_dir == dirname( $context_dir ) ) {
					break;
				}

			// Continue one level at a time.
			} while ( $context_dir = dirname( $context_dir ) );
		}

		$check_dirs = array_unique( $check_dirs );

		// Search all directories we've found for evidence of version control.
		foreach ( $vcs_dirs as $vcs_dir ) {
			foreach ( $check_dirs as $check_dir ) {
				if ( $checkout = @is_dir( rtrim( $check_dir, '\\/' ) . "/$vcs_dir" ) ) {
					break 2;
				}
			}
		}

		if ( $checkout && apply_filters( 'automatic_updates_is_vcs_checkout', true, ABSPATH ) ) {
			return $vcs_dir;
		}

		return false;
	}

}