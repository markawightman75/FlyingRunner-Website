<?php 

	class phimind_plugin_manager_0_1
	{
		var $version = 0.1;
		var $params;
		var $layout = 'default';
		var $view_vars;

		/*PLUGIN CONFIGURATION VALUES*/
		var $plugin_root;
		var $plugin_root_web;
		var $plugin_menu_name;
		var $plugin_page_name;
		var $plugin_index_class_name;

		function __construct($_PHIMIND_CURRENT_CONFIG_VARS)
		{

			foreach ($_PHIMIND_CURRENT_CONFIG_VARS as $var_key => $var_value)
				$this->$var_key = $var_value;

			//ADD ALL AJAX CALLS
			$class_methods = get_class_methods($this);
			foreach ($class_methods as $method_name) 
				if (substr($method_name, 0, 6) == 'ajax__')
					add_action('wp_ajax_'.$method_name, array($this, $method_name));
		}

		function __destruct()
		{
		}

		function _copy_parent_vars($parent_obj)
		{
			//GET PARAMS FROM PARENT TO USE IN DIRECT INSTANCING
			$this->plugin_root = $parent_obj->plugin_root;
			$this->plugin_root_web = $parent_obj->plugin_root_web;
			$this->plugin_menu_name = $parent_obj->plugin_menu_name;
			$this->plugin_page_name = $parent_obj->plugin_page_name;
		}


		function init_configuration()
		{
			//QUEUE THE JS/CSS FOR THIS PLUGIN IF THE PAGE BEING DISPLAYED IS FOR THE PLUGIN
			//THIS AVOIDS ANY KIND OF CONFLICT WITH CSS THAT IS NOT WELL WRITTEN
			if ($_GET['page'] == $this->plugin_page_name)
				add_action('admin_enqueue_scripts', array($this, 'setup_scripts'));

			//ACTIVATE AND DEACTIVATE HOOKS
			register_activation_hook($this->plugin_root.'/phimind_excel_export_plus.php', array($this, 'activate'));
			register_deactivation_hook($this->plugin_root.'/phimind_excel_export_plus.php', array($this, 'deactivate'));
			//ACTIVATE AND DEACTIVATE HOOKS

			//GENERATE THE MENU
			add_action('admin_menu', array($this, 'configure_main_menu'));
			//GENERATE THE BASE MENU

			//SETUP THE DASHBOARD WIDGET
			add_action('wp_dashboard_setup', array($this, 'configure_dashboard_widgets'));
			//SETUP THE DASHBOARD WIDGET
		}

		function configure_dashboard_widgets() 
		{
			wp_add_dashboard_widget('wp_phimind_dashboard_widget', 'PhiMind', array($this, 'dashboard_widget_news'));
		} 

		function dashboard_widget_news() 
		{
			$url = 'http://support.phimind.com/projects/welcome_wordpress/';
			$file_location = download_url($url);
			echo file_get_contents($file_location);
			unlink($file_location);
		}

		function configure_main_menu()
		{
			if (!$this->check_if_menu_exists('phimind'))
				add_menu_page('PhiMind', 'PhiMind', 'edit_plugins', 'phimind', array($this, 'menu_main_page'));

			$index_class = new $this->plugin_index_class_name();
			add_submenu_page('phimind', $this->plugin_menu_name, $this->plugin_menu_name, 'edit_plugins', $this->plugin_page_name, array($index_class, 'index'));
		}

		function check_if_menu_exists($slug)
		{
			global $menu;
			$flag_exists = 0;
			foreach($menu as $menu_item)
			{
				if ($menu_item[2] == $slug)
				{
					$flag_exists = 1;
					break;
				}
			}
			return $flag_exists;
		}

		function menu_main_page()
		{
//			echo 'This is the main page for the MENU PHIMIND';
			$url = 'http://support.phimind.com/projects/welcome_to_admin/host:'.urlencode($_SERVER["HTTP_HOST"]);
			$file_location = download_url($url);
			echo file_get_contents($file_location);
		}

		function setup_scripts()
		{
			wp_register_script('bootstrap_js', $this->plugin_root_web.'/assets/css/bootstrap/js/bootstrap.min.js', array('jquery'), '1.0', true);
			wp_enqueue_script('bootstrap_js');

			wp_register_style('bootstrap_css', $this->plugin_root_web.'/assets/css/bootstrap/css/bootstrap.min.css', array(), '1.0', 'all');
			wp_enqueue_style('bootstrap_css');

			wp_register_script('global_js', $this->plugin_root_web.'/assets/js/global.js', array('jquery'), '1.0', true);
			wp_enqueue_script('global_js');

			wp_register_style('global_css', $this->plugin_root_web.'/assets/css/global.css', array(), '1.0', 'all');
			wp_enqueue_style('global_css');
		}

		function activate()
		{
		}

		function deactivate()
		{
		}

		function redirect($url)
		{
			header('Location:'.$url);
			die;
		}

		function set($var_name, $var_value)
		{
			$this->view_vars[$var_name] = $var_value;
		}

		function render($template, $render_to_variable = false)
		{
			require('config.php');

			foreach($this->view_vars as $view_var_name => $view_var_value)
				${$view_var_name} = $view_var_value;

			$render_output_layout = '';
			$render_output_view = '';

			//RENDER THE LAYOUT FIRST
			if (!empty($this->layout))
			{
				ob_start();
				include($this->plugin_root.DS.'views'.DS.'layouts'.DS.$this->layout.'.php');
				$render_output_layout = ob_get_contents();
				ob_end_clean();
			}

			//RENDER THE VIEW

			if ($render_to_variable)
				ob_start();

			if (empty($this->layout))
			{
				include_once($this->plugin_root.DS.'views'.DS.$template.'.php');
			} else {
				$contents_before = substr($render_output_layout, 0, strpos($render_output_layout, '{content_block'));
				$contents_after = substr($render_output_layout, strpos($render_output_layout, '}') + 1);

				echo $contents_before;
				include_once($this->plugin_root.DS.'views'.DS.$template.'.php');
				echo $contents_after;
			}

			if ($render_to_variable)
			{
				$render_var = ob_get_contents();
				ob_end_clean();
				return $render_var;
			}
		}

	}

?>