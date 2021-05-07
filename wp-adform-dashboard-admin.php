<?php

/**
 * Enqueue Admin Scripts
 */
function af_admin_scripts(){
	wp_enqueue_style( 'af-admin-style',  plugin_dir_url( __FILE__ ) . '/assets/css/af-admin-style.css', array(),'1.0.0');
	wp_enqueue_script( 'af-admin-ajax-script', plugin_dir_url( __FILE__ ) . '/assets/js/af-admin-ajax-script.js',  array("jquery"), "1.0.0", true );
	$title_nonce = wp_create_nonce( 'title_example' );
	wp_localize_script(
    'af-admin-ajax-script',
    'af_my_admin_ajax_obj',
    array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $title_nonce,
    )
);
}
add_action( "admin_enqueue_scripts", "af_admin_scripts" );
 
/**
 * Adform Menu 
 */
add_action('admin_menu', 'af_admin_menu');
	function af_admin_menu() {
		/*
		 * Register New menu in admin side 
		 */
		add_menu_page( 
				'Adform Dashboard',
				'Adform', 
				'edit_posts', 
				'af_admin_menu', 
				'af_admin_menu_fn', 
				'dashicons-admin-site-alt3' 

			   );
		
		function af_admin_menu_fn(){
		?>
		<h1> Hii </h1>
		<?php
		}
		
		/* 
		 * Register Submenu : Add Agency
		 */
		add_submenu_page(
			'af_admin_menu',
			'Add Agency',
			'Add Agency',
			'edit_posts',
			'af_sm_add_agency',
			'af_sm_add_agency_fn'
		);
		function af_sm_add_agency_fn(){
			
				if( isset($_POST["af_add_user_btn"]) ){

					$af_uname = $_POST["af_uname"];
					$af_pass = $_POST["af_pass"];
					$af_email = $_POST["af_email"];
					$af_client_id = $_POST["af_client_id"];
					$af_client_secert = $_POST["af_client_secert"];
					$af_fname = $_POST["af_fname"];
					$af_lname = $_POST["af_lname"];
					$af_dname = $_POST["af_dname"];

				
				
					global $wpdb;

					/**
					 * Check agency with same secert key exisist
					 */
					$af_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}usermeta WHERE meta_key = 'af_client_id' and meta_value='".$af_client_id."'" );

					
					if( $af_results != null || !empty( $af_results ) ){
						
						$af_user_id = wp_insert_user( array(
							'user_login' => $af_uname,
							'user_pass' => $af_pass,
							'user_email' => $af_email,
							'first_name' => $af_fname,
							'last_name' => $af_lname,
							'display_name' => $af_dname,
							'role' => 'subscriber'
						  ));
						  //check if there are no errors
						if ( ! is_wp_error( $af_user_id ) ) {

							update_user_meta( $af_user_id, "af_client_id", $af_client_id );
							update_user_meta( $af_user_id, "af_client_secert", $af_client_secert );
							update_user_meta( $af_user_id, "af_user_type", "af_agency" );

							?>
							 <div class="notice notice-success is-dismissible">
								<p><?php _e( 'User Creation Sucessful', 'user_sucess_flag' ); ?></p>
							</div>
							<?php
						}
						else{
							
							?>
							<div class="notice notice-error is-dismissible">
								<p><?php 
										
									if( array_key_exists( "existing_user_login",$af_user_id->errors) ){
										print_r ($af_user_id->errors["existing_user_login"][0]); 
									}
									elseif( array_key_exists( "existing_user_email",$af_user_id->errors) ){
										print_r ($af_user_id->errors["existing_user_email"][0]);
									}
									else{
										echo "User Creation Failed.";
									}
									

								 ?></p>
							</div>
							<?php
						}
					}

					else{

					$table_name = $wpdb->prefix . 'adform_website_stat';
					$af_website_post_body = '{
						"dimensions": [
						"campaign","campaignID","client","clientID","rtbDomain"
						],
						"metrics": [
						"clicks","impressions"
						],
						"filter": {
						"date": "thisYear"
						}
						}';
					$af_stat_data_arr = af_report_stat( $af_client_id, $af_client_secert, $af_website_post_body );

					
					$table_name_device_type = $wpdb->prefix . 'adform_device_type_stat';
					$af_deviceType_post_body = '{
						"dimensions": [
						"campaign","campaignID","client","clientID","deviceType"
						],
						"metrics": [
						"clicks","impressions"
						],
						"filter": {
						"date": "thisYear"
						}
					   }';
					$af_stat_data_deviceType_arr = af_report_stat( $af_client_id, $af_client_secert, $af_deviceType_post_body );

					$table_name_os = $wpdb->prefix . 'adform_os_stat';
					$af_os_post_body = '{
						"dimensions": [
						"campaign","campaignID","client","clientID","operatingSystem"
						],
						"metrics": [
						"clicks","impressions"
						],
						"filter": {
						"date": "thisYear"
						}
					   }';
					$af_stat_data_os_arr = af_report_stat( $af_client_id, $af_client_secert, $af_os_post_body );

					$table_name_reportStat = $wpdb->prefix . 'adform_report_stat';
					$af_reportStat_post_body1 = '{
						"dimensions": [
						"campaign","campaignID","client","clientID","date"
						],
						"metrics": [
						"clicks","impressions","ctr","ecpm","ecpc","ecpa","rtbWinRate","rtbBids","viewImpressions","viewImpressionsPercent"
						],
						"filter": {
						"date": "thisYear"
						}
					   }';

					   $af_reportStat_post_body2 = '{
						"dimensions": [
						"campaign","campaignID","client","clientID","date"
						],
						"metrics": [
						"cost","avgEngagementTime","avgViewabilityTime","conversions","pageviews","bounceRate"
						],
						"filter": {
						"date": "thisYear"
						}
					   }';


					$af_reportStat_arr1 = af_report_stat( $af_client_id, $af_client_secert, $af_reportStat_post_body1 );
					$af_reportStat_arr2 = af_report_stat( $af_client_id, $af_client_secert, $af_reportStat_post_body2 );

					if( $af_stat_data_arr!="error" && $af_stat_data_deviceType_arr != "error" && $af_stat_data_os_arr !="error" && $af_reportStat_arr2 !="error" && $af_reportStat_arr1 !="error" ){

						$af_user_id = wp_insert_user( array(
							'user_login' => $af_uname,
							'user_pass' => $af_pass,
							'user_email' => $af_email,
							'first_name' => $af_fname,
							'last_name' => $af_lname,
							'display_name' => $af_dname,
							'role' => 'subscriber'
						  ));
						  //check if there are no errors
						if ( ! is_wp_error( $af_user_id ) ) {
						
							update_user_meta( $af_user_id, "af_client_id", $af_client_id );
							update_user_meta( $af_user_id, "af_client_secert", $af_client_secert );
							update_user_meta( $af_user_id, "af_user_type", "af_agency" );
	
							
	
							if( array_key_exists( "reportData" , $af_stat_data_arr ) && array_key_exists( "reportData" , $af_stat_data_deviceType_arr ) && array_key_exists( "reportData" , $af_stat_data_os_arr ) && array_key_exists( "reportData" , $af_reportStat_arr1 ) && array_key_exists( "reportData" , $af_reportStat_arr2 ) ){
								
								for( $i=0; $i< sizeof(  $af_stat_data_arr["reportData"]["rows"] ); $i++){
						
	
									$db_campaign_id = $af_stat_data_arr["reportData"]["rows"][$i][1];
									$db_campaign_name = $af_stat_data_arr["reportData"]["rows"][$i][0];
									$advertiser_id = $af_stat_data_arr["reportData"]["rows"][$i][3];
									$advertiser_name = $af_stat_data_arr["reportData"]["rows"][$i][2];
									$impression = $af_stat_data_arr["reportData"]["rows"][$i][6];
									$click = $af_stat_data_arr["reportData"]["rows"][$i][5];
									$rtb_website = $af_stat_data_arr["reportData"]["rows"][$i][4];
					
									$wpdb->insert( 
										$table_name, 
										array( 
											'campaign_id' => $db_campaign_id, 
											'campaign_name' => $db_campaign_name, 
											'advertiser_id' => $advertiser_id,
											'advertiser_name' => $advertiser_name,
											'impression' => $impression,
											'click' => $click, 
											'rtb_website' => $rtb_website,
										) 
									);
					
					
								}

								for( $i=0; $i< sizeof(  $af_stat_data_deviceType_arr["reportData"]["rows"] ); $i++){

									$db_campaign_id = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][1];
									$db_campaign_name = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][0];
									$advertiser_id = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][3];
									$advertiser_name = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][2];
									$impression = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][6];
									$click = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][5];
									$device_type = $af_stat_data_deviceType_arr["reportData"]["rows"][$i][4];

									$wpdb->insert( 
										$table_name_device_type, 
										array( 
											'campaign_id' => $db_campaign_id, 
											'campaign_name' => $db_campaign_name, 
											'advertiser_id' => $advertiser_id,
											'advertiser_name' => $advertiser_name,
											'impression' => $impression,
											'click' => $click, 
											'device' => $device_type,
										) 
									);

								}

								for( $i=0; $i< sizeof( $af_stat_data_os_arr["reportData"]["rows"] ); $i++){

									$db_campaign_id = $af_stat_data_os_arr["reportData"]["rows"][$i][1];
									$db_campaign_name = $af_stat_data_os_arr["reportData"]["rows"][$i][0];
									$advertiser_id = $af_stat_data_os_arr["reportData"]["rows"][$i][3];
									$advertiser_name = $af_stat_data_os_arr["reportData"]["rows"][$i][2];
									$impression = $af_stat_data_os_arr["reportData"]["rows"][$i][6];
									$click = $af_stat_data_os_arr["reportData"]["rows"][$i][5];
									$os = $af_stat_data_os_arr["reportData"]["rows"][$i][4];

									$wpdb->insert( 
										$table_name_os, 
										array( 
											'campaign_id' => $db_campaign_id, 
											'campaign_name' => $db_campaign_name, 
											'advertiser_id' => $advertiser_id,
											'advertiser_name' => $advertiser_name,
											'impression' => $impression,
											'click' => $click, 
											'os' => $os,
										) 
									);

								}


								for( $i=0; $i< sizeof( $af_reportStat_arr1["reportData"]["rows"] ); $i++){

									$db_campaign_id = $af_reportStat_arr1["reportData"]["rows"][$i][1];
									$db_campaign_name = $af_reportStat_arr1["reportData"]["rows"][$i][0];
									$advertiser_id = $af_reportStat_arr1["reportData"]["rows"][$i][3];
									$advertiser_name = $af_reportStat_arr1["reportData"]["rows"][$i][2];
									
									$date =  $af_reportStat_arr1["reportData"]["rows"][$i][4];
									$impression = $af_reportStat_arr1["reportData"]["rows"][$i][6];
									$click = $af_reportStat_arr1["reportData"]["rows"][$i][5];
									$ctr = $af_reportStat_arr1["reportData"]["rows"][$i][7];
									$ecpm = $af_reportStat_arr1["reportData"]["rows"][$i][8];
									$ecpc = $af_reportStat_arr1["reportData"]["rows"][$i][9];
									$ecpa = $af_reportStat_arr1["reportData"]["rows"][$i][10];
									$rtb_winrate =$af_reportStat_arr1["reportData"]["rows"][$i][11];
									$rtb_bids = $af_reportStat_arr1["reportData"]["rows"][$i][12];
									$view_impression = $af_reportStat_arr1["reportData"]["rows"][$i][13];
									$view_impression_percentage = $af_reportStat_arr1["reportData"]["rows"][$i][14];
									$spend = $af_reportStat_arr2["reportData"]["rows"][$i][5];
									$avg_view_time = $af_reportStat_arr2["reportData"]["rows"][$i][7];
									$avg_engage_time= $af_reportStat_arr2["reportData"]["rows"][$i][6];
									$page_view = $af_reportStat_arr2["reportData"]["rows"][$i][9];
									$bounce_rate = $af_reportStat_arr2["reportData"]["rows"][$i][10];
									$conversion = $af_reportStat_arr2["reportData"]["rows"][$i][8];


									$wpdb->insert( 
										$table_name_reportStat, 
										array( 
											'campaign_id' => $db_campaign_id, 
											'campaign_name' => $db_campaign_name, 
											'advertiser_id' => $advertiser_id,
											'advertiser_name' => $advertiser_name,
											'date' => $date,
											'impression' => $impression,
											'click' => $click,
											'ctr' => $ctr,
											'ecpm' => $ecpm,
											'ecpc' => $ecpc,
											'ecpa' => $ecpa,
											'rtb_winrate' => $rtb_winrate,
											'rtb_bids' => $rtb_bids,
											'view_impression' => $view_impression,
											'view_impression_percentage' => $view_impression_percentage,
											'spend' => $spend,
											'avg_view_time' => $avg_view_time,
											'avg_engage_time' => $avg_engage_time,
											'page_view' => $page_view,
											'bounce_rate' => $bounce_rate,
											'conversion' => $conversion,
										) 
									);

								}

								?>
								<div class="notice notice-success is-dismissible">
									<p><?php _e( 'User Creation Sucessful', 'user_sucess_flag' ); ?></p>
								</div>
								<?php

								$af_update_timestamp = gmdate( "Y/m/d/H:i:s" );
								update_option("af_tbl_device_type", $af_update_timestamp);
								update_option("af_tbl_os_stat", $af_update_timestamp);
								update_option("af_tbl_report_stat", $af_update_timestamp);
								update_option("af_tbl_website_stat", $af_update_timestamp);

							}
							else
							{
								
								?>
								<div class="notice notice-error is-dismissible">
									<p><?php _e( 'User is created data is not synced', 'user_fail_flag' ); ?></p>
								</div>
								<?php
							}
						}
						else{
							
							?>
							<div class="notice notice-error is-dismissible">
								<p><?php _e( 'Error in User creation', 'user_fail_flag' ); ?></p>
							</div>
							<?php
						}

					}
					else{
						?>
						<div class="notice notice-error is-dismissible">
							<p><?php _e( 'Unable to fetch agency data from Adform API please try after some time', 'user_fail_flag' ); ?></p>
						</div>
						<?php
						
					}



				}

				}
			?>
				<h2>
					Add Agency
				</h2>
				<form method="post">
					<div class="af_add_user_form">
						<div>
							<label> Username </label>
						</div>
						<div>
							<input type="text" name="af_uname" required>
						</div>
					</div>
					
					
					<div class="af_add_user_form">
						<div>
							<label> Password </label>
						</div>
						<div>
							<input type="password" name="af_pass" required>
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Email </label>
						</div>
						<div>
							<input type="email" name="af_email" required>
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> First Name  </label>
						</div>
						<div>
							<input type="text" name="af_fname" >
							
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Last Name </label>
						</div>
						<div>
							<input type="text" name="af_lname" >
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Display Name </label>
						</div>
						<div>
							<input type="text" name="af_dname" required>
						</div>
					</div>
					
					<div class="af_add_user_form">
						<div>
							<label> Client ID </label>
						</div>
						<div>
							<input type="text" name="af_client_id" id="af_client_id" required>
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Client Secert </label>
						</div>
						<div>
							<input type="text" name="af_client_secert" id="af_client_secert" required>
							<button name="af_verify_client_btn" id="af_verify_client_btn" formnovalidate="true"> Verify </button>
						</div>		
					</div>
					
					<div  class="af_add_user_form af_msg_verify">
						<div></div>
					
					</div>
					<button name="af_add_user_btn" id="af_add_user_agency_btn" disabled> Add New Agency </button>

					<div id="af_add_user_btn_msg"> </div>
					
					
				</form>
			<?php
		}

		/**
		 * Register Sub Menu: Add advertiser
		 */
		add_submenu_page(
			'af_admin_menu',
			'Add Advertiser',
			'Add Advertiser',
			'edit_posts',
			'af_sm_add_advertiser',
			'ad_sm_add_adevrtiser_fn'
		);

		function ad_sm_add_adevrtiser_fn(){
			$args = array( 
				'role' => 'subscriber',
				'meta_key' => 'af_user_type',
				'meta_value' => 'af_agency'
			);
			$af_agency_list = new WP_User_Query( $args );
			$af_agency_details =  $af_agency_list->get_results();
			$af_agency_count = sizeof($af_agency_list->results );

			if( isset($_POST["af_add_user_advertiser_btn"]) ){
				$sel_agency = $_POST["af_select_agency"];
				$sel_advert = $_POST["af_select_advertiser"];
				$af_uname = $_POST["af_uname"];
				$af_pass = $_POST["af_pass"];
				$af_email = $_POST["af_email"];
				$af_fname = $_POST["af_fname"];
				$af_lname = $_POST["af_lname"];
				$af_dname = $_POST["af_dname"];

				$af_user_id = wp_insert_user( array(
					'user_login' => $af_uname,
					'user_pass' => $af_pass,
					'user_email' => $af_email,
					'first_name' => $af_fname,
					'last_name' => $af_lname,
					'display_name' => $af_dname,
					'role' => 'subscriber'
				));

				if ( ! is_wp_error( $af_user_id ) ) {
					echo "sucess";
					update_user_meta( $af_user_id, "af_user_type", "af_advertiser" );
					update_user_meta( $af_user_id, "af_parent_agency", $sel_agency );
					update_user_meta( $af_user_id, "af_advert_id", $sel_advert );
				}
				else{
					echo "fail";
					print_r( $af_user_id );
				}
			}
			
			?>
			<h2>Add Advertiser</h2>
			<form method="post">
				<div class="af_add_user_form">
					<div>
						<label> Select Agency </label>
					</div>
					<div>
						<select name="af_select_agency" id="af_select_agency" required>
							<option value=""> Select </option>
							
							<?php 
							for( $i=0; $i<$af_agency_count; $i++ ){
							?>
								<option value=<?php echo esc_attr( $af_agency_details[$i]->ID ); ?> > <?php echo esc_html( $af_agency_details[$i]->display_name); ?>  </option>
							<?php
							}
							
							?>
						</select>
					</div>		
				</div>

				<div class="af_add_user_form">
					<div>
						<label> Select Advertiser </label>
					</div>
					<div>
						<select name="af_select_advertiser" id="af_select_advertiser" required>
						<option value=""> Select </option>
						</select>
					</div>		
				</div>

				<div class="af_add_user_form">
						<div>
							<label> Username </label>
						</div>
						<div>
							<input type="text" name="af_uname" required>
						</div>
					</div>
					
					
					<div class="af_add_user_form">
						<div>
							<label> Password </label>
						</div>
						<div>
							<input type="password" name="af_pass" required>
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Email </label>
						</div>
						<div>
							<input type="email" name="af_email" required>
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> First Name  </label>
						</div>
						<div>
							<input type="text" name="af_fname" >
							
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Last Name </label>
						</div>
						<div>
							<input type="text" name="af_lname" >
						</div>
					</div>

					<div class="af_add_user_form">
						<div>
							<label> Display Name </label>
						</div>
						<div>
							<input type="text" name="af_dname" required>
						</div>
					</div>

				<button name="af_add_user_advertiser_btn" id="af_add_user_advertiser_btn"> Add New Advertiser </button>
			</form>
			<?php
					
					
			
		}
}


/**
 * AJAX request for fetching advertiser data for add adveritser page 
 */
add_action( 'wp_ajax_my_advertiser_list', 'af_add_advertiser_data' ); 
function af_add_advertiser_data() {

	$af_agency_id = $_POST["_af_agency_id"];
	$af_client_id = get_user_meta( $af_agency_id, "af_client_id", true );
	$af_client_secert = get_user_meta( $af_agency_id, "af_client_secert", true );
	$af_access_token = adform_acc_key( $af_client_id, $af_client_secert );
	$token_type = $af_access_token["token_type"];
	$access_token = $af_access_token["access_token"];

	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.adform.com/v1/buyer/advertisers',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	CURLOPT_HTTPHEADER => array(
		'Authorization: ' . $token_type . ' ' . $access_token
	),
	));

	$response = curl_exec($curl);
	
	curl_close($curl);
	

	
	//$myJSON = json_encode($args);
	wp_send_json( $response );
    wp_die();
}

/**
 * Adform GET Access Token
 */
function adform_acc_key( $af_tok_client_id, $af_tok_client_secert ){
	$af_client_scope = "https://api.adform.com/scope/buyer.advertisers https://api.adform.com/scope/buyer.classifiers.readonly https://api.adform.com/scope/eapi https://api.adform.com/scope/buyer.unified.tags.readonly https://api.adform.com/scope/tracking.advertisertracking.readonly https://api.adform.com/scope/agencies.readonly https://api.adform.com/scope/buyer.campaigns.api.readonly https://api.adform.com/scope/api.publishers.readonly https://api.adform.com/scope/privacy.rti https://api.adform.com/scope/buyer.masterdata https://api.adform.com/scope/crossdevice.meta.advertiser https://api.adform.com/scope/integrations.classifiers.readonly https://api.adform.com/scope/ads.api.readonly  https://api.adform.com/scope/buyer.stats https://api.adform.com/scope/deliveryindications.readonly";
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://id.adform.com/sts/connect/token',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => 'client_id=' . $af_tok_client_id . '&client_secret=' . $af_tok_client_secert . '&grant_type=client_credentials&scope=' . $af_client_scope,
	  CURLOPT_HTTPHEADER => array(
		'Content-Type: application/x-www-form-urlencoded'
	  ),
	));

	$response = curl_exec($curl);
	$access_token_data = json_decode( $response, true );

	curl_close($curl);
	return $access_token_data;
}

/**
 * AJAX for verifying client secert and client id in agency registration page
 */
add_action( 'wp_ajax_af_verify_agency', 'af_verify_agency_fn' ); 
function af_verify_agency_fn(){
	$af_client_id = $_POST["_client_id"];
	$af_client_secert = $_POST["_client_secert"];
	$af_token = adform_acc_key( $af_client_id,$af_client_secert );
	wp_send_json( $af_token );
	wp_die();
}

/**
 * POST Request for report stats
 */
function af_report_stat( $af_tok_client_id, $af_tok_client_secert, $af_post_body ){
	//$af_tok_client_id = "integration.datauniversal.nl@clients.adform.com" ;
	//$af_tok_client_secert = "Jamu0Ye5iSheNod_uE274xfGYBYUXQ1VSEE1MkDS" ;
	$af_access_token_response =  adform_acc_key( $af_tok_client_id, $af_tok_client_secert );
	$af_access_token = $af_access_token_response["access_token"];
	$af_token_type = $af_access_token_response["token_type"];
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.adform.com/v1/buyer/stats/data',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS => $af_post_body,
	CURLOPT_HTTPHEADER => array(
		'Authorization:' . $af_token_type . ' ' . $af_access_token ,
		'Content-Type: application/json'
	),
	));
	curl_setopt($curl, CURLOPT_HEADER, 1);

	$response = curl_exec($curl);

	curl_close($curl);
						
	$response_header = explode( PHP_EOL,$response );
	$res_header_arr = explode( " ",$response_header[0] );
	
	if( in_array( "202",$res_header_arr ) ){
		$operation_loacation_arr = explode ( " ", $response_header[8] );
		$operation_loacation = trim( $operation_loacation_arr[1] );
		

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.adform.com'.$operation_loacation,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization:' . $af_token_type . ' ' . $af_access_token
		),
		));

		$response = curl_exec($curl);
		$operation_status_arr = json_decode( $response, true );
		$loop_flag = 0;
		$sleep_time = 0;
																			
		while( $loop_flag == 0){
			if( ($operation_status_arr["status"]=="notStarted") || ($operation_status_arr["status"]=="running") ){
				
				$sleep_time++;
				sleep( $sleep_time );
				$response = curl_exec($curl);
				$operation_status_arr = json_decode($response,true);
				if( $sleep_time== 5){
					$sleep_time = 30;
				}
			}
			if( $operation_status_arr["status"]=="succeeded" ){
				$loop_flag = 1;
				
			}
			if( $operation_status_arr["status"]=="failed" ){

				return "error";
				exit("Unable to create operation");
			}
		}

		curl_close($curl);
		$stat_location = $operation_status_arr["location"];
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.adform.com'.$stat_location,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization:' . $af_token_type . ' ' . $af_access_token
		
		),
		));

		$response = curl_exec($curl);
		$af_stat_data_arr = json_decode( $response , true );
		curl_close($curl);
		
		if( array_key_exists( "reportData" , $af_stat_data_arr ) ){

			
			echo "<pre>";
			//print_r( $af_stat_data_arr );
			echo "</pre>";
			
			return $af_stat_data_arr;
			
			
			
		}

	}
	else{
		return "error";
		exit("Unable to send post request for stat");
	}
	
	return "error";
}

add_action( "wp_footer", "my_msg" );

function my_msg(){

	$param = get_option( "af_tbl_website_stat" );
	$af_time_stamp = $param;
	$now_time = gmdate("Y/m/d/H:i:s");
	$af_time_stamp_std = strtotime( $af_time_stamp );
	$now_time_std = strtotime( $now_time );
	$diff = $now_time_std - $af_time_stamp_std;
	
	if( $diff > 3 ){
		
		global $wpdb;
		$af_results_client_id = $wpdb->get_results( "SELECT DISTINCT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = 'af_client_id'", ARRAY_A  );
		$af_results_client_secert = $wpdb->get_results( "SELECT DISTINCT meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key = 'af_client_secert'", ARRAY_A  );

		for( $i=0; $i< sizeof( $af_results_client_id ) ; $i++ ){
			print_r($af_results_client_id );
			$af_client_id = $af_results_client_id[$i]["meta_value"];
			$af_client_secert = $af_results_client_secert[$i]["meta_value"];
			echo "%%%";
			echo $af_client_id;
			echo $af_client_secert;
			$table_name = $wpdb->prefix . 'adform_website_stat';
			$af_website_post_body = '{
				"dimensions": [
				"campaign","campaignID","client","clientID","rtbDomain"
				],
				"metrics": [
				"clicks","impressions"
				],
				"filter": {
				"date": {"from":"yesterday", "to":"today"}
				}
				}';
			$af_website_data_arr = af_report_stat( $af_client_id, $af_client_secert, $af_website_post_body );

			
			$table_name_device_type = $wpdb->prefix . 'adform_device_type_stat';
			$af_deviceType_post_body = '{
				"dimensions": [
				"campaign","campaignID","client","clientID","deviceType"
				],
				"metrics": [
				"clicks","impressions"
				],
				"filter": {
				"date": "thisYear"
				}
				}';
			//$af_stat_data_deviceType_arr = af_report_stat( $af_client_id, $af_client_secert, $af_deviceType_post_body );

			$table_name_os = $wpdb->prefix . 'adform_os_stat';
			$af_os_post_body = '{
				"dimensions": [
				"campaign","campaignID","client","clientID","operatingSystem"
				],
				"metrics": [
				"clicks","impressions"
				],
				"filter": {
				"date": "thisYear"
				}
				}';
			//$af_stat_data_os_arr = af_report_stat( $af_client_id, $af_client_secert, $af_os_post_body );

			$table_name_reportStat = $wpdb->prefix . 'adform_report_stat';
			$af_reportStat_post_body1 = '{
				"dimensions": [
				"campaign","campaignID","client","clientID","date"
				],
				"metrics": [
				"clicks","impressions","ctr","ecpm","ecpc","ecpa","rtbWinRate","rtbBids","viewImpressions","viewImpressionsPercent"
				],
				"filter": {
				"date": "thisYear"
				}
				}';

				$af_reportStat_post_body2 = '{
				"dimensions": [
				"campaign","campaignID","client","clientID","date"
				],
				"metrics": [
				"cost","avgEngagementTime","avgViewabilityTime","conversions","pageviews","bounceRate"
				],
				"filter": {
				"date": "thisYear"
				}
				}';


			//$af_reportStat_arr1 = af_report_stat( $af_client_id, $af_client_secert, $af_reportStat_post_body1 );
			//$af_reportStat_arr2 = af_report_stat( $af_client_id, $af_client_secert, $af_reportStat_post_body2 );
			
			echo "<pre>";
			print_r( $af_website_data_arr["reportData"]["rows"] );
			echo "</pre>";
			echo "<h1> asasas </h1>";
			if( $af_website_data_arr != "error" &&  array_key_exists( "reportData" , $af_website_data_arr ) ){
				
				$af_website_data_db = $wpdb->get_results( "SELECT campaign_id,impression,click,rtb_website FROM {$wpdb->prefix}adform_website_stat", ARRAY_A  );
				
				echo "<pre>";
				print_r($af_website_data_db );
				echo "</pre>";

				for( $i=0; $i< sizeof( $af_website_data_arr["reportData"]["rows"] ); $i++){

					$b_lfag = 0;

					for( $j=0; $j< sizeof( $af_website_data_db ); $j++ ){

						

						//check post data is present in database
						if( in_array( $af_website_data_arr["reportData"]["rows"][$i][1], $af_website_data_db[$j] ) && in_array( $af_website_data_arr["reportData"]["rows"][$i][4], $af_website_data_db[$j] ) ){
							//check data have changes
							if( $af_website_data_arr["reportData"]["rows"][$i][6] != $af_website_data_db[$j]["impression"] || $af_website_data_arr["reportData"]["rows"][$i][5] != $af_website_data_db[$j]["click"]  ){
								$b_lfag = 1;
								break;
							}
							
							
						}
						
						
					}

					if( $b_lfag == 1 ){

						// update exsiting databse field with new impression and clicks
					}
					else{
						//insert api data as new field
						
					}


				}

			}


		}
	
	}
	else
	{
		echo "king ranganr";
	}
}

 

?>
