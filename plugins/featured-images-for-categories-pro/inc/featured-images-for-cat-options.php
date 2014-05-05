<?php

// ********** OPTIONS PAGE **********
// **********************************

function wpfifc_pro_options_shown() {
	$wpfifc_license_key = trim(get_option('wpfifc_license_key'));
	$wpfifc_license_status = trim(get_option('wpfifc_license_key_status'));
	if ( !$wpfifc_license_key || $wpfifc_license_status != 'valid' ){
		$wpfifc_license_status = 'invalid';
		delete_option( 'wpfifc_license_key_status' );
	}
  
	$readOnlyStr = ''; 
	$action = $_SERVER["REQUEST_URI"];
	if ( $wpfifc_license_status !== false && $wpfifc_license_status == 'valid' ) {
		$readOnlyStr = 'readonly';
		$action = 'options.php';
	}
?>
    <div class="wrap">
        <img src="<?PHP echo plugins_url(); ?>/featured-images-for-categories-pro/images/help-for-wordpress-small.png" alt="Help For WordPress Logo" style="float:left;" />
        <h2 style="padding:10px 0 10px 10px; font-size:26px; font-weight: normal;">Featured Images for Categories Pro</h2>
    	<form action="<?php echo $action; ?>" method="POST" id="wpls_settings">
        <?php 
			if ( $wpfifc_license_status !== false && $wpfifc_license_status == 'valid' ) {
				settings_fields( 'wpfifc-settings' );
			}
	    ?>
        <h4>Plugin Licence Activation</h4>
    	<p>In the field below please enter your license key to activate this plugin</p>
    	<p>
        	<input id="wpfifc_license_key_id" name="wpfifc_license_key" type="text" value="<?php echo $wpfifc_license_key; ?>" size="50" <?php echo $readOnlyStr; ?> />
			<?php
            if( $wpfifc_license_status !== false && $wpfifc_license_status == 'valid' ) {
                echo '<span style="color:green;">Active</span>';
                echo '<input type="submit" class="button-secondary" name="wpfifc_license_deactivate" value="Deactivate License" style="margin-left:20px;" />';
            }else{
                if ($wpfifc_license_key !== false && strlen($wpfifc_license_key) > 0) { 
                    echo '<span style="color:red;">Inactive</span>'; 
                }
                echo '<input type="submit" class="button-secondary" name="wpfifc_license_activate" value="Activate License" style="margin-left:20px;" />';
            }
            wp_nonce_field( 'wpfifc_license_key_nonce', 'wpfifc_license_key_nonce' ); 
            ?>	
        </p>
        <?php 
		if ( $wpfifc_license_status !== false && $wpfifc_license_status == 'valid' ) { 
			$padding = get_option('wpfifc_image_padding', 0); 
			$columns = get_option('wpfifc_default_columns'); 
			$saved_size = get_option('wpfifc_default_size');
			$image_sizes = get_intermediate_image_sizes();
		?>
        <h3>Plugin Settings</h3>

    	<table>
        	<tr style="width:160px;">
            	<td>
                	<input name="wpfifc_image_padding" type="text" id="wpfifc_image_padding_id" value="<?php echo $padding; ?>" style="width:20px;" maxlength="1" />&nbsp;&nbsp;
                </td>
                <td>Number of px to place around the image when output.</td>
            </tr>
        	<tr>
            	<td style="width:160px;">
                	<select name="wpfifc_default_columns" id="wpfifc_default_columns_id" style="width:150px;">
                        <option value="1"<?php if ($columns == 1) echo ' selected="selected"' ?>>1</option>	
                        <option value="2"<?php if ($columns == 2) echo ' selected="selected"' ?>>2</option>	
                        <option value="3"<?php if ($columns == 3) echo ' selected="selected"' ?>>3</option>	
                        <option value="4"<?php if ($columns == 4) echo ' selected="selected"' ?>>4</option>	
                        <option value="5"<?php if ($columns == 5) echo ' selected="selected"' ?>>5</option>	
                        <option value="6"<?php if ($columns == 6) echo ' selected="selected"' ?>>6</option>	
                    </select>
                </td>
                <td>
		            Choose the number of columns for the output. ( You can override this in the shortcode )     
                </td>
            </tr>
            <tr>
            	<td style="width:160px;">
                	<select name="wpfifc_default_size" id="wpfifc_default_size_id" style="width:150px;">
                        <?php foreach ($image_sizes as $size_name): ?>
                        <option value="<?php echo $size_name ?>" <?php if( $saved_size == $size_name ){ echo ' selected="selected"'; } ?>><?php echo $size_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
		            Choose the registered image size for the output. ( You can override this in the shortcode )     
                </td>
            </tr>
        </table>
        <?php
		if(defined('PARENT_THEME_NAME') && PARENT_THEME_NAME == 'Genesis'){
		?>
		<h4>Genesis Framework Settings</h4>
		<p>These settings are available because you're running a Genesis Framework child theme.</p>
        <table>
        	<tr>
            	<td>Display featured images on category, tag and custom taxonomy archive pages, choose where you would like to enable this feature.</td>
            </tr>
            <tr>
            	<td>
            <?php
				$args = array( 'public' => true );
				$output = 'objects';
				$all_taxes = get_taxonomies( $args, $output );
				$saved_genesis_taxonomies = get_option('wpfifc_genesis_taxonomy', array());
				$saved_genesis_postion = get_option('wpfifc_genesis_position', 'left');
				
				foreach( $all_taxes as $taxonomy ): 
					$checked_str = '';
					if (is_array($saved_genesis_taxonomies) && count($saved_genesis_taxonomies) > 0){
						if(in_array($taxonomy->name, $saved_genesis_taxonomies)){
							$checked_str = ' checked="checked"';
						}
					}
				?>
                <input type="checkbox" name="wpfifc_genesis_taxonomy[]" value="<?php echo $taxonomy->name ?>" <?php echo $checked_str; ?> />&nbsp;<?php echo $taxonomy->name ?>
                <br />
                <?php 
                endforeach;
                ?>
                </td>
            </tr>
            <tr>
            	<td><br />Set position</td>
            </tr>
            <tr>
            	<td>
                	<select name="wpfifc_genesis_position" id="wpfifc_genesis_position_id" style="width:150px;">
                        <option value="left"<?php if ($saved_genesis_postion == 'left') echo ' selected="selected"' ?>>left</option>	
                        <option value="right"<?php if ($saved_genesis_postion == 'right') echo ' selected="selected"' ?>>right</option>	
                    </select>
               </td>
            </tr>            
        </table>
		<?php
		}
		?>
        <table>
        	<tr>
            	<td colspan="2"><p style="margin-top: 20px"><button class="button-primary" type="submit" id="wpls_admin_submit">Save Settings</button></p></td>
            </tr>
        </table>
		<?php 
        	} //end of if ( $wpfifc_license_status !== false && $wpfifc_license_status == 'valid' ) { 
        ?>
        </form>
        
        <h3>Quick Start Guide</h3>
    	
    	<p>Once activated this plugin will add the ability to assign a featured image to WordPress categories, tags and custom taxonomies </p>
    	
    	
    	<p>Visit the <a href="<?PHP echo $h4wp_admin_url;?>edit-tags.php?taxonomy=category">Category page here</a> in your dashboard to see the new featured images option for each category.</p>
    	
    	<p>You can assign a featured image to a category ( tag or taxonomy ) when editing it (ie it has to be created first then you edit it), simply edit the category and click 'Set featured image'.</p>
    	
    	<h4>Display featured images with a shortcode</h4>
    	<p>To display featured images for categories or tags on a page or post in your WordPress site use this shortcode.</p>
    	<p>
	    	[FeaturedImagesCat taxonomy='category' columns='3']
	    	
    	</p>
    	<p>There are a lot of options for shortcodes, visit the  <a target="_blank" href="http://helpforwp.com/plugins/featured-images-for-categories/?utm_source=FIMAGESFCATEGORIES&utm_medium=Plugin&utm_campaign=FIMAGESFCATEGORIES">plugin documentation page at HelpForWP.com to view detailed documents.</a></p>
    	
    	<h4>Display featured images with a widget</h4>
    	<p>Visit the <a href="<?PHP echo $h4wp_admin_url;?>widgets.php">Widget section</a> of your WordPress Dashboard and you'll see two new widgets: "Featured Images for Categories" & "Featured Images for Categories - Term"</p>
    	<p>
    	These two widgets allow you to display a full list of categories or one specific term from a category. Visit the <a target="_blank" href="http://helpforwp.com/plugins/featured-images-for-categories/?utm_source=FIMAGESFCATEGORIES&utm_medium=Plugin&utm_campaign=FIMAGESFCATEGORIES">plugin documentation page here</a> to view more details on the use of these widgets.</p> 
    	</p>
    	
  
		<?php
            global $_wpfifc_messager;
            
            $_wpfifc_messager->eddslum_plugin_option_page_update_center();
        ?>
        
    </div>
<?php
}
?>


