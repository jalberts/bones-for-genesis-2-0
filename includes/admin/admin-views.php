<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Only show the admin bar to users who can at least use Posts
 *
 * @since 2.0.0
 */
add_filter( 'show_admin_bar' , 'prc_maybe_hide_admin_bar', 99 );
function prc_maybe_hide_admin_bar( $default ) {

	return current_user_can( 'edit_posts' ) ? $default : false;

}

add_action( 'admin_menu', 'prc_remove_dashboard_widgets' );
/**
 * Disable some or all of the default admin dashboard widgets.
 *
 * See: http://digwp.com/2010/10/customize-wordpress-dashboard/
 *
 * @since 1.x
 */
function prc_remove_dashboard_widgets() {

	// remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );				// Right Now
	// remove_meta_box( 'dashboard_activity', 'dashboard', 'core' );				// Activity
	// remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' );			// Comments
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );				// Incoming Links
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );					// Plugins
	// remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );				// Quick Press
	// remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );			// Recent Drafts
	remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );					// WordPress Blog
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );					// Other WordPress News
	remove_meta_box( 'yoast_db_widget', 'dashboard', 'normal' );					// WordPress SEO by Yoast

}

add_action('widgets_init', 'prc_unregister_default_widgets');
/**
 * Disable some or all of the default widgets.
 *
 * @since 2.0.0
 */
function prc_unregister_default_widgets() {

	// unregister_widget( 'WP_Widget_Pages' );
	// unregister_widget( 'WP_Widget_Calendar' );
	// unregister_widget( 'WP_Widget_Archives' );
	unregister_widget( 'WP_Widget_Meta' );
	// unregister_widget( 'WP_Widget_Search' );
	// unregister_widget( 'WP_Widget_Text' );
	// unregister_widget( 'WP_Widget_Categories' );
	// unregister_widget( 'WP_Widget_Recent_Posts' );
	// unregister_widget( 'WP_Widget_Recent_Comments' );
	// unregister_widget( 'WP_Widget_RSS' );
	// unregister_widget( 'WP_Widget_Tag_Cloud' );
	// unregister_widget( 'WP_Nav_Menu_Widget' );

}

add_filter( 'default_hidden_meta_boxes', 'prc_hidden_meta_boxes', 2 );
/**
 * Change which meta boxes are hidden by default on the post and page edit screens.
 *
 * @since 2.0.0
 */
function prc_hidden_meta_boxes( $hidden ) {

	global $current_screen;
	if( 'post' == $current_screen->id ) {
		$hidden = array( 'postexcerpt', 'trackbacksdiv', 'postcustom', 'commentstatusdiv', 'slugdiv', 'authordiv' );
		// Other hideable post boxes: genesis_inpost_scripts_box, commentsdiv, categorydiv, tagsdiv, postimagediv
	} elseif( 'page' == $current_screen->id ) {
		$hidden = array( 'postcustom', 'commentstatusdiv', 'slugdiv', 'authordiv', 'postimagediv' );
		// Other hideable post boxes: genesis_inpost_scripts_box, pageparentdiv
	}
	return $hidden;

}

// add_action( 'admin_footer-post-new.php', 'prc_media_manager_default_view' );
// add_action( 'admin_footer-post.php', 'prc_media_manager_default_view' );
/**
 * Change the media manager default view to 'upload', instead of 'library'
 *
 * See: http://wordpress.stackexchange.com/questions/96513/how-to-make-upload-filesselected-by-default-in-insert-media
 *
 * @since 2.0.11
 */
function prc_media_manager_default_view() {

	?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			wp.media.controller.Library.prototype.defaults.contentUserSetting=false;
		});
	</script>
	<?php

}

// add_filter( 'posts_where', 'prc_restrict_attachment_viewing' );
/**
 * Prevent authors and contributors from seeing media that isn't theirs
 *
 * See: http://wordpress.org/support/topic/restrict-editors-from-viewing-media-that-others-have-uploaded
 *
 * @since 2.0.20
 */
function prc_restrict_attachment_viewing( $where ) {

	global $current_user;
	if(
		is_admin() &&
		!current_user_can('edit_others_posts') &&
		isset($_POST['action']) &&
		$_POST['action'] == 'query-attachments'
	) {
		$where .= ' AND post_author=' . $current_user->data->ID;
	}
	return $where;

}

/**
 * Add a stylesheet for TinyMCE
 *
 * @since 2.0.0
 */
// add_editor_style( 'css/editor-style.css' );

add_filter( 'tiny_mce_before_init', 'prc_tiny_mce_before_init' );
/**
 * Modifies the TinyMCE settings array
 *
 * @since 2.0.0
 */
function prc_tiny_mce_before_init( $options ) {

	$options['element_format'] = 'html'; // See: http://www.tinymce.com/wiki.php/Configuration:element_format
	$options['schema'] = 'html5-strict'; // Only allow the elements that are in the current HTML5 specification. See: http://www.tinymce.com/wiki.php/Configuration:schema
	$options['block_formats'] = 'Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Blockquote=blockquote'; // Restrict the block formats available in TinyMCE. See: http://www.tinymce.com/wiki.php/Configuration:block_formats

	return $options;

}

add_filter( 'mce_buttons', 'prc_tinymce_buttons' );
/**
 * Enables some commonly used formatting buttons in TinyMCE. A good resource on customizing TinyMCE: http://www.wpexplorer.com/wordpress-tinymce-tweaks/
 *
 * @since 2.0.15
 */
function prc_tinymce_buttons( $buttons ) {

	$buttons[] = 'wp_page';															// Post pagination
	return $buttons;

}

add_filter( 'user_contactmethods', 'prc_user_contactmethods' );
/**
 * Updates the user profile contact method fields for today's popular sites.
 *
 * See: http://wpmu.org/shun-the-plugin-100-wordpress-code-snippets-from-across-the-net/
 *
 * @since 2.0.0
 */
function prc_user_contactmethods( $fields ) {

	//$fields['facebook'] = 'Facebook';												// Add Facebook
	//$fields['twitter'] = 'Twitter';												// Add Twitter
	//$fields['linkedin'] = 'LinkedIn';												// Add LinkedIn
	unset( $fields['aim'] );														// Remove AIM
	unset( $fields['yim'] );														// Remove Yahoo IM
	unset( $fields['jabber'] );														// Remove Jabber / Google Talk
	return $fields;

}

// add_action( 'admin_menu', 'prc_remove_dashboard_menus' );
/**
 * Remove default admin dashboard menus
 *
 * See: http://speckyboy.com/2011/04/27/20-snippets-and-hacks-to-make-wordpress-user-friendly-for-your-clients/
 *
 * @since 2.0.0
 */
function prc_remove_dashboard_menus() {

	global $menu;
	$restricted = array(
		__('Dashboard'),
		__('Posts'),
		__('Media'),
		__('Links'),
		__('Pages'),
		__('Comments'),
		__('Appearance'),
		__('Plugins'),
		__('Users'),
		__('Tools'),
		__('Settings')
	);
	end($menu);
	while( prev($menu) ) {
		$value = explode( ' ', $menu[key($menu)][0] );
		if( in_array($value[0] != NULL ? $value[0] : "" , $restricted) ) {
			unset( $menu[key($menu)] );
		}
	}

}

add_filter( 'login_errors', 'prc_login_errors' );
/**
 * Prevent the failed login notice from specifying whether the username or the password is incorrect.
 *
 * See: http://wpdaily.co/top-10-snippets/
 *
 * @since 2.0.0
 */
function prc_login_errors() {

	return 'Invalid username or password.';

}

add_action( 'admin_head', 'prc_hide_admin_help_button' );
/**
 * Hide the top-right help pull-down button by adding some CSS to the admin <head>
 *
 * See: http://speckyboy.com/2011/04/27/20-snippets-and-hacks-to-make-wordpress-user-friendly-for-your-clients/
 *
 * @since 2.0.0
 */
function prc_hide_admin_help_button() {

	?><style type="text/css">
		#contextual-help-link-wrap {
			display: none !important;
		}
	</style>
	<?php

}

/**
 * Deregister Genesis parent theme page templates
 *
 * See: http://wptheming.com/2014/04/features-wordpress-3-9/
 *
 * @since 2.2.8
 */
// add_filter( 'theme_page_templates', 'prc_deregister_page_templates' );
function prc_deregister_page_templates( $templates ) {

	unset($templates['page_archive.php']);
	unset($templates['page_blog.php']);
	return $templates;

}

add_action( 'admin_bar_menu', 'prc_admin_menu_plugins_node' );
/**
 * Add a plugins link to the appearance admin bar menu
 *
 * @since 2.2.9
 */
function prc_admin_menu_plugins_node( $wp_admin_bar ) {

	if( !current_user_can('install_plugins') )
		return;

	$node = array(
		'parent' => 'appearance',
		'id' => 'plugins',
		'title' => __('Plugins'),
		'href' => admin_url('plugins.php')
	);

	$wp_admin_bar->add_node( $node );

}
