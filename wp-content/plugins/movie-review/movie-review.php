<?php 
/**
 *
 * @wordpress-plugin
 * Plugin Name:       Custom movie Api
 * Description:       This Plugin contain all rest api.
 * Version:           1.0
 * Author:            NM
 */

add_action('init','create_movie_review');
function create_movie_review(){
	register_post_type('movie_review',array(
		'lables' => array(
			'name'=>'movie review',
			'singular_name'=>'movie review',
			'add_new'=>'add new',
			'add_new_item'=>'add new movie review',
			'eidt'=>'edit',
			'eidt_item'=>'edit movie review',
			'view'=>'view',
			'view_item'=>'view movie review',
			'search_item'=>'search movie review',
			'not_found'=>'movie review not found',
			'not_found_in_trash'=>'movie review found in trash',
			'parent'=>'parent movie review'

		), 
		'public'=>true,
		'menu_position'=>15,
		'supports'=>array('title','editor','comments','thumbnail','custom-fields'),
		'taxonomies'=>array(''),
		'menu_icon'=>plugins_url('images/akash.jpg',__FILE__),
		'has_archive'=>true
	)
	
	);
}

?>