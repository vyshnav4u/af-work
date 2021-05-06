<?php
/*
	Plugin Name: WP ADFORM Dashboard
	Description: WP ADFORM Dashboard — is a plugin to show a dashboard to users with ADFORM statistics as graphical representations .
	Version: 1.0.0
	Author: Midnay
	Author URI: https://midnay.com
	Textdomain: wp-mid-lang
	License: GPLv2 or later
*/
class Wp_ADFORM_Main_Class{
    /**
	* function for general init
	* @return void
	* @author MidnayWS
	*/ 
	public static function wp_ADFORM_main_init(){
	    add_shortcode("adforms_advertisers_get", __CLASS__.'::adforms_advertisers_shortcode');
	    add_shortcode("adforms_managers_get", __CLASS__.'::adforms_managers_shortcode');
	
	    add_shortcode("adforms_campagins_get", __CLASS__.'::adforms_campagins_shortcode');
	
	}
	
	 /**
	* function to display list of advertisers
	* @return void
	* @author MidnayWS
	*/ 
	public static function adforms_advertisers_shortcode(){
	    ob_start();
	    $curl = curl_init();
        echo "<h4>Advertisers </h4>";
        curl_setopt_array($curl, array(
           CURLOPT_URL => 'https://id.adform.com/sts/connect/token/',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS => 'client_id=integration.datauniversal.nl@clients.adform.com&client_secret=Jamu0Ye5iSheNod_uE274xfGYBYUXQ1VSEE1MkDS&grant_type=client_credentials&scope=https://api.adform.com/scope/eapi https://api.adform.com/scope/buyer.advertisers',
           CURLOPT_HTTPHEADER => array(
               'Content-Type: application/x-www-form-urlencoded',
           ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        //echo '<pre>';
        //print_r( $response);
        //echo '</pre>';
        $grant_token_array = array();
		$grant_token_array = json_decode($response, true);
		//echo '<pre>';
        //print_r( $grant_token);
        //echo '</pre>';
        $access_token = $grant_token_array['access_token'];
        echo "Access Token: " . $access_token ;
        
        
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
            'Authorization: Bearer ' . $access_token 
          ),
        ));
        
        $response = curl_exec($curl);
        $advertisers =  json_decode($response, true);
        
        curl_close($curl);
        echo '<pre>';
        print_r( $advertisers);
        echo '</pre>'; 	
		return ob_get_clean();
	}
	
	 /**
	* function to display list of managers
	* @return void
	* @author MidnayWS
	*/ 
	public static function adforms_managers_shortcode(){
	    ob_start();
	    $curl = curl_init();
        echo "<h4>Managers </h4>";
        curl_setopt_array($curl, array(
           CURLOPT_URL => 'https://id.adform.com/sts/connect/token/',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS => 'client_id=integration.datauniversal.nl@clients.adform.com&client_secret=Jamu0Ye5iSheNod_uE274xfGYBYUXQ1VSEE1MkDS&grant_type=client_credentials&scope=https://api.adform.com/scope/buyer.classifiers.readonly',
           CURLOPT_HTTPHEADER => array(
               'Content-Type: application/x-www-form-urlencoded',
           ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        //echo '<pre>';
        //print_r( $response);
        //echo '</pre>';
        $grant_token_array = array();
		$grant_token_array = json_decode($response, true);
		//echo '<pre>';
        //print_r( $grant_token);
        //echo '</pre>';
        $access_token = $grant_token_array['access_token'];
        echo "Access Token: " . $access_token ;
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.adform.com/v1/buyer/classifiers/managers',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $access_token 
          ),
        ));
        
        $response = curl_exec($curl);
        $managers =  json_decode($response, true);
        
        curl_close($curl);
        echo '<pre>';
        print_r( $managers);
        echo '</pre>'; 	
		return ob_get_clean();
	} 
	
	/**
	* function to display list of Campagins
	* @return void
	* @author MidnayWS
	*/ 
	public static function adforms_campagins_shortcode(){
	    ob_start();
	    
        echo "<h4>Advertiser Campagins </h4>";
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://id.adform.com/sts/connect/token/',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'client_id=integration.datauniversal.nl%40clients.adform.com&client_secret=Jamu0Ye5iSheNod_uE274xfGYBYUXQ1VSEE1MkDS&grant_type=client_credentials&scope=https://api.adform.com/scope/buyer.advertisers https://api.adform.com/scope/buyer.advertisers.readonly',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: __cfduid=d2d9bc55c0c6d75c40fb0c33507b68bbc1618471638'
          ),
        ));
        
        $response = curl_exec($curl);
        //echo '<pre>';
        //print_r( $response);
        //echo '</pre>';
        $grant_token_array = array();
		$grant_token_array = json_decode($response, true);
		//echo '<pre>';
        //print_r( $grant_token);
        //echo '</pre>';
        $access_token = $grant_token_array['access_token'];
        echo "Access Token: " . $access_token ;
        
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.adform.com/v1/buyer/advertisers/2086523/campaignLabels',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $access_token 
          ),
        ));
        
        $response = curl_exec($curl);
        $campagins =  json_decode($response, true);
        
        curl_close($curl);
        echo '<pre>';
        print_r( $campagins);
        echo '</pre>'; 	
		return ob_get_clean();
	}
}
Wp_ADFORM_Main_Class::wp_ADFORM_main_init();

/**
 * Class which handle all operation related to databse
 */
class wp_ADFORM_table_class{

  public static function af_table_creation () {
    global $wpdb;
  
    $table_name = $wpdb->prefix . "adform_report_stat";
    
    $charset_collate = $wpdb->get_charset_collate();
  
    $sql = "CREATE TABLE $table_name (
      report_stat_id int NOT NULL AUTO_INCREMENT,
      campaign_id int NOT NULL,
      campaign_name varchar(55) NOT NULL,
      advertiser_id int NOT NULL,
      advertiser_name varchar(55) NOT NULL,
      date date NOT NULL,
      impression varchar(9) NOT NULL,
      click varchar(9) NOT NULL,
      ctr varchar(9) NOT NULL,
      ecpm varchar(9) NOT NULL,
      ecpc varchar(9) NOT NULL,
      ecpa varchar(9) NOT NULL,
      rtb_winrate varchar(9) NOT NULL,
      rtb_bids varchar(9) NOT NULL,
      view_impression varchar(9) NOT NULL,
      view_impression_percentage varchar(9) NOT NULL,
      spend varchar(9) NOT NULL,
      avg_view_time varchar(9) NOT NULL,
      avg_engage_time varchar(9) NOT NULL,
      page_view varchar(9) NOT NULL,
      bounce_rate varchar(9) NOT NULL,
      conversion varchar(9) NOT NULL,
      PRIMARY KEY  (report_stat_id)
      ) $charset_collate;";
  
      $table_name2 = $wpdb->prefix . "adform_device_type_stat";
      $sql2 = "CREATE TABLE $table_name2 (
        device_stat_id int NOT NULL AUTO_INCREMENT,
        campaign_id int NOT NULL,
        campaign_name varchar(55) NOT NULL,
        advertiser_id int NOT NULL,
        advertiser_name varchar(55) NOT NULL,
        impression varchar(9) NOT NULL,
        click varchar(9) NOT NULL,
        device varchar(30) NOT NULL,
        PRIMARY KEY  (device_stat_id)
        ) $charset_collate;";

      $table_name3 = $wpdb->prefix . "adform_os_stat";
      $sql3 = "CREATE TABLE $table_name3 (
        os_stat_id int NOT NULL AUTO_INCREMENT,
        campaign_id int NOT NULL,
        campaign_name varchar(55) NOT NULL,
        advertiser_id int NOT NULL,
        advertiser_name varchar(55) NOT NULL,
        impression varchar(9) NOT NULL,
        click varchar(9) NOT NULL,
        os varchar(30) NOT NULL,
        PRIMARY KEY  (os_stat_id)
        ) $charset_collate;";

      $table_name4 = $wpdb->prefix . "adform_website_stat";
      $sql4 = "CREATE TABLE $table_name4 (
        web_stat_id int NOT NULL AUTO_INCREMENT,
        campaign_id int NOT NULL,
        campaign_name varchar(55) NOT NULL,
        advertiser_id int NOT NULL,
        advertiser_name varchar(55) NOT NULL,
        impression varchar(9) NOT NULL,
        click varchar(9) NOT NULL,
        rtb_website varchar(30) NOT NULL,
        PRIMARY KEY  (web_stat_id)
        ) $charset_collate;";


      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );
      dbDelta( $sql2 );
      dbDelta( $sql3 );
      dbDelta( $sql4 );
    }
  
}
register_activation_hook( __FILE__, 'wp_ADFORM_table_class::af_table_creation' );

include("adform-dashboard-page-template.php");
include("wp-adform-dashboard-admin.php");

