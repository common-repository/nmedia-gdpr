<?php 
/*
Plugin Name: WordPress GDPR
Plugin URI: http://www.najeebmedia.com
Description: Add checkbox, privacy policy statements to your site comment forms
Version: 1.1
Author: nmedia
Text Domain: wp-gdpr
Author URI: http://www.najeebmedia.com/
*/

// exit if accessed directly
if( ! defined('ABSPATH' ) ){
    exit;
}

define( 'WPGDPR_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );
define( 'WPGDPR_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );


/* ======= plugin includes =========== */
if( file_exists( dirname(__FILE__).'/inc/helpers.php' )) include_once dirname(__FILE__).'/inc/helpers.php';
if( file_exists( dirname(__FILE__).'/inc/admin.php' )) include_once dirname(__FILE__).'/inc/admin.php';
if( file_exists( dirname(__FILE__).'/inc/gdpr.settings.php' )) include_once dirname(__FILE__).'/inc/gdpr.settings.php';


class WP_GDPR {

    function __construct(){


        // Local properties
        $this->is_enable    = $this->is_enable();
        $this->is_consent_required = $this->is_consent_required();
        $this->policy_text  = $this->get_policy_text();
        $this->policy_title = $this->get_policy_title();
        $this->error_message= $this->get_error_message();
        

        // Actions
        add_action('wp_enqueue_scripts', array($this, 'enqueue_script') );
        
        // Filters
        // validating comment for consent/checkbox
        add_filter('comment_form_after_fields', array($this, 'show_on_comment_form'), 99);
        add_filter( 'preprocess_comment', array($this, 'verify_consent') );
    }
    
    
    function enqueue_script() {
        
        if( is_single() && comments_open() ){
            
            wp_enqueue_style('wpgdpr-css', WPGDPR_URL."/css/wpgdpr.css");
        }
    }
    
    function show_on_comment_form() {
        
        if( ! $this->is_enable ) return '';
        
        echo $this->show_policy_text();
    }
    
    function verify_consent($comment_data) {
        
        if( ! $this->is_enable ) return '';
        if( ! $this->is_consent_required ) return '';
        
        if( ! isset($_POST['wpgdpr_consent']) ) {
            
            $args = array('back_link'=>true);
            wp_die( sprintf(__('%s'), $this->error_message), 'Error', $args);
        }
        
    }
    
    
    // Helper function
    function is_enable() {
        
        $gdpr_enable = false;
        
        if( WPGDPR_Settings()->get_option('wpgdpr_enable') == 'on' )
            $gdpr_enable = true;
            
        return apply_filters('wpgdpr_is_enable', $gdpr_enable);
    }
    
    // Should display checkbox
    function is_consent_required() {
        
        $consent_required = false;
        
        if( WPGDPR_Settings()->get_option('wpgdpr_show_checkbox') == 'on' )
            $consent_required = true;
            
        return apply_filters('wpgdpr_is_consent_required', $consent_required);
    }
    
    function get_policy_text() {
        
        $policy_text = WPGDPR_Settings()->get_option('wpgdpr_policy_text');;
        $policy_text = stripslashes($policy_text);
        return apply_filters('wpgdpr_policy_text', $policy_text);
    }
    
    function get_policy_title() {
        
        $policy_title = WPGDPR_Settings()->get_option('wpgdpr_policy_title');;
        $policy_title = stripslashes($policy_title);
        return apply_filters('wpgdpr_policy_title', $policy_title);
    }
    
    function get_error_message() {
        
        $error_message = WPGDPR_Settings()->get_option('wpgdpr_error_message');;
        
        if( empty($error_message) ) {
            $error_message = 'You need to accept our policies';
        }
        
        $error_message = stripslashes($error_message);
        return apply_filters('wpgdpr_error_message', $error_message);
    }
    
    
    
    // Show policy text
    function show_policy_text() {
        
        $policy_text_html = '';
		
		if( !empty($this->policy_title) ) {
		
		    $policy_text_html .= sprintf(__("<h3>%s</h3>",'wp-gdpr'), $this->policy_title);    
		}
		
		if( $this->is_consent_required ) {
			
			$policy_text_html .= '<label class="wpgdpr-comment-checkbox">';
			$policy_text_html .= '<input type="checkbox" name="wpgdpr_consent" required id="wpgdpr-comment-checkbox"><span class="checkmark"></span>';
		}
		
		$policy_text_html .= sprintf(__("%s", 'wp-gdpr'), $this->policy_text);
		
		if( $this->is_consent_required ) {
		    $policy_text_html .= '</label>';
		}
		
		
		return apply_filters('wpgdpr_policy_text_html', $policy_text_html);
    }

    
}

// lets start plugin
add_action('plugins_loaded', 'wpr_start');
function wpr_start() {
    return new WP_GDPR();
}

if( is_admin() ) {
    WPGDPR_Settings();
}