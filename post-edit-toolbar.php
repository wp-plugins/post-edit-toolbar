<?php

/*
Plugin Name: Post Edit Toolbar
Plugin URI: http://www.webyourbusiness.com
Description: Adds most recently edited posts to the WordPress Toolbar for easy access
Version: 1.3.1
Author: Web Your Business
Author URI: http://www.webyourbusiness.com/post-edit-toolbar

Release Notes:

1.3.2 - found a problem with this new version if you had page-edit-toolbar installed - changed function names to resolve
1.3.1 - fixed broken page-edit-toolbar functionality where - hierarchical caused less than 5 pages to be returned
1.3.0 - Rolled in page-edit-toolbar functionality.
1.2.2 - added truncation if max len of title is > 40 chars
1.2 - added drafters + separators
1.1.1 - updated image included in the assets folder to be post-edit-tool-bar installed, not the page-edit-toolbar used as initial source
1.1 - bug fix + add new post - added 'Add New Post' to top of list and fixed incorrect variables passed to get_posts() - they differ from get_pages()
1.0 - initial release - based on Page Edit Toolbar by Jeremy Green
*/

add_action( 'admin_bar_menu', 'pot_page_admin_bar_function', 998 );
add_action( 'admin_bar_menu', 'pot_post_admin_bar_function', 998 );

function pot_page_admin_bar_function( $wp_admin_bar ) {

	// parent page
	$args = array(
		'id' => 'page_list',
		'title' => 'Page List',
		'href' => home_url() . '/wp-admin/edit.php?post_type=page'
	);
	$wp_admin_bar->add_node( $args );

	// top item in list is add new page
	$args = array(
		'id' => 'page_item_a',
		'title' => 'Add New Page',
		'parent' => 'page_list',
		'href' => home_url() . '/wp-admin/post-new.php?post_type=page'
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
	$page_drafts = pot_recently_edited_page_drafts();

	foreach( $page_drafts as $page_draft ) {
		$page_drafts_found = 'Y';

		if ($page_draft->post_title == '') {
			$page_draft->post_title = '[EMPTY PAGE TITLE]';
		} else {
			if (strlen($page_draft->post_title) > 40){
				$page_draft->post_title = substr($page_draft->post_title, 0, 36).' [...]';
			}
		}

		// add child nodes (page_draft recently edited)
		$args = array(
			'id' => 'post_item_' . $page_draft->ID,
			'title' => '<strong><u>Draft</u>:</strong> '.$page_draft->post_title,
			'parent' => 'page_list',
			'href' => home_url() . '/wp-admin/post.php?post=' . $page_draft->ID . '&action=edit'
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
	$pages = pot_recently_edited_pages();

	// loop through up to 10 most recently modified pages
	foreach( $pages as $page ) {
		if ($page->post_title == '') {
			$page->post_title = '[EMPTY PAGE TITLE]';
		} else {
			if (strlen($page->post_title) > 40){
				$page->post_title = substr($page->post_title, 0, 36).' [...]';
			}
		}

		// add child nodes (pages to edit)
		$args = array(
			'id' => 'page_item_' . $page->ID,
			'title' => $page->post_title,
			'parent' => 'page_list',
			'href' => home_url() . '/wp-admin/post.php?post=' . $page->ID . '&action=edit'
		);
		$wp_admin_bar->add_node( $args );
	}
}

///////////////////////// NOW POSTS /////////////////////////////

function pot_post_admin_bar_function( $wp_admin_bar ) {

	// parent post
	$args = array(
		'id' => 'post_list',
		'title' => 'Post List',
		'href' => home_url() . '/wp-admin/edit.php'
	);
	$wp_admin_bar->add_node( $args );

	// top item in list is add new post
	$args = array(
		'id' => 'post_item_a',
		'title' => 'Add New Post',
		'parent' => 'post_list',
		'href' => home_url() . '/wp-admin/post-new.php'
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
	$drafts = pot_recently_edited_drafts();

	foreach( $drafts as $draft ) {
		$drafts_found = 'Y';

		if ($draft->post_title == '') {
			$draft->post_title = '[EMPTY POST TITLE]';
		} else {
			if (strlen($draft->post_title) > 40){
				$draft->post_title = substr($draft->post_title, 0, 36).' [...]';
			}
		}

		// add child nodes (drafts recently edited)
		$args = array(
			'id' => 'post_item_' . $draft->ID,
			'title' => '<strong><u>Draft</u>:</strong> '.$draft->post_title,
			'parent' => 'post_list',
			'href' => home_url() . '/wp-admin/post.php?post=' . $draft->ID . '&action=edit'
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
	$posts = pot_recently_edited_posts();

	// loop through up to 5 most recently modified posts
	foreach( $posts as $post ) {

		if ($post->post_title == '') {
			$post->post_title = '[EMPTY POST TITLE]';
		} else {
			if (strlen($post->post_title) > 40){
				$post->post_title = substr($post->post_title, 0, 36).' [...]';
			}
		}

		// add child nodes (posts to edit)
		$args = array(
			'id' => 'post_item_' . $post->ID,
			'title' => $post->post_title,
			'parent' => 'post_list',
			'href' => home_url() . '/wp-admin/post.php?post=' . $post->ID . '&action=edit'
		);
		$wp_admin_bar->add_node( $args );
	}
}

function pot_recently_edited_drafts() {
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
function pot_recently_edited_posts() {
	$args = array(
		'posts_per_page' => 5,
		'sort_column' => 'post_modified',
		'orderby' => 'post_date',
		'order' => 'DESC'
	);
	$posts = get_posts( $args );
	return $posts;
}
function pot_recently_edited_pages() {
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
function pot_recently_edited_page_drafts() {
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
