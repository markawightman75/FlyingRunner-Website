<?php

if ( ! defined('ABSPATH') ) {
	die();
}

class Site_Manager_Logger {

	/**
	 * Caches the readme file when used multiple times
	 *
	 * @var array
	 */
	private static $readme_cache = array();

	/**
	 * Since WordPress doesn't return the version numbers we need to retrieve them before updating
	 *
	 * @var array
	 */
	private static $versions_cache = array();

	public function __construct() {
		add_filter( 'pre_update_option_auto_updater.lock', array( $this, 'cache_current_versions' ) );
		add_filter( 'one_and_one_batch_installer_start', array( $this, 'cache_current_versions' ) );

		add_action( 'current_screen', array( $this, 'current_screen_cache_current_versions' ), 20 );

		add_action( 'automatic_updates_complete', array( $this, 'log_automatic_updates' ) );
		add_action( 'upgrader_process_complete', array( $this, 'log_upgrader_complete' ), 10, 2 );
	}


	public function cache_current_versions( $value ) {
		global $wp_version;

		if ( ! self::$versions_cache ) {
			self::$versions_cache['core']    = $wp_version;
			self::$versions_cache['plugins'] = get_plugins();
			self::$versions_cache['themes']  = array();

			// Different logic due reference.
			foreach ( wp_get_themes() as $stylesheet => $theme_data ) {
				self::$versions_cache['themes'][ $stylesheet ] = $theme_data->get( 'Version' );
			}
		}

		return $value;
	}

	public function current_screen_cache_current_versions( $screen ) {
		if ( 'update' == $screen->id ) {
			$actions = array(
				'install-theme',
				'upgrade-theme',
				'install-plugin',
				'upgrade-plugin',
			);

			if ( in_array( $_GET['action'], $actions ) ) {
				$this->cache_current_versions('');
			}
		}
	}



	public function log_automatic_updates( $update_result ) {
		foreach ( $update_result as $type => $items ) {
			foreach ( $items as $item ) {
				if ( $item->result && ! is_wp_error( $item->result ) ) {
					$post_id = $this->log_update( $type, $item->item, $item->name );

					if ( $post_id && ! is_wp_error( $post_id ) ) {
						update_post_meta( $post_id, '_messages', $item->messages );

						wp_set_object_terms( $post_id, 'Automatic update', 'update_log_method' );
					}

					do_action( 'automatic_update_manager_did_update', $post_id );
				}
			}
		}
	}

	public function log_upgrader_complete( $upgrader, $info ) {
		// If auto updating then skipp this step
		if ( Site_Manager_Update::is_auto_updating() ) {
			return;
		}

		// Skip if type isn't set
		if ( ! isset( $info['type'] ) ) {
			return;
		}

		$type = $info['type'];

		if ( ! in_array( $type, array( 'plugin', 'theme' ) ) ) {
			return;
		}

		if ( isset( $info['action'] ) ) {
			$action = ucfirst( $info['action'] );
		}
		else {
			$action = 'Update';
		}

		// This should never be needed. In this case you are already to late. Implement a doing it wrong filter here.
		$this->cache_current_versions('');


		if ( 'plugin' == $info['type'] ) {
			if ( isset( $info['bulk'] ) && true == $info['bulk'] ) {
				$slugs = $info['plugins'];
			}
			else {
				if ( isset( $upgrader->skin->plugin ) && $upgrader->skin->plugin ) {
					$slugs = array( $upgrader->skin->plugin );
				}
				else if ( isset( $upgrader->skin->api ) ) {
					$file  = $this->get_main_plugin_file( WP_PLUGIN_DIR . '/' . $upgrader->skin->api->slug );
					$slugs = array( $upgrader->skin->api->slug . '/' . $file );
				}
				else {
					return;
				}
			}

			foreach ( $slugs as $slug ) {
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $slug );

				if ( ! isset( self::$versions_cache['plugins'][ $slug ] ) || self::$versions_cache['plugins'][ $slug ]['Version'] != $plugin_data['Version'] ) {
					$post_id = $this->log_update( $type, $slug, $plugin_data['Name'] );

					wp_set_object_terms( $post_id, $info['action'], 'update_log_method' );
				}
			}
		}
		else if ( 'theme' == $info['type'] ) {
			if ( isset( $info['bulk'] ) && true == $info['bulk'] ) {
				$slugs = $info['themes'];
			}
			else if ( isset( $upgrader->skin->theme ) && $upgrader->skin->theme ) {
				$slugs = array( $upgrader->skin->theme );
			}
			else {
				return;
			}

			foreach ( $slugs as $slug ) {
				$theme_data = wp_get_theme( $slug );

				if ( 'Install' != $action ) {
					// Old information will still be here
					$theme_data->cache_delete();
				}

				if ( ! isset( self::$versions_cache['themes'][ $slug ] ) || self::$versions_cache['themes'][ $slug ] != $theme_data->get( 'Version' ) ) {
					$post_id = $this->log_update( $type, $slug, $theme_data->get( 'Name' ) );

					wp_set_object_terms( $post_id, $info['action'], 'update_log_method' );
				}
			}
		}
	}


	/**
	 * Helper methods
	 */

	private function log_update( $type, $file, $name ) {
		$args = array(
			'post_title'  => $name,
			'post_status' => 'publish',
			'post_type'   => Site_Manager_Changelog::post_type,
		);

		if ( 'plugin' == $type ) {
			$readme_file = dirname( WP_PLUGIN_DIR . '/' . $file ) . '/readme.txt';

			if ( isset( self::$versions_cache['plugins'][ $file ] ) ) {
				$args['post_content'] = $this->get_changes_since_last_update( $readme_file, self::$versions_cache['plugins'][ $file ]['Version'] );
			}
			else {
				$args['post_content'] = $this->get_changes_since_last_update( $readme_file, '' );
			}
		}

		$post_id = wp_insert_post( $args );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( 'plugin' == $type ) {
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file, false, false );

				update_post_meta( $post_id, '_version', $plugin_data['Version'] );
				update_post_meta( $post_id, '_is_active', is_plugin_active( $file ) );

				if ( isset( self::$versions_cache['plugins'][ $file ] ) ) {
					update_post_meta( $post_id, '_version_previous', self::$versions_cache['plugins'][ $file ]['Version'] );
				}
			}
			else if( 'theme' == $type ) {
				$theme = wp_get_theme( $file );

				update_post_meta( $post_id, '_version', $theme->get( 'Version' ) );
				update_post_meta( $post_id, '_is_active', ( get_option( 'template' ) == $file ) );

				if ( isset( self::$versions_cache['themes'][ $file ] ) ) {
					update_post_meta( $post_id, '_version_previous', self::$versions_cache['themes'][ $file ] );
				}
			}
			else if( 'core' == $type ) {

			}

			wp_set_object_terms( $post_id, ucfirst( $type ), 'update_log_category' );
		}

		return $post_id;
	}



	private function get_main_plugin_file( $dir ) {
		$plugins_dir = @opendir( $dir );
		$plugin_files = array();

		if ( $plugins_dir ) {
			while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
				if ( '.' === substr( $file, 0, 1 ) ) {
					continue;
				}

				if ( '.php' === substr( $file, -4 ) ) {
					$plugin_files[] = $file;
				}
			}

			closedir( $plugins_dir );
		}

		if ( empty( $plugin_files ) ) {
			return false;
		}

		$main_file = false;
		foreach ( $plugin_files as $plugin_file ) {
			if ( ! is_readable( "$dir/$plugin_file" ) ) {
				continue;
			}

			$source = $this->get_first_lines( "$dir/$plugin_file", 30 );

			// Stop when we find a file with a plugin name header in it.
			if ( $this->get_addon_header( 'Plugin Name', $source ) != false ) {
				$main_file = $plugin_file;
				break;
			}
		}

		return $main_file;
	}

	private function get_first_lines( $filename, $lines = 30 ) {
		$extf = fopen( $filename, 'r' );

		if ( ! $extf ) {
			return false;
		}

		$first_lines = '';

		foreach ( range( 1, $lines ) as $x ) {
			$line = fgets( $extf );

			if ( feof( $extf ) ) {
				break;
			}

			if ( false === $line ) {
				return false;
			}

			$first_lines .= $line;
		}

		return $first_lines;
	}

	public function get_addon_header( $header, &$source ) {
		if ( preg_match( '|' . $header . ':(.*)$|mi', $source, $matches ) ) {
			return trim( $matches[1] );
		}
		else {
			return false;
		}
	}


	private function get_changes_since_last_update( $file, $old_version ) {
		$changelog = $this->retrieve_changelog( $file );
		$updates   = '';
		$did_first = false;

		foreach ( $changelog as $version => $content ) {
			$has_first = false;

			if( $old_version && strpos( $version, $old_version ) === 0 ) {
				$did_first = $has_first = true;
			}
			else if ( $did_first ) {
				break;
			}

			$updates .= '<div class="update">';
			$updates .= '<strong>' . $version . '</strong><br/>';
			$updates .= wpautop( $content );
			$updates .= '</div>';

			if( $version == $old_version ) {
				break;
			}
		}

		return trim( $updates );
	}

	private function retrieve_changelog( $file ) {
		$readme = $this->get_readme( $file );

		if( isset( $readme['changelog'] ) ) {
			$_changelog = preg_split( '/^[\s]*=[\s]*(.+?)[\s]*=/m', $readme['changelog'], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY );
			$changelog  = array();

			if( count( $_changelog ) > 1 ) {
				for ( $i = 1; $i <= count( $_changelog ); $i += 2 ) {
					$title = $this->sanitize_text( $_changelog[ $i - 1 ] );
					$title = explode(' ',trim( $title ) );

					$changelog[ $title[0] ] = $_changelog[ $i ];
				}

				return $changelog;
			}
		}

		return array();
	}

	private function get_readme( $file ) {
		if( ! isset( self::$readme_cache[ $file ] ) ) {
			if ( ! is_file( $file ) ) {
				return false;
			}

			$file_contents = implode( '', file( $file ) );

			$file_contents = str_replace( array( "\r\n", "\r" ), "\n", $file_contents );
			$file_contents = trim($file_contents);

			if ( 0 === strpos( $file_contents, "\xEF\xBB\xBF" ) ) {
				$file_contents = substr( $file_contents, 3 );
			}

			$_sections = preg_split( '/^[\s]*==[\s]*(.+?)[\s]*==/m', $file_contents, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY );

			$sections = array();
			for ( $i = 1; $i <= count( $_sections ); $i += 2 ) {
				$title = $this->sanitize_text( $_sections[ $i - 1 ] );
				$sections[ str_replace(' ', '_', strtolower( $title ) ) ] = $_sections[ $i ];
			}

			self::$readme_cache[ $file ] = $sections;
		}

		return self::$readme_cache[ $file ];
	}

	private function sanitize_text( $text ) {
		$text = strip_tags( $text );
		$text = trim( $text );

		return $text;
	}

}