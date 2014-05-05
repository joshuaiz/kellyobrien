<?php

/*
Plugin Name: Featured Images for Categories Pro
Plugin URI: http://helpforwp.com/plugins/features-images-forcategories/
Description: Assign a featured image to a WordPress category, tag or custom taxonomy then use these featured images via a widget area or a shortcode.
Version: 1.4.2
Author: HelpForWP
Author URI: http://HelpForWP.com

------------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, 
or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

require_once('featured-images-for-categories-pro-widget.php');
require_once('featured-images-for-categories-pro-term-widget.php');

global $_wpfifc_plugin_name, $_wpfifc_version, $_wpfifc_home_url, $_wpfifc_plugin_author, $_wpfifc_messager;

$_wpfifc_plugin_name = 'Featured Images for Categories';
$_wpfifc_version = '1.4.2';
$_wpfifc_home_url = 'http://helpforwp.com';
$_wpfifc_plugin_author = 'HelpForWP';


if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	require_once(dirname( __FILE__ ) . '/inc/EDD_SL_Plugin_Updater.php');
}

$_wpfifc_license_key = trim( get_option( 'wpfifc_license_key' ) );
// setup the updater
$_wpfifc_updater = new EDD_SL_Plugin_Updater( $_wpfifc_home_url, __FILE__, array( 
		'version' 	=> $_wpfifc_version, 				// current version number
		'license' 	=> $_wpfifc_license_key, 		// license key (used get_option above to retrieve from DB)
		'item_name' => $_wpfifc_plugin_name, 	// name of this plugin
		'author' 	=> $_wpfifc_plugin_author  // author of this plugin
	)
);

//for new version message and expiring version message shown on dashboard
if( !class_exists( 'EddSLUpdateExpiredMessager' ) ) {
	// load our custom updater
	require_once(dirname( __FILE__ ) . '/inc/edd-sl-update-expired-messager.php');
}
$init_arg = array();
$init_arg['plugin_name'] = $_wpfifc_plugin_name;
$init_arg['plugin_download_id'] = 5821;
$init_arg['plugin_folder'] = 'featured-images-for-categories-pro';
$init_arg['plugin_file'] = basename(__FILE__);
$init_arg['plugin_version'] = $_wpfifc_version;
$init_arg['plugin_home_url'] = $_wpfifc_home_url;
$init_arg['plugin_sell_page_url'] = 'http://helpforwp.com/downloads/featured-images-for-categories/';
$init_arg['plugin_author'] = $_wpfifc_plugin_author;
$init_arg['plugin_option_menu'] = 'wpfifc_pro_options';
$init_arg['plugin_license_key_opiton_name'] = 'wpfifc_license_key';
$init_arg['plugin_license_status_option_name'] = 'wpfifc_license_key_status';
$_wpfifc_messager = new EddSLUpdateExpiredMessager( $init_arg );

class WPFeaturedImgCategoriesPro {
	
	var $_database_version = 132;
	var $_database_option_name = '_wpfifc_taxonomy_term_database_version_';

	public function __construct() {
		
		if( is_admin() ) {
			add_action( 'admin_init', array($this, 'wpfifc_register_settings') );
			add_action( 'admin_menu', array($this, 'wpfifc_add_admin_option_page') );
	    }
		add_action( 'widgets_init', create_function( '', 'register_widget( "WPFeaturedImgCategoriesProWidget" );' ) );
		add_action( 'widgets_init', create_function( '', 'register_widget( "WPFeaturedImgCategoriesTermWidget" );' ) );
		
		register_activation_hook( __FILE__, array($this, 'wpfifc_activate' ) );
		register_deactivation_hook( __FILE__, array($this, 'wpfifc_deactivate' ) );
		register_uninstall_hook( __FILE__,  'WPFeaturedImgCategories::wpfifc_remove_option' );
		
		//enale theme support feature iamge
		add_theme_support( 'post-thumbnails' );
		
		//Plugin update actions
		add_action( 'admin_init', array($this, 'wpfifc_activate_license') );
		add_action( 'admin_init', array($this, 'wpfifc_deactivate_license') );
		
		
		//create custome field for taxonomies
		add_action( 'admin_init', array($this, 'wpfifc_enqueue_scripts'), 999 );
		add_action( 'admin_init', array($this, 'wpfifc_taxonomies_add_form_fields'), 999 );
		add_action( 'admin_init', array($this, 'wpfifc_taxonomies_save_form_fields'), 999 );
		
		//ajax action
		add_action( 'wp_ajax_wpfifc-remove-image', array($this, 'wpfifc_ajax_set_post_thumbnail') );
		add_action( 'wp_ajax_wpfifcgetterms', array($this, 'wpfifc_ajax_get_terms_of_given_taxonomy') );
		
		//shortcodes
		add_shortcode('FeaturedImagesCat', array($this, 'wpfifc_front_show') );
		
		add_action( 'genesis_before_loop', array($this, 'genesis_show_taxonomy_image'), 12 );
		
		//check and update database format
		$this->wpfifc_upgrade_database();
	}

	
	function wpfifc_activate() {
		//create a post for save 
		$wpfifc_curr_option =	get_option('wpfifc_image_padding');
		if( !$wpfifc_curr_option ){
			update_option('wpfifc_image_padding', '2' );
		}
		$wpfifc_curr_option =	get_option('wpfifc_default_columns');
		if( !$wpfifc_curr_option ){
			update_option('wpfifc_default_columns', '3' );
		}
		$wpfifc_curr_option =	get_option('wpfifc_default_size');
		if(!$wpfifc_curr_option){
			update_option('wpfifc_default_size', 'thumbnail' );
		}
		$wpfifc_curr_option =	get_option('wpfifc_genesis_taxonomy');
		if(!$wpfifc_curr_option){
			update_option('wpfifc_genesis_taxonomy', array() );
		}
		$wpfifc_curr_option =	get_option('wpfifc_genesis_position');
		if(!$wpfifc_curr_option){
			update_option('wpfifc_genesis_position', 'left');
		}
	}
	
	
	function wpfifc_deactivate(){
		
	}


	function wpfifc_remove_option() {
	 	delete_option('wpfifc_post_ids_save_image');
	 
		delete_option('wpfifc_image_padding');
		delete_option('wpfifc_default_columns');	
		delete_option('wpfifc_default_size');
		delete_option('wpfifc_genesis_taxonomy');
		delete_option('wpfifc_genesis_position');
		
		//widget option
		delete_option('widget_wpfifc_widget');
		
		delete_option('wpfifc_license_key');
		delete_option('wpfifc_license_key_status');
		
		return;
	}
	
	function wpfifc_add_admin_option_page(){
		add_options_page('Featured Image for Categories Pro', 'Featured Image for Categories Pro', 'manage_options', 'wpfifc_pro_options', array($this, 'wpfifc_option_page') );
	}
	
	function wpfifc_option_page() {
		if ( ! current_user_can( 'manage_options' ) ){
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}		
		
		require_once('inc/featured-images-for-cat-options.php');
		//for title and body
		wpfifc_pro_options_shown();

		
		//for footer
		require_once('inc/footer.php');
	}
	
	function wpfifc_register_settings() {
		register_setting( 'wpfifc-settings', 'wpfifc_image_padding' );
		register_setting( 'wpfifc-settings', 'wpfifc_default_columns' );
		register_setting( 'wpfifc-settings', 'wpfifc_default_size' );
		register_setting( 'wpfifc-settings', 'wpfifc_genesis_taxonomy' );
		register_setting( 'wpfifc-settings', 'wpfifc_genesis_position' );		
	}
	
	function wpfifc_enqueue_scripts(){
		wp_enqueue_style('thickbox');
		
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wpfifc-admin', plugin_dir_url( __FILE__ ) . 'js/featured-images-for-cat-pro-admin.js', array( 'jquery' ) );
	}
	
	function wpfifc_taxonomies_add_form_fields(){
		$args = array( 'public' => true );
		$output = 'objects';
		$add_taxes = get_taxonomies( $args, $output );

		foreach ( $add_taxes  as $add_tax ) {
			if ( $add_tax->name == 'nav_menu' || $add_tax->name == 'post_format') {
				continue;
			}
			add_action( $add_tax->name.'_edit_form_fields', array($this, 'wpfifc_taxonomies_edit_meta'), 10, 2 );
		}
	}
	
	//edit term page
	function wpfifc_taxonomies_edit_meta( $term ) {
 		// put the term ID into a variable
		$term_id = $term->term_id;
		$post = get_default_post_to_edit( 'post', true );
		$post_ID = $post->ID;

		$wpfifc_license_key = trim(get_option('wpfifc_license_key'));
		$wpfifc_license_status = trim(get_option('wpfifc_license_key_status'));
		if ( !$wpfifc_license_key || $wpfifc_license_status != 'valid' ){
			$wpfifc_license_status = 'invalid';
			delete_option( 'wpfifc_license_key_status' );
			
			$setting_page = admin_url('options-general.php?page=wpfifc_pro_options');
			echo '<tr class="form-field"><th>Set Featured Image</th><td>Please go to <a href="'.$setting_page.'">plugin setting page</a> activate the your license first.</td></tr>';
			return;
		}
	?>
        <tr class="form-field">
			<th>Set Featured Image</th>
            <td>
            	<div id="postimagediv" class="postbox" style="width:95%;" >
                    <div class="inside">
                        <?php wp_enqueue_media( array('post' => $post_ID) ); ?>
                        <?php
                            $thumbnail_id = get_option( '_wpfifc_taxonomy_term_'.$term_id.'_thumbnail_id_', 0 );
                            echo _wp_post_thumbnail_html( $thumbnail_id, $post_ID );
                        ?>
                    </div>
                    <input type="hidden" name="wpfifc_taxonomies_edit_post_ID" id="wpfifc_taxonomies_edit_post_ID_id" value="<?php echo $post_ID; ?>" />
                    <input type="hidden" name="wpfifc_taxonomies_edit_term_ID" id="wpfifc_taxonomies_edit_term_ID_id" value="<?php echo $term_id; ?>" />
                </div>
        	</td>
		</tr>
	<?php
	}
	
	function wpfifc_taxonomies_save_form_fields(){
		$args = array( 'public' => true );
		$output = 'objects';
		$add_taxes = get_taxonomies( $args, $output );
		foreach ( $add_taxes  as $add_tax ) {
			if ( $add_tax->name == 'nav_menu' || $add_tax->name == 'post_format') {
				continue;
			}
			add_action('edited_'.$add_tax->name, array($this, 'wpfifc_taxonomies_save_meta'), 10, 2 );  
		}
	}

	function wpfifc_taxonomies_save_meta( $term_id ) {
		if ( isset( $_POST['wpfifc_taxonomies_edit_post_ID'] ) ) {
			$default_post_ID = $_POST['wpfifc_taxonomies_edit_post_ID'];
		}
		$thumbnail_id = get_post_meta( $default_post_ID, '_thumbnail_id', true );
		if( $thumbnail_id ){
			update_option( '_wpfifc_taxonomy_term_'.$term_id.'_thumbnail_id_', $thumbnail_id );
		}
	}  
	
	function wpfifc_activate_license() {
		// listen for our activate button to be clicked
		if( isset( $_POST['wpfifc_license_activate'] ) ) {
			global $_wpfifc_plugin_name, $_wpfifc_home_url;

			// run a quick security check 
			if( ! check_admin_referer( 'wpfifc_license_key_nonce', 'wpfifc_license_key_nonce' ) ) 	
				return; // get out if we didn't click the Activate button
	
			// retrieve the license from the database
			$license = trim( $_POST['wpfifc_license_key'] );
				
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $_wpfifc_plugin_name ) // the name of our product in EDD
			);
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $_wpfifc_home_url ), array( 'timeout' => 15, 'sslverify' => false ) );
			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
	
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( 'wpfifc_license_key', $license );
			update_option( 'wpfifc_license_key_status', $license_data->license );
		}
	}
	
	function wpfifc_deactivate_license() {
		// listen for our activate button to be clicked
		if( isset( $_POST['wpfifc_license_deactivate'] ) ) {
			global $_wpfifc_plugin_name, $_wpfifc_home_url;
			
			// run a quick security check 
			if( ! check_admin_referer( 'wpfifc_license_key_nonce', 'wpfifc_license_key_nonce' ) ) 	
				return; // get out if we didn't click the Activate button
	
			// retrieve the license from the database
			$license = trim( get_option( 'wpfifc_license_key' ) );
				
	
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'deactivate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $_wpfifc_plugin_name ) // the name of our product in EDD
			);
			
			// Call the custom API.
			global $_wpfifc_home_url;
			$response = wp_remote_get( add_query_arg( $api_params, $_wpfifc_home_url ), array( 'timeout' => 15, 'sslverify' => false ) );
	
			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
	
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' )
				delete_option( 'wpfifc_license_key_status' );
		}
	}
	
	function wpfifc_ajax_set_post_thumbnail() {
		global $current_user;

		if ( $current_user->ID < 0 ){
			wp_die( 'ERROR:You are not allowed to do the operation.' );
		}
		$post_ID = intval( $_POST['post_id'] );
		if ( $post_ID < 1 ){
			wp_die( "ERROR:Invalid post ID.".$post_ID );
		}
		delete_post_thumbnail( $post_ID );

		$thumbnail_id = intval( $_POST['thumbnail_id'] );
		if ( $thumbnail_id == '-1' ){
			//delete option which used to saving thumbnail id
			if( $_POST['term_id'] > 0 ){
				delete_option( '_wpfifc_taxonomy_term_'.$_POST['term_id'].'_thumbnail_id_' );
			}
			$return = _wp_post_thumbnail_html( null, $post_ID );
			wp_die( $return );
			
		}
		wp_die( "ERROR" );
	}
	
	function wpfifc_front_show( $atts ){
		$wpfifc_license_key = trim(get_option('wpfifc_license_key'));
		$wpfifc_license_status = trim(get_option('wpfifc_license_key_status'));
		if ( !$wpfifc_license_key || $wpfifc_license_status != 'valid' ){
			$wpfifc_license_status = 'invalid';
			delete_option( 'wpfifc_license_key_status' );
			$setting_page = admin_url('options-general.php?page=wpfifc_pro_options');
			
			$output = '<div class="FeaturedImageTax">'."\n";
			$output .= '<p>Please go to <a href="'.$setting_page.'">plugin setting page</a> activate the your license first.</p>';
			$output .= '</div>';
			
			return $output;
		}
		extract( shortcode_atts( array(
							  'taxonomy' => '',
							  'columns' => 0,
							  'imagesize' => '',
							  'orderby' => 'name',
							  'order' => 'ASC',
							  'hideempty' => 0,
							  'showcatname' => 0,
							  'showcatdesc' => 0,
							  'include' => '',
							  'parentcatsonly' => 0,
							  'childrenonly' => ''), 
						$atts )
			   );
		$show_cat_name = false;
		$show_cat_desc = false;
		if( $showcatname && is_string($showcatname) ){
			$show_cat_name = $showcatname == 'true' ? true : false;
		}else if( is_bool($showcatname) ){
			$show_cat_name = $showcatname;
		}
		$show_cat_desc = false;
		if( $showcatdesc && is_string($showcatdesc) ){
			$show_cat_desc = $showcatdesc == 'true' ? true : false;
		}else if( is_bool($showcatdesc) ){
			$show_cat_desc = $showcatdesc;
		}
		$include_array = array();
		if( $include && is_string($include) ){
			$include_array = explode(',', $include);
			foreach($include_array as $key => $include_term_id){
				$include_term_id = intval($include_term_id);
				if( is_int($include_term_id) == false ){
					unset($include_array[$key]);
				}
				$include_array[$key] = $include_term_id;
			}
		}
		//check if only show parent categories
		$show_parent_cat_only = false;
		if( $parentcatsonly && is_string($parentcatsonly) ){
			$show_parent_cat_only = $parentcatsonly == 'true' ? true : false;
		}else if( is_bool($parentcatsonly) ){
			$show_parent_cat_only = $parentcatsonly;
		}
		//show the child categories only of the included category ids.
		$childrenonly_array = array();
		if( $childrenonly && is_string($childrenonly) ){
			$childrenonly_array = explode(',', $childrenonly);
			foreach($childrenonly_array as $key => $childrenonly_term_id){
				$childrenonly_term_id = intval($childrenonly_term_id);
				if( is_int($childrenonly_term_id) == false ){
					unset($childrenonly_array[$key]);
				}
				$childrenonly_array[$key] = $childrenonly_term_id;
			}
		}
		
		if ( $taxonomy == '' ){
			return;
		}
		$taxonomy_obj = get_taxonomy( $taxonomy );
		if ( !$taxonomy_obj ){
			return;
		}
		$orderby = strtolower($orderby);
		if($orderby != 'name' && $orderby != 'slug' && $orderby != 'id' && $orderby != 'count'){
			$orderby != 'name';
		}
		$order = strtoupper($order);
		if($order != 'ASC' && $order != 'DESC'){
			$order = 'ASC';
		}
		$hideempty = intval($hideempty);
		if($hideempty !== 0 && $hideempty !== 1){
			$hideempty = 0;
		}
		//get terms
		$taxonomy_terms = array();
		if( count($include_array) > 0 ){ //if a user has all three of these options we should give priority to include first and ignore the others
			$show_parent_cat_only = false;
			$taxonomy_terms = get_terms( $taxonomy, array('orderby' => $orderby, 'order' => $order, 'hide_empty' => $hideempty, 'include' => $include_array) );
			if ( !$taxonomy_terms || count($taxonomy_terms) < 1 ){
				return;
			}
			//re-order terms by include ids order
			$temp_terms_array = array();
			$taxonomy_terms_id_as_key = array();
			foreach($taxonomy_terms as $key => $term_obj){
				$taxonomy_terms_id_as_key[$term_obj->term_id] = $term_obj;
			}
			foreach($include_array as $include_term_id){
				$temp_terms_array[$include_term_id] = $taxonomy_terms_id_as_key[$include_term_id];
			}
			$taxonomy_terms = $temp_terms_array;
		}else if( $show_parent_cat_only ){ //If a user has parentcatsonly and childrenonly then we should use parentcatsonly and ignore childrenonly.
			$taxonomy_terms = get_terms( $taxonomy, array('orderby' => $orderby, 'order' => $order, 'hide_empty' => $hideempty) );
			if ( !$taxonomy_terms || count($taxonomy_terms) < 1 ){
				return;
			}
		}else if( count($childrenonly_array) > 0 ){
			$show_parent_cat_only = false;
			foreach( $childrenonly_array as $parent_term_id ){
				$child_terms = get_terms( $taxonomy, array('orderby' => $orderby, 'order' => $order, 'hide_empty' => $hideempty, 'parent' => $parent_term_id) );
				if ( !$child_terms || count($child_terms) < 1 ){
					continue;
				}
				$taxonomy_terms = array_merge($taxonomy_terms, $child_terms);
			}
		}else{
			$taxonomy_terms = get_terms( $taxonomy, array('orderby' => $orderby, 'order' => $order, 'hide_empty' => $hideempty) );
			if ( !$taxonomy_terms || count($taxonomy_terms) < 1 ){
				return;
			}
		}

		//check imagesize
		if ( $imagesize == '' ){
			$imagesize = get_option('wpfifc_default_size');
		}
		if ( $imagesize == '' ){
			$imagesize = 'thumbnail';
		}
		$image_sizes = get_intermediate_image_sizes();
		if ( $imagesize != 'thumbnail' && !in_array( $imagesize, $image_sizes) ){
			$imagesize = 'thumbnail';
		}
		//get padding
		$padding = get_option('wpfifc_image_padding');
		//get columns
		if ( $columns == 0 ){
			$columns = get_option('wpfifc_default_columns'); 	
		}
		//caculate column width
		$column_width = floor(100 / $columns);
		
		$output = '<div class="FeaturedImageTax">'."\n";
		$images_str = '';
		$column_item = 0;
		foreach( $taxonomy_terms as $term ){
			if( $show_parent_cat_only && $term->parent != 0 ){
				continue;
			}
			$term_id = $term->term_id;
			$thumbnail_id = get_option( '_wpfifc_taxonomy_term_'.$term_id.'_thumbnail_id_', 0 );
			if ( $thumbnail_id < 1 ){
				continue;
			}
			$image = wp_get_attachment_image_src( $thumbnail_id, $imagesize );

			list($src, $width, $height) = $image;
			if ( $src ){
				$padding_str = $padding ? $padding.'px;' : '0;';
				$images_str .= '<div style="width:'.$column_width.'%; text-align:center;float:left;">
									<a href="'.get_term_link($term->slug, $taxonomy).'" title="'.$term->name.'">
										<img src="'.$src.'" alt="'.$term->name.'" style="padding:'.$padding_str.'" />
									</a>';
				if( $show_cat_name ){
					$images_str .= '
									<a href="'.get_term_link($term->slug, $taxonomy).'" title="'.$term->name.'">
										<h2 class="FeaturedImageCat">'.$term->name.'</h2>
									</a>';
				}
				if( $show_cat_desc ){					
					$images_str .= '<div class="FeaturedImageCatDesc">'.$term->description.'</div>';
				}					
				$images_str .= '</div>'."\n";
				$column_item++;
				if ( $column_item >= $columns ){
					$column_item = 0;
					$images_str .= "\n".'<div style="clear:both;"></div>'."\n";
				}
			}
		}
		$output .= $images_str;
		$output .= '</div>'."\n";
		
		return $output;
	}
	
	
	function genesis_show_taxonomy_image(){
		if(!defined('PARENT_THEME_NAME') || PARENT_THEME_NAME != 'Genesis'){
			return;
		}
		global $wp_query;

		if (!is_category() && !is_tag() && !is_tax()){
			return;
		}
	
		if (get_query_var( 'paged' ) >= 2){
			return;
		}
		$taxonomy = '';
		if(is_category()){
			$taxonomy = 'category';
		}
		if(is_tag()){
			$taxonomy = 'post_tag';
		}
		if(is_tax()){
			$taxonomy = get_query_var('taxonomy');
		}
		$saved_genesis_taxonomies = get_option('wpfifc_genesis_taxonomy', array());
		$saved_genesis_postion = get_option('wpfifc_genesis_position', 'left');
		$imagesize = get_option('wpfifc_default_size');
		if ( $imagesize == '' ){
			$imagesize = 'thumbnail';
		}
		if (!is_array($saved_genesis_taxonomies) || count($saved_genesis_taxonomies) < 1){
			return;
		}
		if(!in_array($taxonomy, $saved_genesis_taxonomies)){
			return;
		}

		$term = is_tax() ? get_term_by('slug', get_query_var('term'), get_query_var('taxonomy')) : $wp_query->get_queried_object();
		if( !$term ){
			return;
		}
		$term_id = $term->term_id;
		$thumbnail_id = get_option( '_wpfifc_taxonomy_term_'.$term_id.'_thumbnail_id_', 0 );
		if( $thumbnail_id < 1 ){
			return;
		}
		$image = wp_get_attachment_image_src( $thumbnail_id, $imagesize );
		
		list($src, $width, $height) = $image;
		if ( $src ){
			echo '<img src="'.$src.'" style="float:'.$saved_genesis_postion.';" class="FeaturedImageTax"/>';
		}
		
		return;
	}
	
	function wpfifc_ajax_get_terms_of_given_taxonomy(){
		$taxonomy = $_POST['taxonomy'];
		$args = array(	'orderby'       => 'name', 
						'order'         => 'ASC',
						'hide_empty'	=> false ); 
		$terms = get_terms( $taxonomy, $args );
		$terms_option_str = '';
		
		if(count($terms) > 0 ){
			foreach( $terms as $term ){
				$terms_option_str .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
			} 
		}
		echo $terms_option_str;
					
		die();
	}
	
	function wpfifc_upgrade_database(){
		global $wpdb;
		
		$exist_database_version = get_option( $this->_database_option_name, 0 );
		if( $exist_database_version >= $this->_database_version ){
			return;
		}
		
		//convert old data format to new and save database version
		$sql = 'SELECT `option_id`, `option_name`, `option_value` FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_wpfifc_taxonomy_term_%"';
		$results = $wpdb->get_results( $sql );
		if( !$results || count($results) < 1 ){
			update_option( $this->_database_option_name, $this->_database_version );
			return;
		}
		$old_option_ids = array();
		foreach( $results as $record ){
			$old_option_ids[] = $record->option_id;
			$term_id = str_replace('_wpfifc_taxonomy_term_', '', $record->option_name);
			$term_id = intval($term_id);
			$post_ID = $record->option_value;
			//check if the post still in database
			$sql_post = 'SELECT * FROM `'.$wpdb->posts.'` WHERE `ID` = '.$post_ID;
			if( !$wpdb->get_results( $sql_post ) ){
				continue;
			}
			//get thumbnail id
			$sql_postmeta = 'SELECT * FROM `'.$wpdb->postmeta.'` WHERE `post_id` = '.$post_ID.' AND `meta_key` = "_thumbnail_id"';
			$thumbnail_id_obj = $wpdb->get_results( $sql_postmeta );
			if( !$thumbnail_id_obj ){
				continue;
			}
			$thumbnail_id = $thumbnail_id_obj[0]->meta_value;
			$thumbnail_id = intval($thumbnail_id);
			if( !$thumbnail_id ){
				continue;
			}
			//write new option
			$new_option = '_wpfifc_taxonomy_term_'.$term_id.'_thumbnail_id_';
			update_option( $new_option, $thumbnail_id );
		}
		
		//remove old options
		$optons_ids_str = implode( ',', $old_option_ids );
		$optons_ids_str = trim( $optons_ids_str );
		$sql = 'DELETE FROM '.$wpdb->options.' WHERE option_id IN ('.$optons_ids_str.')';
		$wpdb->query( $sql );
		
		update_option( $this->_database_option_name, $this->_database_version );
	}
}


$wpfifc_pro_instance = new WPFeaturedImgCategoriesPro();

function fifc_get_tax_thumbnail( $category_id, $taxonomy, $image_size = 'thumbnail', &$err = ''){
	$err = '';
	if( $category_id < 1 ){
		$err = 'A category id is required';
		return '';
	}
	if( strlen($taxonomy) < 1 ){
		$err = 'A taxonomy is required';
		return '';
	}
	$term_obj = get_term_by( 'id', $category_id, $taxonomy);
	if( !$term_obj ){
		$err = 'Invalid category id or taxonomy';
		return '';
	}
	//check if hte image_size exit
	$systme_image_sizes = get_intermediate_image_sizes();
	if ( $image_size != 'thumbnail' && !in_array( $image_size, $systme_image_sizes) ){
		$image_size = 'thumbnail';
	}
	//get thumbnail
	$thumbnail_id = get_option( '_wpfifc_taxonomy_term_'.$category_id.'_thumbnail_id_', 0 );
	if ( $thumbnail_id < 1 ){
		$err = 'A category id is required or the category hasn\'t been assigned featured image.';
		return '';
	}
	$image = wp_get_attachment_image_src( $thumbnail_id, $image_size );
	
	list($src, $width, $height) = $image;
	if ( $src ){
		return $src;
	}
	
	$err = 'Invalid featured image';
	return '';
}

function fifc_the_tax_thumbnail( $category_id, $taxonomy, $image_size = 'thumbnail' ){
	$err_ret = '';
	$image_url = fifc_get_tax_thumbnail( $category_id, $taxonomy, $image_size, $err_ret);
	if( $err_ret || $image_url == ''){
		echo $err_ret;
		return '';
	}
	
	$term_obj = get_term_by( 'id', $category_id, $taxonomy);
	if( !$term_obj ){
		echo 'Invalid category id or taxonomy';
		return;
	}
	
	echo '<a href="'.get_term_link($term_obj->slug, $taxonomy).'" title="'.$term_obj->name.'">';
	echo '<img src="'.$image_url.'" class="FeaturedImageTax"/>';
	echo '</a>';
}
	
