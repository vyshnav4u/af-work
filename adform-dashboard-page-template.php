<?php
// ---------------------------------------------------------------------------
// Add virtual pages.
// ---------------------------------------------------------------------------
 
/**
 * First create a query variable addition for the pages. This means that
 * WordPress will recognize index.php?virtualpage=name
 */
function example_adformpage_query_vars($vars) {
  $vars[] = 'adformpage';
  return $vars;
}
add_filter('query_vars', 'example_adformpage_query_vars');


/**
 * Add redirects to point desired virtual page paths to the new 
 * index.php?virtualpage=name destination.
 *
 * After this code is updated, the permalink settings in the administration
 * interface must be saved before they will take effect. This can be done 
 * programmatically as well, using flush_rewrite_rules() triggered on theme
 * or plugin install, update, or removal.
 */
function example_adformpage_add_rewrite_rules() {
  add_rewrite_tag('%adformpage%', '([^&]+)');
  add_rewrite_rule(
    'adxl-dashboard-xl/adform/?$',
    'index.php?adformpage=adform-dashboard',
    'top'
  );
 
}
add_action('init', 'example_adformpage_add_rewrite_rules');


/**
 * Assign templates to the Adform pages.
 */
function example_adform_template_include($template) {
  global $wp_query;
  $new_template = '';
 
  if (array_key_exists('adformpage', $wp_query->query_vars)) {
	  global $wp;
    $plugindir = dirname( __FILE__ );
	$templatefilename = 'adform-dashboard.php';
    $return_template = $plugindir . '/templates/' . $templatefilename;
     
    switch ($wp_query->query_vars['adformpage']) {
      case 'adform-dashboard':
        // We expect to find virtualpage-interesting-things.php in the 
        // currently active theme.
        $new_template = $plugindir . '/templates/' . $templatefilename;
        break;
    }
 
    if ($new_template != '') {
      return $new_template;
    } else {
      // This is not a valid virtualpage value, so set the header and template
      // for a 404 page.
      $wp_query->set_404();
      status_header(404);
      return get_404_template();
    }
  }
 
  return $template;
}
add_filter('template_include', 'example_adform_template_include');


function example_adform_settingspage_template_include($template) {
  global $wp_query;
  $new_template = '';
 
  if (array_key_exists('settingspage', $wp_query->query_vars)) {
	  global $wp;
    $plugindir = dirname( __FILE__ );
	$templatefilename = 'adxl-settings.php';
    $return_template = $plugindir . '/templates/' . $templatefilename;
     
    switch ($wp_query->query_vars['settingspage']) {
      case 'adxl-settings':
        // We expect to find settingspage-interesting-things.php in the 
        // currently active theme.
        $new_template = $plugindir . '/templates/' . $templatefilename;
        break;
    }
 
    if ($new_template != '') {
      return $new_template;
    } else {
      // This is not a valid settingspage value, so set the header and template
      // for a 404 page.
      $wp_query->set_404();
      status_header(404);
      return get_404_template();
    }
  }
 
  return $template;
}

add_filter('template_include', 'example_adform_settingspage_template_include');

add_filter( 'show_admin_bar', function( $show ) {
	global $wp_query;
    if (array_key_exists('virtualpage', $wp_query->query_vars) || array_key_exists('settingspage', $wp_query->query_vars) ) {
       
		 return false;
		
    }
	return $show;
} );


?>
