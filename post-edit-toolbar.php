<?php

/*
Plugin Name: Post Edit Toolbar
Plugin URI: http://www.webyourbusiness.com/post-edit-toolbar/
Description: Adds most recently edited posts to the WordPress Toolbar for easy access
Version: 1.4.4.1
Author: Web Your Business
Author URI: http://www.webyourbusiness.com/

Release Notes:

1.4.4.1 - updated docs
1.4.4 - Added bloginfo('wpurl') to fix installations inside subfolders menus - now tested as working
1.4.2 - fixed a couple of typos - and initiated blank classes where needed - tested on multiple sites + php installs
1.4.1 - commented out blank title page code while I debug it (must be a difference between post + page fuctions in codex)
1.4.0 - Added link to site in the settings section + created function to shorten long post/page names (remove repeating code)
1.3.3 - removed home_url() calls - they seem redundant.
1.3.2 - found a problem with this new version if you had page-edit-toolbar installed - changed function names to resolve
1.3.1 - fixed broken page-edit-toolbar functionality where - hierarchical caused less than 5 pages to be returned
1.3.0 - Rolled in page-edit-toolbar functionality.
1.2.2 - added truncation if max len of title is > 40 chars
1.2 - added drafters + separators
1.1.1 - updated image included in the assets folder to be post-edit-tool-bar installed, not the page-edit-toolbar used as initial source
1.1 - bug fix + add new post - added 'Add New Post' to top of list and fixed incorrect variables passed to get_posts() - they differ from get_pages()
1.0 - initial release - based on Page Edit Toolbar by Jeremy Green

==
Known issues: Page list does not shorten length to <40 like it should - investigate later
*/

add_action( 'admin_bar_menu', 'pet_page_admin_bar_function', 998 );
add_action( 'admin_bar_menu', 'pet_post_admin_bar_function', 998 );

function pet_page_admin_bar_function( $wp_admin_bar ) {

	// parent page
	$args = array(
		'id' => 'page_list',
		'title' => 'Page List',
		'href' => get_bloginfo('wpurl').'/wp-admin/edit.php?post_type=page'
	);
	$wp_admin_bar->add_node( $args );

	// top item in list is add new page
	$args = array(
		'id' => 'page_item_a',
		'title' => 'Add New Page',
		'parent' => 'page_list',
		'href' => get_bloginfo('wpurl').'/wp-admin/post-new.php?post_type=page'
	);
	$wp_admin_bar->add_node( $args );

	// separator from new to drafts
	$args = array(
		'id' => 'page_item_b',
		'title' => '------------------------',
		'parent' => 'page_list',
		'href' => ''
	);
	$wp_admin_bar->add_node( $args );

	$page_drafts_found = 'N';
	$page_drafts = pet_recently_edited_page_drafts();

	// loop through the most recently modified page drafts
	foreach( $page_drafts as $page_draft ) {
		$page_drafts_found = 'Y';

		// fixing "Warning: Creating default object from empty value in errors":
		if (!is_object($page_draft)) {
			$page_draft = new stdClass;
			$page_draft->post_title = new stdClass;
		}

		$page_draft_title = return_short_title($page_draft->post_title,'[EMPTY DRAFT TITLE]');

		// add child nodes (page_draft recently edited)
		$args = array(
			'id' => 'post_item_' . $page_draft->ID,
			'title' => '<strong><u>Draft</u>:</strong> '.$page_draft_title,
			'parent' => 'page_list',
			'href' => get_bloginfo('wpurl').'/wp-admin/post.php?post=' . $page_draft->ID . '&action=edit'
		);
		$wp_admin_bar->add_node( $args );
	}

	if ($page_drafts_found == 'Y') {
		// separator from page_drafts to published
		$args = array(
			'id' => 'page_item_c',
			'title' => '------------------------',
			'parent' => 'page_list',
			'href' => ''
		);
		$wp_admin_bar->add_node( $args );
	}

	// get list of pages
	$pages = pet_recently_edited_pages();

	// loop through the most recently modified pages
	foreach( $pages as $thispage ) {

		// fixing "Warning: Creating default object from empty value in errors":
		if (!is_object($thispage)) {
			$thispage = new stdClass;
			$thispage->post_title = new stdClass;
		}

		$thispage_title = return_short_title($thispage->post_title,'[EMPTY PAGE TITLE]');

		// add child nodes (pages to edit)
		$args = array(
			'id' => 'page_item_' . $thispage->ID,
			'title' => $thispage_title,
			'parent' => 'page_list',
			'href' => get_bloginfo('wpurl').'/wp-admin/post.php?post=' . $thispage->ID . '&action=edit'
		);
		$wp_admin_bar->add_node( $args );
	}
}

///////////////////////// NOW POSTS /////////////////////////////

function pet_post_admin_bar_function( $wp_admin_bar ) {

	// parent post - the 'edit-all-posts' link at the top
	$args = array(
		'id' => 'post_list',
		'title' => 'Post List',
		'href' => get_bloginfo('wpurl').'/wp-admin/edit.php'
	);
	$wp_admin_bar->add_node( $args );

	// top item in list is add new post
	$args = array(
		'id' => 'post_item_a',
		'title' => 'Add New Post',
		'parent' => 'post_list',
		'href' => get_bloginfo('wpurl').'/wp-admin/post-new.php'
	);
	$wp_admin_bar->add_node( $args );

	// separator from new to drafts
	$args = array(
		'id' => 'post_item_b',
		'title' => '------------------------',
		'parent' => 'post_list',
		'href' => ''
	);
	$wp_admin_bar->add_node( $args );


	// get list of drafts
	$drafts_found = 'N';
	$drafts = pet_recently_edited_drafts();

	// loop through the most recently modified post drafts
	foreach( $drafts as $draft ) {
		$drafts_found = 'Y';

		// fixing "Warning: Creating default object from empty value in errors":
		if (!is_object($draft)) {
			$draft = new stdClass;
			$draft->post_title = new stdClass;
		}

		$draft_post_title = return_short_title($draft->post_title,'[EMPTY DRAFT TITLE]');

		// add child nodes (drafts recently edited)
		$args = array(
			'id' => 'post_item_' . $draft->ID,
			'title' => '<strong><u>Draft</u>:</strong> '.$draft_post_title,
			'parent' => 'post_list',
			'href' => get_bloginfo('wpurl').'/wp-admin/post.php?post=' . $draft->ID . '&action=edit'
		);
		$wp_admin_bar->add_node( $args );
	}

	if ($drafts_found == 'Y') {
		// separator from drafts to published
		$args = array(
			'id' => 'post_item_c',
			'title' => '------------------------',
			'parent' => 'post_list',
			'href' => ''
		);
		$wp_admin_bar->add_node( $args );
	}

	// get list of posts
	$posts = pet_recently_edited_posts();

	// loop through the most recently modified posts
	foreach( $posts as $post ) {

		// fixing "Warning: Creating default object from empty value in errors":
		if (!is_object($post)) {
			$post = new stdClass;
			$post->post_title = new stdClass;
		}

		$post_post_title = return_short_title($post->post_title,'[EMPTY POST TITLE]');

		// add child nodes (posts to edit)
		$args = array(
			'id' => 'post_item_' . $post->ID,
			'title' => $post_post_title,
			'parent' => 'post_list',
			'href' => get_bloginfo('wpurl').'/wp-admin/post.php?post=' . $post->ID . '&action=edit'
		);
		$wp_admin_bar->add_node( $args );
	}
}

function pet_recently_edited_drafts() {
	$args = array(
		'posts_per_page' => 2,
		'sort_column' => 'post_modified',
		'orderby' => 'post_date',
		'post_status' => 'draft',
		'order' => 'DESC'
	);
	$drafts = get_posts( $args );
	return $drafts;
}
function pet_recently_edited_posts() {
	$args = array(
		'posts_per_page' => 5,
		'sort_column' => 'post_modified',
		'orderby' => 'post_date',
		'order' => 'DESC'
	);
	$posts = get_posts( $args );
	return $posts;
}
function pet_recently_edited_pages() {
	$args = array(
		'number' => 5,
		'post_type' => 'page',
		'post_status' => 'publish',
		'sort_column' => 'post_modified',
		'hierarchical' => 0,
		'sort_order' => 'DESC'
	);
	$pages = get_pages( $args );
	return $pages;
}
function pet_recently_edited_page_drafts() {
	$args = array(
		'number' => 5,
		'post_type' => 'page',
		'post_status' => 'draft',
		'sort_column' => 'post_modified',
		'hierarchical' => 0,
		'sort_order' => 'DESC'
	);
	$pagedraft = get_pages( $args );
	return $pagedraft;
}
function return_short_title( $title_to_shorten, $if_empty ) {
	// the variables passed
	$the_title = $title_to_shorten;
	$return_if_empty = $if_empty;
	$return_value = $the_title;
	if (trim($the_title)== FALSE) {
		$the_title='';
		$title_len=0;
	} else {
		$title_len=strlen($the_title);
	}
	if ($title_len < 40){
		if ($title_len == 0) {
			$return_value = $return_if_empty;
		} else {
			$return_value = $the_title;
		}
	} else {
		$return_value = substr($the_title, 0, 36).' [...]';
	}
	return $return_value;
}
// This code adds the links in the settings section of the plugin
if ( ! function_exists( 'post_edit_toolbar_plugin_meta' ) ) :
        function post_edit_toolbar_plugin_meta( $links, $file ) { // add 'Plugin page' and 'Donate' links to plugin meta row
                if ( strpos( $file, 'post-edit-toolbar.php' ) !== false ) {
                        $links = array_merge( $links, array( '<a href="http://www.webyourbusiness.com/post-edit-toolbar/#donate" title="Support the development">Donate</a>' ) );
                        $links = array_merge( $links, array( '<a href="http://www.webyourbusiness.com/post-edit-toolbar/#premium" title="Post-Edit-Toolbar Pro">Should we make a Pro version?</a>' ) );
                }
                return $links;
        }
        add_filter( 'plugin_row_meta', 'post_edit_toolbar_plugin_meta', 10, 2 );
endif; // end of post_edit_toolbar_plugin_meta()
?>
