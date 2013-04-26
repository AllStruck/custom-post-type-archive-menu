<?php
/*
Plugin Name: Custom Post Type Archive Menu
Plugin URI: http://wpcptam.allstruck.com/
Description: This plugin makes it easy to add a new menu items for custom post type archives, and also fixes the bug which shows the Blog menu item as the parent when on any custom post type page or archive.
Author: AllStruck
Version: 1.0
Author URI: http://allstruck.com/
License: GPL v3
Text Domain: allstruckwpcptam
Domain Path: /languages

Add Custom Post Types Archive to Nav Menus
Copyright (C) 2013, AllStruck - wpcptam@allstruck.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1. 403 Forbidden');
	exit();
}

if( !class_exists('CustomPostTypeArchiveInNavMenu') ) {
	class CustomPostTypeArchiveInNavMenu {

		function __init() {
			add_action( 'init', array($this, 'updater_init') );
		}
		
		function CustomPostTypeArchiveInNavMenu() {
			load_plugin_textdomain( 'allstruckwpcptam', false, basename( dirname( __FILE__ ) ) . '/languages' );
			add_action( 'admin_head-nav-menus.php', array( &$this, 'cpt_navmenu_metabox' ) );
			add_filter( 'wp_get_nav_menu_items', array( &$this,'cpt_archive_menu_filter'), 50, 3 );
			add_filter( 'nav_menu_css_class', array( &$this,'blog_class_menu_filter'), 50, 3 );
		}
		
		function cpt_navmenu_metabox() {
	    	add_meta_box( 'add-cpt', __('Custom Post Type Archives', 'andromedamedia'), array( &$this, 'cpt_navmenu_metabox_content' ), 'nav-menus', 'side', 'default' );
	  	}
		
		function cpt_navmenu_metabox_content() {
	    	$post_types = get_post_types( array( 'show_in_nav_menus' => true, 'has_archive' => true ), 'object' );
			
			if( $post_types ) :
				foreach ( $post_types as &$post_type ) {
			        $post_type->classes = array();
			        $post_type->type = $post_type->name;
			        $post_type->object_id = $post_type->name;
			        $post_type->title = $post_type->labels->name;
			        $post_type->object = 'cpt-archive';
			        $post_type->menu_item_parent = '';
			        $post_type->url = '';
			        $post_type->target = '';
			        $post_type->attr_title = '';
			        $post_type->xfn = '';
			        $post_type->db_id = '';

			    }
				$walker = new Walker_Nav_Menu_Checklist( array() );
		
				echo '<div id="cpt-archive" class="posttypediv">';
				echo '<div id="tabs-panel-cpt-archive" class="tabs-panel tabs-panel-active">';
				echo '<ul id="ctp-archive-checklist" class="categorychecklist form-no-clear">';
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $post_types), 0, (object) array( 'walker' => $walker) );
				echo '</ul>';
				echo '</div><!-- /.tabs-panel -->';
				echo '</div>';
				echo '<p class="button-controls">';
				echo '<span class="add-to-menu">';
				//echo '<img class="waiting" src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" alt="" />';
				echo '<input type="submit" class="button-secondary submit-add-to-menu" value="' . __('Add to Menu', 'andromedamedia') . '" name="add-ctp-archive-menu-item" id="submit-cpt-archive" />';
				echo '</span>';
				echo '</p>';
				
			endif;
		}
		
		function blog_class_menu_filter( $classes ) {
			if (is_array($classes)) {
				global  $post;
				if (in_array('menu-item-type-post_type', $classes) && (get_post_type($post) != 'post' || is_author())) {
					foreach ($classes as $key => $value) {
						if ($value == 'current_page_parent') {
							$classes[$key] = '';
						}
					}
				}
				return $classes;
			}
		}
		function cpt_archive_menu_filter( $items, $menu, $args ) {
	    	foreach( $items as &$item ) {
	      		if( $item->object != 'cpt-archive' ) continue;

	      		$item->url = get_post_type_archive_link( $item->type );
	      		
	      		if( get_query_var( 'post_type' ) == $item->type ) {
	       			$item->classes[] = 'current-menu-item';
	        		$item->current = true;
	      		}
	    	}
	    	
	    	return $items;
		}

		# Auto updates from GitHub Repo using updater.php script by jkudish.
		function updater_init() {

			include_once 'updater.php';

			define( 'WP_GITHUB_FORCE_UPDATE', true );

			if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

				$config = array(
					'slug' => plugin_basename( __FILE__ ),
					'proper_folder_name' => 'custom-post-type-archive-menu',
					'api_url' => 'https://api.github.com/repos/allstruck/https://github.com/AllStruck/custom-post-type-archive-menu.git',
					'raw_url' => 'https://raw.github.com/allstruck/https://github.com/AllStruck/custom-post-type-archive-menu.git/master',
					'github_url' => 'https://github.com/allstruck/https://github.com/AllStruck/custom-post-type-archive-menu.git',
					'zip_url' => 'https://github.com/allstruck/https://github.com/AllStruck/custom-post-type-archive-menu.git/zipball/master',
					'sslverify' => true,
					'requires' => '3.0',
					'tested' => '3.3',
					'readme' => 'README.md',
					'access_token' => '',
				);

				new WP_GitHub_Updater( $config );

			}

		}


	}


	/* Instantiate the plugin */
	$CustomPostTypeArchiveInNavMenu = new CustomPostTypeArchiveInNavMenu();
}

