<?php

if ( ! defined('ABSPATH') ) {
	die();
}

class Site_Manager_Changelog {

	/**
	 * Custom post type.
	 *
	 * @var string
	 */
	const post_type = 'update_log';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );

		add_action( 'admin_menu', array( $this, 'remove_add_new_from_menu' ), 999 );
		add_action( 'current_screen', array( $this, 'enqueue_scripts' ), 20 );

		add_action( 'manage_' . self::post_type . '_posts_custom_column', array( $this, 'action_manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_' . self::post_type . '_posts_columns', array( $this, 'filter_manage_post_type_posts_columns' ) );

		add_action( 'bulk_actions-edit-' . self::post_type, array( $this, 'filter_bulk_actions' ) );
		add_action( 'post_row_actions', array( $this, 'filter_row_actions' ), 10, 2 );

		foreach ( array( 'edit.php', 'post.php', 'post-new.php' ) as $item ) {
			add_action( "load-{$item}", array( $this, 'action_load_edit_php' ) );
		}
	}


	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Changelog', 'sitemanager' ),
			'singular_name'      => __( 'Site Manager', 'sitemanager' ),
			'menu_name'          => __( 'Site Manager', 'sitemanager' ),
			'all_items'          => __( 'Changelog', 'sitemanager' ),
			'search_items'       => __( 'Search', 'sitemanager' ),
			'not_found'          => __( 'No changelog available. Please wait for the first update to complete.', 'sitemanager' ),
		);

		$args = array(
			'labels'            => $labels,
			'show_ui'           => true,
			'public'            => false,
			'show_in_admin_bar' => false,
			'capabilities' => array(
				'edit_post'          => 'do_not_allow',
				'edit_posts'         => 'activate_plugins',
				'edit_others_posts'  => 'activate_plugins',
				'publish_posts'      => 'do_not_allow',
				'read_post'          => 'activate_plugins',
				'read_private_posts' => 'do_not_allow',
				'delete_post'        => 'activate_plugins',
			),
			'rewrite'       => false,
			'query_var'     => false,
			'menu_icon'     => 'dashicons-update',
			'menu_position' => 82
		);
		register_post_type( self::post_type, $args );
	}

	public function register_taxonomy() {
		$args = array(
			'public'            => false,
			'hierarchical'      => false,
			'label'             => 'Category',
			'rewrite'           => false
		);
		register_taxonomy( 'update_log_category', array( self::post_type ), $args );

		$args = array(
			'public'            => false,
			'hierarchical'      => false,
			'label'             => 'Method',
			'rewrite'           => false
		);
		register_taxonomy( 'update_log_method', array( self::post_type ), $args );
	}


	public function remove_add_new_from_menu() {
		remove_submenu_page( 'edit.php?post_type=update_log', 'post-new.php?post_type=update_log' );
	}

	public function enqueue_scripts( $screen ) {
		if ( 'edit-' . self::post_type != $screen->id ) {
			return;
		}

		wp_enqueue_style( 'sitemanager_settings' );
		wp_enqueue_script( 'sitemanager_admin' );
	}

	/**
	 * Attached to manage_posts_custom_column action.
	 *
	 */
	public function action_manage_posts_custom_column( $column_name, $post_id ) {
		global $mode;

		switch ( $column_name ) {
			case 'update_log_version' :
				echo get_post_meta( $post_id, '_version', true );
				break;
			case 'update_log_changelog' :
				$post    = get_post( $post_id );
				$content = nl2br( preg_replace( '/<!--more(.*?)?-->(.*)/s', '', $post->post_content ) );

				if( 'excerpt' == $mode ) {
					echo $content;
				}
				else {
					echo '<div class="changelog-show-first">' . $content . '</div>';
				}
				break;
			case 'update_log_version_previous' :
				$previous = get_post_meta( $post_id, '_version_previous', true );

				if( $previous ) {
					echo $previous;
				}
				else {
					echo '&#8722;';
				} 
				break;
			case 'update_log_category' :
				echo $this->list_terms( $post_id, 'update_log_category' );
				break;
			case 'update_log_method' :
				echo $this->list_terms( $post_id, 'update_log_method' );
				break;
		}
	}


	/**
	 * Changes the columns for the table
	 *
	 * Attached to manage_{post_type}_posts_columns filter.
	 */
	public function filter_manage_post_type_posts_columns( $columns ) {
		$columns = array(
			'title'                       => __( 'Title' ),
			'update_log_changelog'        => __( 'Description', 'sitemanager' ),
			'update_log_version'          => __( 'Version', 'sitemanager' ),
			'update_log_version_previous' => __( 'Previous version', 'sitemanager' ),
			'update_log_category'         => __( 'Category', 'sitemanager' ),
			'update_log_method'           => __( 'Method', 'sitemanager' ),
			'date'                        => __( 'Changed on', 'sitemanager' ),
		);

		return $columns;
	}

	/**
	 * Modifies bulk actions.
	 */
	public function filter_bulk_actions( $actions ) {
		unset( $actions['edit'] );

		return array();
	}

	/**
	 * Modifies row actions.
	 */
	public function filter_row_actions( $actions, $post ) {
		if ( self::post_type == $post->post_type ) {
			$actions = array();
		}

		return $actions;
	}



	public function action_load_edit_php() {
		$screen = get_current_screen();

		if ( self::post_type == $screen->id && ( $screen->action == 'add' || $_GET['action'] == 'edit' ) ) {
			wp_die( __( 'Invalid post type.' ) );
		}

		if ( self::post_type != $screen->post_type ) {
			return;
		}

		if ( ( empty( $_GET['post_status'] ) || 'all' == $_GET['post_status'] ) && ( isset( $_GET['delete_all'] ) || isset( $_GET['delete_all2'] ) ) ) {
			$_GET['post_status'] = $_REQUEST['post_status'] = 'publish';
		}

		global $wp_post_statuses;
		// You didn't see this.
		$wp_post_statuses['publish']->show_in_admin_status_list = false;
	}

	private function list_terms( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		$translations = array(
			'Update'           => __( 'Manual update', 'sitemanager' ),
			'Automatic update' => __( 'Automatic update', 'sitemanager' ),
			'Plugin'           => __( 'Plugin', 'sitemanager' ),
			'Theme'            => __( 'Theme', 'sitemanager' ),
		);
						
		if ( $terms && ! is_wp_error( $terms ) ) {
			$names = wp_list_pluck( $terms, 'name' );

			foreach ( $names as &$name ) {
				if ( isset( $translations[ $name ] ) ) {
					$name = $translations[ $name ];
				}
			}

			return join( ", ", $names );
		}
	}

}