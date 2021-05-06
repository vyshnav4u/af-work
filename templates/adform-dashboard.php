<?php
/**
 * The template for displaying dashboard.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Midnay_Adxl_Adforms
 * @since 1.0.0
 */

//include('adform-dashboard-header.php');
$imag_src="https://adxldashboard.com/wp-content/uploads/2020/05/logo-adxl-dashboard-transp-glow.png";
$current_user = wp_get_current_user();
$current_user->ID; // The current user ID
$current_user_id = get_current_user_id(); // Alternative for getting current user ID
if(get_user_meta( $current_user_id, 'user_logo',true) !=""){
   $imag_src=get_user_meta( $current_user_id, 'user_logo',true);
}?>


<?php
if ( is_user_logged_in() ) {
    // your code for logged in user 
    echo '<h1>Text Adforms page</h1>';
    $auth_Code = "";
    if (isset($_GET['code'])){
        $auth_Code = $_GET['code'];
        $curl = curl_init();
        echo "<h4>Auth Code : $auth_Code </h4>";
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://id.adform.com/sts/connect/token/',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code=' . $auth_Code .'&client_id=adxldashboard%40apps.adform.com&client_secret=uWquCjeZJtW_iIRjohQQU8SBFSOHEnU07Fyr2hfT&redirect_uri=https%3A%2F%2Fadxldashboard.com%2Fadxl-dashboard-xl%2Fadform',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: __cfduid=d612b96542ca192ff744c639b2f1310531613463579'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        print_r($response) ;
    }else{
       echo do_shortcode('[adforms_advertisers_get]');
       echo do_shortcode('[adforms_managers_get]');
       //echo do_shortcode('[adforms_campagins_get]');
    }
  
} else { 
   // your code for logged out user 
   echo do_shortcode('[vivid-login-page]');
}

//include('dashboard-footer.php');

?>
