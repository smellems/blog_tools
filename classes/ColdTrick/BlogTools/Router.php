<?php

namespace ColdTrick\BlogTools;

/**
 * Router handling
 *
 * @package    ColdTrick
 * @subpackage BlogTools
 */
class Router {
	
	/**
	 * Listen to the blog page handler, to takeover some pages
	 *
	 * @param string $hook         'route'
	 * @param string $type         'blog'
	 * @param array  $return_value the current page_handler params
	 * @param null   $params       null
	 *
	 * @return void|false
	 */
	public static function blog($hook, $type, $return_value, $params) {
		
		$page = elgg_extract('segments', $return_value);
		if (empty($page)) {
			return;
		}
		
		$include_file = false;
		$pages_path = elgg_get_plugins_path() . 'blog_tools/pages/';
		
		switch ($page[0]) {
			case 'owner':
				$user = get_user_by_username($page[1]);
				if (!empty($user)) {
					// push all blogs breadcrumb
					elgg_push_breadcrumb(elgg_echo('blog:blogs'), 'blog/all');
					
					set_input('owner_guid', $user->guid);
					$include_file = $pages_path . 'owner.php';
				}
				break;
			case 'read': // Elgg 1.7 compatibility
			case 'view':
				if (!elgg_is_logged_in()) {
					$setting = elgg_get_plugin_setting('advanced_gatekeeper', 'blog_tools');
					if ($setting != 'no') {
						if (isset($page[1]) && !get_entity($page[1])) {
							elgg_gatekeeper();
						}
					}
				}
				
				set_input('guid', $page[1]); // to be used in the blog_tools/full/related view
				break;
			case 'add':
			case 'edit':
				// push all blogs breadcrumb
				elgg_push_breadcrumb(elgg_echo('blog:blogs'), 'blog/all');
				
				set_input('page_type', $page[0]);
				if (isset($page[1])) {
					set_input('guid', $page[1]);
				}
				if (isset($page[2])) {
					set_input('revision', $page[2]);
				}
				
				$include_file = $pages_path . 'edit.php';
				break;
			case 'featured':
				
				$include_file = $pages_path . 'featured.php';
				break;
		}
		
		if (!empty($include_file)) {
			include($include_file);
			return false;
		}
	}
}