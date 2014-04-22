<?php

/*
Plugin Name: Post Edit Toolbar
Plugin URI: http://www.webyourbusiness.com/post-edit-toolbar/
Description: Adds most recently edited posts to the WordPress Toolbar for easy access
Version: 1.2.2
Author: Web Your Business
Author URI: http://www.webyourbusiness.com/

Release Notes:

1.2.2 - added truncation if max len of title is > 40 chars
1.2 - added drafters + separators
1.1.1 - updated image included in the assets folder to be post-edit-tool-bar installed, not the page-edit-toolbar used as initial source
1.1 - bug fix + add new post - added 'Add New Post' to top of list and fixed incorrect variables passed to get_posts() - they differ from get_pages()
1.0 - initial release - based on Page Edit Toolbar by Jeremy Green
*/

add_action( 'admin_bar_menu', 'post_admin_bar_function', 999 );

function post_admin_bar_function( $wp_admin_bar ) {

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
	$drafts = recently_edited_drafts();

	foreach( $drafts as $draft ) {
		$drafts_found = 'Y';

		if ($draft->post_title == '') {
			$draft->post_title = '[EMPTY TITLE]';
		} else {
			if (strlen($draft->post_title) > 40){
				$draft->post_title = substr($draft->post_title, 0, 36).' [...]';
			}
		}

		if ($draft->post_title == '') {
			$draft->post_title = '[EMPTY TITLE]';
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
	$posts = recently_edited_posts();

	// loop through up to 5 most recently modified posts
	foreach( $posts as $post ) {

		if ($post->post_title == '') {
			$post->post_title = '[EMPTY TITLE]';
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

function recently_edited_drafts() {
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
function recently_edited_posts() {
	$args = array(
		'posts_per_page' => 5,
		'sort_column' => 'post_modified',
		'orderby' => 'post_date',
		'order' => 'DESC'
	);
	$posts = get_posts( $args );
	return $posts;
}
