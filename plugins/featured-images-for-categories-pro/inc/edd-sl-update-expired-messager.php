<?php

class EddSLUpdateExpiredMessager {
	
	var $_eddslum_plugin_name = '';
	var $_eddslum_plugin_download_id = 0;
	var $_eddslum_plugin_folder = '';
	var $_eddslum_plugin_file = '';
	var $_eddslum_plugin_version = '';
	var $_eddslum_plugin_home_url = '';
	var $_eddslum_plugin_sell_page_url = '';
	var $_eddslum_plugin_author = '';
	var $_eddslum_plugin_option_menu = '';
	var $_eddslum_plugin_license_key_opiton_name = '';
	var $_eddslum_plugin_license_stats_option_name = '';
	var $_eddslum_plugin_slug = '';
	//for js & ajax field id
	var $_eddslum_plugin_id_str = '';
	
	var $_eddslum_license_page_URL = 'http://helpforwp.com/licenses/';
	var $_eddslum_plugin_checkout_page = 'http://helpforwp.com/checkout/';
	
	public function __construct( $arg ) {
		
		$this->_eddslum_plugin_name = $arg['plugin_name'];
		$this->_eddslum_plugin_download_id = $arg['plugin_download_id'];
		$this->_eddslum_plugin_folder = $arg['plugin_folder'];
		$this->_eddslum_plugin_file = $arg['plugin_file'];
		$this->_eddslum_plugin_version = $arg['plugin_version'];
		$this->_eddslum_plugin_author = $arg['plugin_author'];
		$this->_eddslum_plugin_option_menu = $arg['plugin_option_menu'];
		$this->_eddslum_plugin_license_key_opiton_name = $arg['plugin_license_key_opiton_name'];
		$this->_eddslum_plugin_license_stats_option_name = $arg['plugin_license_status_option_name'];
		$this->_eddslum_plugin_home_url = trailingslashit($arg['plugin_home_url']);
		$this->_eddslum_plugin_sell_page_url = $arg['plugin_sell_page_url'];
		
		$this->_eddslum_plugin_slug = basename( $this->_eddslum_plugin_file, '.php');
		
		$this->_eddslum_plugin_id_str = str_replace('-', '_', $this->_eddslum_plugin_folder);
		if( is_admin() ){
			add_action("wp_ajax_eddslum_dismiss_upgrade_".$this->_eddslum_plugin_id_str, array($this, 'eddslum_dashboard_dismiss_upgrade'));
			add_action("wp_ajax_eddslum_dismiss_expired_".$this->_eddslum_plugin_id_str, array($this, 'eddslum_dashboard_dismiss_expired'));	
			add_action("wp_ajax_eddslum_dismiss_invalid_".$this->_eddslum_plugin_id_str, array($this, 'eddslum_dashboard_dismiss_invalid'));		
			
			add_action('admin_notices', array($this, 'eddslum_dashboard_message'));
		}
	}
	
	
	
	function eddslum_dashboard_message(){
		global $pagenow;
		if( 'index.php' !== $pagenow ){
			return;
		}
		$license_key = get_option( $this->_eddslum_plugin_license_key_opiton_name );
		if( !$license_key ){
			return;
		}
		//check if license key expired or not
		$check_license_key_return = $this->eddslum_check_license_key();
		if( !$check_license_key_return ){
			return;
		}
		if( $check_license_key_return == 'expired' ){
			$this->eddslum_dashboard_show_expired_message();
			return;
		}else if( $check_license_key_return != 'valid' ){
			delete_option( $this->_eddslum_plugin_license_stats_option_name );
			$this->eddslum_dashboard_show_invalid_message();
			return;
		}
		update_option("_eddslum_".$this->_eddslum_plugin_id_str."_expired_dismissed", 0);
		update_option("_eddslum_".$this->_eddslum_plugin_id_str."_invalid_dismissed", 0);
		
		//valid & not expired license, get latest info
		$return_from_server = $this->eddslum_get_plugin_latest_info();
		if( !$return_from_server || !is_object( $return_from_server ) ){
			return;
		}
		if( version_compare( $this->_eddslum_plugin_version, $return_from_server->new_version, '>=' ) ) {
			return;
		}
		$this->eddslum_dashboard_show_upgrade_message( $return_from_server->new_version );
	}
	
	function eddslum_dashboard_show_upgrade_message( $new_version ){
		//don't display a message if use has dismissed the message the plugin's current version
		$dismissed_versions_array = get_option("_eddslum_".$this->_eddslum_plugin_id_str."_upgrades_dismissed");
		if( !empty($dismissed_versions_array) && in_array($new_version, $dismissed_versions_array) ){
			return;
		}
		
		$details_page = admin_url('options-general.php?page='.$this->_eddslum_plugin_option_menu.'#update');
		$message = "There is an update available for <b>".$this->_eddslum_plugin_name."</b> <a href='".$details_page."'>View Details</a>";
		?>
		<div class='updated' style='padding:15px; position:relative;' id='eddslum_dashboard_message_<?php echo $this->_eddslum_plugin_id_str; ?>'>
			<?php echo $message ?>
			<a href="javascript:void(0);" onclick="eddslum_dismiss_message_<?php echo $this->_eddslum_plugin_id_str; ?>();" style='float:right;'>Dismiss</a>
		</div>
		<script type="text/javascript">
		function eddslum_dismiss_message_<?php echo $this->_eddslum_plugin_id_str; ?>(){
			jQuery("#eddslum_dashboard_message_<?php echo $this->_eddslum_plugin_id_str; ?>").slideUp();
			jQuery.post(ajaxurl, {action:"eddslum_dismiss_upgrade_<?php echo $this->_eddslum_plugin_id_str; ?>", version:"<?php echo $new_version; ?>"});
		}
		</script>
		<?php
	}
	
	function eddslum_dashboard_show_expired_message(){
		$is_dismissed = get_option("_eddslum_".$this->_eddslum_plugin_id_str."_expired_dismissed", 0);
		if( $is_dismissed ){
			return;
		}
		$license_key = get_option( $this->_eddslum_plugin_license_key_opiton_name );
		$message = 'Updates are not available for <b>'.$this->_eddslum_plugin_name.'</b> as your license has expired. <a href="'.$this->_eddslum_license_page_URL.'" target="_blank">Learn more here</a>';
		$update_license_url = add_query_arg( array('download_id' => $this->_eddslum_plugin_download_id, 'edd_license_key' => $license_key), $this->_eddslum_plugin_checkout_page );
		?>
		<div class='updated' style='padding:15px; position:relative;' id='eddslum_dashboard_expired_message_<?php echo $this->_eddslum_plugin_id_str; ?>'>
			<?php echo $message ?>
            &nbsp;&nbsp;
            <a class='button-primary' href='<?php echo $update_license_url; ?>' target="_blank">Update license</a>
            <a href="javascript:void(0);" onclick="eddslum_dismiss_expired_message_<?php echo $this->_eddslum_plugin_id_str; ?>();" style='float:right;'>Dismiss</a>
		</div>
		<script type="text/javascript">
		function eddslum_dismiss_expired_message_<?php echo $this->_eddslum_plugin_id_str; ?>(){
			jQuery("#eddslum_dashboard_expired_message_<?php echo $this->_eddslum_plugin_id_str; ?>").slideUp();
			jQuery.post(ajaxurl, {action:"eddslum_dismiss_expired_<?php echo $this->_eddslum_plugin_id_str; ?>"});
		}
		</script>
		<?php
	}
	
	function eddslum_dashboard_show_invalid_message(){
		$is_dismissed = get_option("_eddslum_".$this->_eddslum_plugin_id_str."_invalid_dismissed", 0);
		if( $is_dismissed ){
			return;
		}
		$message = 'Updates are not available for <b>'.$this->_eddslum_plugin_name.'</b> as your license is invalid. <a href="'.$this->_eddslum_license_page_URL.'" target="_blank">Learn more here</a>';
		?>
		<div class='updated' style='padding:15px; position:relative;' id='eddslum_dashboard_invalid_message_<?php echo $this->_eddslum_plugin_id_str; ?>'>
			<?php echo $message ?>
            &nbsp;&nbsp;
            <a class='button-primary' href='<?php echo $this->_eddslum_plugin_sell_page_url; ?>' target="_blank">Buy a license</a>
			<a href="javascript:void(0);" onclick="eddslum_dismiss_invalid_message_<?php echo $this->_eddslum_plugin_id_str; ?>();" style='float:right;'>Dismiss</a>
		</div>
		<script type="text/javascript">
		function eddslum_dismiss_invalid_message_<?php echo $this->_eddslum_plugin_id_str; ?>(){
			jQuery("#eddslum_dashboard_invalid_message_<?php echo $this->_eddslum_plugin_id_str; ?>").slideUp();
			jQuery.post(ajaxurl, {action:"eddslum_dismiss_invalid_<?php echo $this->_eddslum_plugin_id_str; ?>"});
		}
		</script>
		<?php
	}
	
	function eddslum_dashboard_dismiss_upgrade(){
       $dismissed_versions_array = get_option("_eddslum_".$this->_eddslum_plugin_id_str."_upgrades_dismissed");
        if( !is_array( $dismissed_versions_array ) ){
            $dismissed_versions_array = array();
		}

        $dismissed_versions_array[] = $_POST["version"];
        update_option("_eddslum_".$this->_eddslum_plugin_id_str."_upgrades_dismissed", $dismissed_versions_array);
    }
	
	function eddslum_dashboard_dismiss_expired(){
		update_option("_eddslum_".$this->_eddslum_plugin_id_str."_expired_dismissed", 1);
	}
	
	function eddslum_dashboard_dismiss_invalid(){
		update_option("_eddslum_".$this->_eddslum_plugin_id_str."_invalid_dismissed", 1);
	}
	
	/*
	 * This function is used for plugin's option page, call this function on option page.
	 */
	function eddslum_plugin_option_page_update_center(){
		$license_key = get_option( $this->_eddslum_plugin_license_key_opiton_name );
		if( !$license_key ){
			return;
		}
		echo '<a name="update" id="update"></a>'."\n";
    	echo '<h3>Plugin Update Centre</h3>'."\n";
		
		//check if license key expired or not
		$check_license_key_return = $this->eddslum_check_license_key();
		if( !$check_license_key_return ){
			return;
		}
		if( $check_license_key_return == 'expired' ){
			$this->edslum_update_center_show_expired();
			return;
		}else if( $check_license_key_return != 'valid' ){
			delete_option( $this->_eddslum_plugin_license_stats_option_name );
			$this->edslum_update_center_show_invalid();
			return;
		}
		
		//get latest plugin info from server, if there's new version, show new version info
		$return_from_server = $this->eddslum_get_plugin_latest_info();
		if( !$return_from_server || !is_object( $return_from_server ) || version_compare( $this->_eddslum_plugin_version, $return_from_server->new_version, '>=' ) ){
			echo '<p>The version '.$this->_eddslum_plugin_version.' you have is the latest.</p>';
			return;
		}
		$this->edslum_update_center_show_update( $return_from_server );
	}
	
	function edslum_update_center_show_expired(){
		$license_key = get_option( $this->_eddslum_plugin_license_key_opiton_name );
		$message = 'Updates are not available for '.$this->_eddslum_plugin_name.' as your license has expired. <a href="'.$this->_eddslum_license_page_URL.'">Learn more here';
		$update_license_url = add_query_arg( array('download_id' => $this->_eddslum_plugin_download_id, 'edd_license_key' => $license_key), $this->_eddslum_plugin_checkout_page );
	?>
        <div style="padding:20px; border: 1px solid #E6DB55; background-color: #FFFBCC; color: #424242;">
            <p><?php echo $message; ?></p>
            <a class='button-primary' href='<?php echo $update_license_url; ?>' target="_blank">Update license here</a> &nbsp;
        </div>
    <?php
	}
	
	function edslum_update_center_show_invalid(){
		$message = 'Updates are not available for '.$this->_eddslum_plugin_name.' as your license is invalid. <a href="'.$this->_eddslum_license_page_URL.'">Learn more here';
	?>
        <div style="padding:20px; border: 1px solid #E6DB55; background-color: #FFFBCC; color: #424242;">
            <p><?php echo $message; ?></p>
            <a class='button-primary' href='<?php echo $this->_eddslum_plugin_sell_page_url; ?>' target="_blank">Buy a license</a> &nbsp;
        </div>
    <?php
	}
    
    function edslum_update_center_show_update( $latest_info ){
		//see if WordPress created its update array or not, if the array not exist then can not upgrade automatically
		$only_can_update_manually = false;
		$update_transient = get_site_transient( 'update_plugins' );
		if( empty( $update_transient ) ){
			$only_can_update_manually = true;
		}else{
			//To make the update works it must set the update transient data first, because WordPress will read it to download the package
			$update_transient->response[$this->_eddslum_plugin_folder.'/'.$this->_eddslum_plugin_slug] = $return_from_server;
			set_site_transient( 'update_plugins', $update_transient );
			
			//url to show update automatically
			$plugin_file_with_folder = $this->_eddslum_plugin_folder.'/'.$this->_eddslum_plugin_file;
			$upgrade_url = wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . urlencode($plugin_file_with_folder), 'upgrade-plugin_' . $plugin_file_with_folder);
		}
		?>
        <div style="padding:20px; border: 1px solid #E6DB55; background-color: #FFFBCC; color: #424242;">
            There is a new version of <?php echo $this->_eddslum_plugin_name; ?> available. 
        <?php if( $only_can_update_manually == false ){ ?>
            <p>You can update to the latest version automatically.</p>
            <a class='button-primary' href='<?php echo $upgrade_url; ?>'>Update Automatically</a> &nbsp;
        <?php }else{ //show download manually ?>
            <p>The WordPress automatic update feature is disabled on your site. You may download the latest version and update manually. </p>
            <p>Extract the package and overwrite all files under <b><?php echo plugins_url().'/'.$this->_eddslum_plugin_folder.'/'; ?></b> using FTP </p>
            <?php
            if( isset($latest_info->package) ){
            	$download_page_url = $latest_info->package;
            	$target = "_self";
            }else{
				$download_page_url = 'http://helpforwp.com/wp-login.php';
				$target = "_blank";
            }
            ?>
            <a class='button-primary' href='<?php echo $download_page_url; ?>' target="<?php echo $target; ?>">Download latest package</a> &nbsp;
        <?php } ?>
        </div>
        <div style="margin-top: 10px; padding: 20px; border:1px solid #ccc;">
            <h4>What's New in <?php echo $this->_eddslum_plugin_name.' '.$latest_info->new_version; ?></h4>
            <?php if( isset($latest_info->sections['changelog']) ) echo $latest_info->sections['changelog']; ?>
        </div>
    <?php
	}	
	
	
	function eddslum_get_plugin_latest_info() {
		$license_key = get_option( $this->_eddslum_plugin_license_key_opiton_name );
		$api_params = array( 
			'edd_action' 	=> 'get_version',
			'license' 		=> $license_key, 
			'name' 			=> $this->_eddslum_plugin_name,
			'slug' 			=> $this->_eddslum_plugin_slug,
			'author'		=> $this->_eddslum_plugin_author
		);
		$request = wp_remote_post( $this->_eddslum_plugin_home_url, array( 'timeout' => 15, 'ssverify' => false, 'body' => $api_params ) );
		if ( is_wp_error( $request ) ){
			return false;
		}
		$request = json_decode( wp_remote_retrieve_body( $request ) );
		if( $request ){
			$request->sections = maybe_unserialize( $request->sections );
		}
		
		return $request;
	}
	
	function eddslum_check_license_key() {
		$license_key = get_option( $this->_eddslum_plugin_license_key_opiton_name );
		$api_params = array( 
			'edd_action' 	=> 'check_license',
			'license' 		=> $license_key, 
			'item_name' 	=> urlencode($this->_eddslum_plugin_name)
		);
		$request = wp_remote_post( $this->_eddslum_plugin_home_url, array( 'timeout' => 15, 'ssverify' => false, 'body' => $api_params ) );
		if ( is_wp_error( $request ) ){
			return false;
		}
		$license_data = json_decode( wp_remote_retrieve_body( $request ) );
	
		return $license_data->license;
	}
}
