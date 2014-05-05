<?php
	class WPFeaturedImgCategoriesProWidget extends WP_Widget {
	
		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				'wpfifc_widget', // Base ID
				'Featured Images for Categories', // Name
				array( 'description' => __( 'Display the featured images for categories, tags or any custom taxonomy in a widget area', 'text_domain' ), ) // Args
			);
		}
	
		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			echo $args['before_widget'];
			$wpfifc_license_key = trim(get_option('wpfifc_license_key'));
			$wpfifc_license_status = trim(get_option('wpfifc_license_key_status'));
			if ( !$wpfifc_license_key || $wpfifc_license_status != 'valid' ){
				$wpfifc_license_status = 'invalid';
				delete_option( 'wpfifc_license_key_status' );
				
				$setting_page = admin_url('options-general.php?page=wpfifc_pro_options');
				
				$output = '<div class="FeaturedImageTaxWidget'.$$args['widget_id'].'">'."\n";
				$output .= 'Please go to <a href="'.$setting_page.'">plugin setting page</a> activate the your license first.';
				$output .= '</div>';
				
				echo $output;
				echo $args['after_widget'];
				
				return;
			}
			
			$taxonomy = $instance['wpfifc_taxonomy'];
			$columns = $instance['wpfifc_columns'];
			$imagesize = $instance['wpfifc_imagesize'];
			$padding = $instance['wpfifc_padding'];
			$wpfifc_orderby = $instance['wpfifc_orderby'];
			$wpfifc_order = $instance['wpfifc_order'];
			$wpfifc_hideempty = $instance['wpfifc_hideempty'];			
			
			$wpfifc_orderby = $wpfifc_orderby ? $wpfifc_orderby : 'name';
			$wpfifc_order = $wpfifc_order ? $wpfifc_order : 'ASC';
				
			if ( $taxonomy == '' ){
				return;
			}
			$taxonomy_obj = get_taxonomy( $taxonomy );
			if ( !$taxonomy_obj ){
				return;
			}
			//get terms
			$taxonomy_terms = get_terms( $taxonomy, array('orderby' => $wpfifc_orderby, 'order' => $wpfifc_order, 'hide_empty' => $wpfifc_hideempty) );
			if ( !$taxonomy_terms || count($taxonomy_terms) < 1 ){
				return;
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
			//get columns
			if ( $columns == 0 ){
				$columns = get_option('wpfifc_default_columns'); 	
			}
			//caculate column width
			$column_width = floor(100 / $columns);
			
			$output = '<div class="FeaturedImageTaxWidget'.$args['widget_id'].'">'."\n";
			$images_str = '';
			$column_item = 0;
			foreach( $taxonomy_terms as $term ){
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
										</a>
									</div>'."\n";
					$column_item++;
					if ( $column_item >= $columns ){
						$column_item = 0;
						$images_str .= "\n".'<div style="clear:both;"></div>'."\n";
					}
				}
			}
			$output .= $images_str;
			$output .= '</div>'."\n";
			
			echo $output;
			
			
			echo $args['after_widget'];
		}
	
		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['wpfifc_taxonomy'] = strip_tags( $new_instance['wpfifc_taxonomy'] );
			$instance['wpfifc_columns'] = strip_tags( $new_instance['wpfifc_columns'] );
			$instance['wpfifc_imagesize'] = strip_tags( $new_instance['wpfifc_imagesize'] );
			$instance['wpfifc_padding'] = strip_tags( $new_instance['wpfifc_padding'] );
			$instance['wpfifc_orderby'] = strip_tags( $new_instance['wpfifc_orderby'] );
			$instance['wpfifc_order'] = strip_tags( $new_instance['wpfifc_order'] );
			$instance['wpfifc_hideempty'] = intval( $new_instance['wpfifc_hideempty'] );
	
			return $instance;
		}
	
		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
				$wpfifc_taxonomy = isset( $instance[ 'wpfifc_taxonomy' ] ) ? $instance[ 'wpfifc_taxonomy' ] : '';
				$wpfifc_columns = isset( $instance[ 'wpfifc_columns' ] ) ? $instance[ 'wpfifc_columns' ] : 1;
				$wpfifc_imagesize = isset( $instance[ 'wpfifc_imagesize' ] ) ? $instance[ 'wpfifc_imagesize' ] : 'thumbnail';
				$wpfifc_padding = isset( $instance[ 'wpfifc_padding' ] ) ? $instance[ 'wpfifc_padding' ] : 0;
				$wpfifc_orderby = isset( $instance[ 'wpfifc_orderby' ] ) ? $instance[ 'wpfifc_orderby' ] : 'name';
				$wpfifc_order = isset( $instance[ 'wpfifc_order' ] ) ? $instance[ 'wpfifc_order' ] : 'ASC';
				$wpfifc_hideempty = isset( $instance[ 'wpfifc_hideempty' ] ) ? $instance[ 'wpfifc_hideempty' ] : 0;
				
				
				$args = array( 'public' => true );
				$output = 'objects';
				$all_taxes = get_taxonomies( $args, $output );
				
				$image_sizes = get_intermediate_image_sizes();
				
				$wpfifc_license_key = trim(get_option('wpfifc_license_key'));
				$wpfifc_license_status = trim(get_option('wpfifc_license_key_status'));
				if ( !$wpfifc_license_key || $wpfifc_license_status != 'valid' ){
					$wpfifc_license_status = 'invalid';
					delete_option( 'wpfifc_license_key_status' );
					
					$setting_page = admin_url('options-general.php?page=wpfifc_pro_options');
					echo '<p>Please go to <a href="'.$setting_page.'">plugin setting page</a> activate the your license first.</p>';
					return;
				}
			?>
            <p>Taxonomy:<br />
                <select name="<?php echo $this->get_field_name( 'wpfifc_taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_taxonomy' ); ?>" style="width:150px;">
					<?php foreach( $all_taxes as $taxonomy ): ?>
                    <option value="<?php echo $taxonomy->name ?>" <?php if( $wpfifc_taxonomy == $taxonomy->name ){ echo ' selected="selected"'; } ?>><?php echo $taxonomy->name ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
			<p>Columns:<br />
               <select name="<?php echo $this->get_field_name( 'wpfifc_columns' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_columns' ); ?>" style="width:150px;">
                    <option value="1"<?php if ($wpfifc_columns == 1) echo ' selected="selected"' ?>>1</option>	
                    <option value="2"<?php if ($wpfifc_columns == 2) echo ' selected="selected"' ?>>2</option>	
                    <option value="3"<?php if ($wpfifc_columns == 3) echo ' selected="selected"' ?>>3</option>	
                    <option value="4"<?php if ($wpfifc_columns == 4) echo ' selected="selected"' ?>>4</option>	
                    <option value="5"<?php if ($wpfifc_columns == 5) echo ' selected="selected"' ?>>5</option>	
                    <option value="6"<?php if ($wpfifc_columns == 6) echo ' selected="selected"' ?>>6</option>	
               </select>
            </p>
            <p>Image Size:<br />
               <select name="<?php echo $this->get_field_name( 'wpfifc_imagesize' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_imagesize' ); ?>" style="width:150px;">
               <?php foreach ($image_sizes as $size_name): ?>
               		<option value="<?php echo $size_name ?>" <?php if( $wpfifc_imagesize == $size_name ){ echo ' selected="selected"'; } ?>><?php echo $size_name ?></option>
               <?php endforeach; ?>
               </select>
            </p>
            <p>Order By:<br />
               <select name="<?php echo $this->get_field_name( 'wpfifc_orderby' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_orderby' ); ?>" style="width:75px;">
                    <option value="name"<?php if ($wpfifc_orderby == 'name') echo ' selected="selected"' ?>>name</option>	
                    <option value="slug"<?php if ($wpfifc_orderby == 'slug') echo ' selected="selected"' ?>>slug</option>	
					<option value="id"<?php if ($wpfifc_orderby == 'id') echo ' selected="selected"' ?>>id</option>	
                    <option value="count"<?php if ($wpfifc_orderby == 'count') echo ' selected="selected"' ?>>count</option>	
               </select>
               <select name="<?php echo $this->get_field_name( 'wpfifc_order' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_order' ); ?>" style="width:60px; margin-left:10px;">
                    <option value="ASC"<?php if ($wpfifc_order == 'ASC') echo ' selected="selected"' ?>>ASC</option>	
                    <option value="DESC"<?php if ($wpfifc_order == 'DESC') echo ' selected="selected"' ?>>DESC</option>	
               </select>
            </p>
            <p>Hide Empty:<br />
               <select name="<?php echo $this->get_field_name( 'wpfifc_hideempty' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_hideempty' ); ?>" style="width:150px;">
                    <option value="0"<?php if ($wpfifc_hideempty == 0) echo ' selected="selected"' ?>>false</option>	
                    <option value="1"<?php if ($wpfifc_hideempty == 1) echo ' selected="selected"' ?>>true</option>	
               </select>
            </p>
            <p>Padding around image:
               <input name="<?php echo $this->get_field_name( 'wpfifc_padding' ); ?>" id="<?php echo $this->get_field_id( 'wpfifc_padding' ); ?>" value="<?php echo $wpfifc_padding; ?>" size="1" maxlength="1" />
            </p>
            <?php
		}
	} // class