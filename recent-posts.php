<?php
/*
Plugin Name: Recent Posts
Plugin URI: https://wordpress.org/plugins/recent-posts/
Description: Returns a list of the most recent posts.
Version: 1.3
Author: Nick Momrik
Author URI: http://nickmomrik.com/
*/

function mdv_recent_posts( $no_posts = 5, $before = '<li>', $after = '</li>', $hide_pass_post = true, $skip_posts = 0, $show_excerpts = false, $include_pages = false ) {
	global $wpdb;

	$now = gmdate( "Y-m-d H:i:s", time() );
	$request = "SELECT ID, post_title, post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' ";
	if ( $hide_pass_post ) {
		$request .= "AND post_password ='' ";
	}
	if ( $include_pages ) {
		$request .= "AND (post_type='post' OR post_type='page') ";
	} else {
		$request .= "AND post_type='post' ";
	}
	$request .= $wpdb->prepare( "AND post_date_gmt < %s ORDER BY post_date_gmt DESC LIMIT %d, %d", $now, $skip_posts, $no_posts );
	$posts = $wpdb->get_results( $request );
	$output = '';

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$post_title = $post->post_title;
			$output .= $before . '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" rel="bookmark" title="Permanent Link: ' . esc_attr( $post_title ) . '">' . esc_html( $post_title ) . '</a>';
			if ( $show_excerpts ) {
				$post_excerpt = esc_html( $post->post_excerpt );
				$output.= '<br />' . $post_excerpt;
			}
			$output .= $after;
		}
	} else {
		$output .= $before . "None found" . $after;
	}

	echo $output;
}
