<?php
/**
 * Helper functions for wp-gdpr
 * 
 **/
 

    // not run if accessed directly
    if( ! defined('ABSPATH' ) ){
        die("Not Allowed");
    }

    // loading template files
    function wpgdpr_load_templates( $template_name, $vars = null) {

        if( $vars != null && is_array($vars) ){
            extract( $vars );
        };

        $template_path =  WPGDPR_PATH . "/templates/{$template_name}";
        if( file_exists( $template_path ) ){
        	require ( $template_path );
        } else {
            die( "Error while loading file {$template_path}" );
        }
    }

    // print defualt array
    function wpgdpr_pa($arr){
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }

   

   
    // wpr dashboard accessed setting
    function wpgdpr_get_all_wp_roles($name, $id, $value){

        $get_roles = get_editable_roles();
        $html  = '';
        $html .= '<select name="'.esc_attr($name).'[]" id="'.esc_attr($id).'" class="gn_roles" multiple>';
            foreach ($get_roles as $roles => $role_name) {
            
                $selected = '';
                if( !empty($value) ) {
                    $selected = in_array($roles, $value) ? 'selected="selected"' : '';
                }
                $html .= '<option value="'.esc_attr($roles).'" '.$selected.'>'.sprintf(__("%s","wp-registration"),$roles).'</option>';
            }
        $html .= '</select>';
        return $html;
    }

    // Get logged in user role
    function wpgdpr_get_current_user_role() {
      if( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $role = ( array ) $user->roles;
        return $role[0];
      } else {
        return false;
      }
    }
    
    // Return current user id
    function wpgdpr_get_current_user_id() {

      $user_id = null;

      if(isset($_GET['user_id']) && $_GET['user_id'] != '') {
        $user_id = intval($_GET['user_id']);
      } elseif( is_user_logged_in() ) {
        $user_id = get_current_user_id();
      }

      return apply_filters('wpgdpr_current_user_id', $user_id);
    }

  
    // Loading scripts for inputs based on field meta
    // This function load scripts for inputs
    function wpgdpr_load_input_script( $field_type, $scritps ) {

        foreach($scritps as $type => $source) {
            // wpgdpr_pa($source);

            $script_handler = "{$field_type}-{$type}";
            $scrtipt_source = WPGDPR_URL.'/'.$source['source'];
            if( $type == 'js' ) {
                wp_enqueue_script($script_handler, 
                                $scrtipt_source, 
                                $source['depends'], 
                                WPGDPR_VERSION, 
                                true);
            } else if( $type == 'default' ){
                wp_enqueue_script($source['source']);

            }else{
                wp_enqueue_style($script_handler, $scrtipt_source);
            }
        }
    }
    
    /*--------------------------------------
     This function render all setting array 
    ----------------------------------------*/
   function wpgdpr_get_admin_setting() {

        $wpgdpr_options = array(
            'general' =>  array(
                array(
                    'type'         => 'checkbox',
                    'id'           => 'wpgdpr_enable',
                    'label'        => __("GDPR Enable", 'wp-gdpr'),
                    'description'  => __('Show GDPR content/consent on forms.', 'wp-gdpr'),
                ),
                
                array(
                    'type'         => 'text',
                    'id'           => 'wpgdpr_policy_title',
                    'label'        => __("GDPR Policy Title", 'wp-gdpr'),
                    'description'  => __('Add title before polic text. Use h3 wrapper', 'wp-gdpr'),
                    'default'       => __('', 'wp-gdpr')
                ),
                
                array(
                    'type'         => 'textarea',
                    'id'           => 'wpgdpr_policy_text',
                    'label'        => __("GDPR Policy Text", 'wp-gdpr'),
                    'description'  => __('Add line before form button. HTML allowed', 'wp-gdpr'),
                    'default'       => __('Your data will be processed and stored in line with our Privacy Policy.', 'wp-gdpr')
                ),
                
                array(
                    'type'         => 'checkbox',
                    'id'           => 'wpgdpr_show_checkbox',
                    'label'        => __("Show checkbox (user consent)", 'wp-gdpr'),
                    'description'  => __('Will show checkbox before polic text.', 'wp-gdpr'),
                ),
                
                array(
                    'type'         => 'text',
                    'id'           => 'wpgdpr_error_message',
                    'label'        => __("Error Message", 'wp-gdpr'),
                    'description'  => __('An error message if consent/checkbox is ticked.', 'wp-gdpr'),
                    'default'       => __('You need to accept our policies.', 'wp-gdpr')
                ),
            ),
            
            'audit' =>  array(
                array(
                    'type'         => 'html',
                    'id'           => 'wpgdpr_audit',
                    'label'        => __("Request GDPR Audit for This Site.", 'wp-gdpr'),
                    'description'  => __('Not sure how to make your site GDPR Compliance? N-Media knows what GDPR exactly demands. We will audit your entire site only $100.00 and make it GDPR ready. Contact us at <a href="mailto:sales@najeebmedia.com">sales@najeebmedia.com</a>', 'wp-gdpr'),
                ),
            ),

            
        );

        return apply_filters( 'wpgdpr_options', $wpgdpr_options);
    }