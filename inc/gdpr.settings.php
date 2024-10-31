<?php
/**
 * N-Media Setting Manager Class
 * 
 * 1- Rendering Settings
 * 2- Save Settings
 * 3- Get Settings
 * 
 */
 
class WPGDPR_Settings {
    
    var $settings;
    var $setting_key;
    var $saved_settings;
    
    private static $ins = null;
    
    function __construct() {
        
        // Adding menu to setting
        add_action( 'admin_menu', array($this,'add_menu_page') );
        
        $this->settings = wpgdpr_get_admin_setting();
        $this->setting_key = 'wpgdpr_settings';
        
        $this->saved_settings = $this->get_saved_settings();
        
        add_action('wp_ajax_save_'.$this->setting_key, array($this, 'save_settings'));
    }
    
    public static function get_instance() {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
    
    function add_menu_page() {
        
        add_options_page( 
    		'WP GDPR',
    		'WP GDPR',
    		'manage_options',
    		'nm-gdpr-settings',
    		array($this,'setting_page')
    	);
    }
    
    function setting_page() {
        
        echo '<h2>'.__('GDPR Settings', 'wp-gdpr').'</h2>';
        echo '<hr class="wpr-heading-line">';
        
        $this -> display();
    }
    
    // Display Function
    public function display() {
        
        wp_enqueue_style('wpgdpr-setting-css', WPGDPR_URL."/css/admin/wpgdpr-admin.css");
        wp_enqueue_style('wpgdpr-settings-css', WPGDPR_URL."/css/jquery-ui-css.css");
        wp_enqueue_style('wpgdpr-setting-st', WPGDPR_URL."/css/select2.css");
        
        wp_enqueue_script('wpgdpr-setting-js', WPGDPR_URL."/js/admin/wpgdpr-admin.js", array('jquery' ,'jquery-ui-core', 'jquery-ui-tabs'), '1.0', true);
        wp_enqueue_script('wpgdpr-setting-slct', WPGDPR_URL."/js/select2.js", array('jquery'), '1.0', true); 

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        ?>
        <div class="wpgdpr-settings-wrapper page_load">
            <form id="<?php echo esc_attr($this->setting_key); ?>_form">
                <input type="hidden" name="action" value="save_<?php echo esc_attr($this->setting_key) ?>">
                <div id="gdpr-tabs">
                  <ul>
                      <?php foreach ($this-> settings as $tab_id => $data) { 
                      
                        $tab_title = ucfirst($tab_id);
                        // Tabs heads
                        echo '<li><a href="#'.esc_attr($tab_id).'">'.sprintf(__("%s",'wp-gdpr'),$tab_title).'</a></li>';
                      }
                      ?>
                       
                   </ul>
                    <?php foreach ($this-> settings as $tab_id => $data) { ?>
                        <div id="<?php echo esc_attr($tab_id); ?>">
                            <table class="form-table tb-control">
                                <?php foreach ($data as $key => $field_data) {
                                    $type       = isset($field_data['type']) ? $field_data['type'] : '';
                                    $id         = isset($field_data['id']) ? $field_data['id'] : '';
                                    $advance_url = isset($field_data['wpgdpr_advance']) ? $field_data['wpgdpr_advance'] : '';

                                    $label      = isset($field_data['label']) ? $field_data['label'] : '';
                                    $desc       = isset($field_data['description']) ? $field_data['description'] : '';
                                    $default    = isset($field_data['default']) ? $field_data['default'] : '';
                                    $options    = isset($field_data['options']) ? $field_data['options'] : '';
                                    $icon       = isset($field_data['icon']) ? $field_data['icon'] : '';
                                    $img        = isset($field_data['img']) ? $field_data['img'] : '';
                                    $icon = '<span class="color '.esc_attr($icon).'"></span>';
                                    // divide rows for heading
                                    $divider  = $type == 'divider' ? 'wpgdpr-divider-heading' : '';
                                    // wpgdpr_pa($type);
                                    $show_url = $id == 'wpgdpr_advance_redirect' ? 'wpgdpr-url-toggle':'';
                                    $hide_url = $advance_url == 'set_advance'? 'set_advance' : '';
                                    
                                    $colspan = $type == 'html' ? 3 : 2;
                                
                                    ?>
                                        <tr 
                                            class="<?php echo esc_attr($divider); ?>" 
                                            data-hide-url ="<?php echo esc_attr($hide_url); ?>"
                                            data-show-url ="<?php echo esc_attr($show_url); ?>"
                                        >
                                            <td class="wpgdpr-label-text"><?php echo $img.' '. $icon.' '. $label; ?></td>
                                            <td colspan="<?php echo esc_attr($colspan);?>"><?php echo $this->input($type, $id, $default, $options, $field_data); ?></td>
                                            <?php if( $type != 'html'): ?>
                                                <td class = "wpgdpr-desc-text"><?php echo $desc; ?></td>
                                            <?php endif;?>
                                        </tr>
                                <?php } ?>
                            </table>
                        </div>
                    <?php } ?>

                </div>
                
                <div class="wpgdpr_sub_st_control">
                    <input type="submit" value="Save Settings" class="btn button button-primary btn-primary">
                     <div class="wpgdpr_save_alert wpgdpr-alert-display"></div>
                     <span class="wpgdpr-spinner"></span>
                </div>
            </form>
        </div>
        <?php
    }
    
    // Render input control
    function input( $type, $id, $default, $options="", $field_data) {
        
        $input_html = '';
        $name  = $this->setting_key.'['.$id.']';
        $label = $field_data['label'];
        $description = $field_data['description'];
        
        $value = ($this->get_option($id) == '') ? $default : $this->get_option($id);
        // wpgdpr_pa($value);
        $value = stripslashes($value);
        
        switch( $type ) {
        
            case 'text':
                
                $input_html .= '<input class="form-control wpgdpr-text-option" name="'.esc_attr($name). '" type="text" id="'.esc_attr($id).'" value="'.esc_attr($value).'">';
                break;
            case 'radio':
                
                $input_html .= '<input  name="'.esc_attr($name).'" type="radio" id="'.esc_attr($id).'" value="'.esc_attr($default).'" '.checked( $value, $default, false ).'>';
                break;
            case 'checkbox':
                
                $input_html .= '<input  name="'.esc_attr($name).'" type="checkbox" id="'.esc_attr($id).'" value="on" '.checked($value,'on', false).'>';
                $input_html .='<p class="wpgdpr-chk-option">Yes</p>';
                break;
            case 'select':
                
                $input_html .= '<select class="wpgdpr-select-design wpgdpr_op_select" name="'.esc_attr($name) .'">';
                    foreach($options as $val => $text) {
                        $input_html .= '<option class="wpgdpr-option-width" value="'.esc_attr($val).'" ' . selected( $value, $val, false). '>'.$text.'</option>';
                    }
                    
                    $input_html .= '</select>';
                    
                break;
            case 'textarea':
                
                $input_html .= '<textarea name="'.esc_attr($name).'" rows="4" style="width:66%;">'. esc_textarea($value) .'</textarea>';
                break;
            case 'wpgdpr_color':
                
                $input_html .= '<input name="'.esc_attr($name).'" class="wp-color" id="'.esc_attr($id).'" value="'.esc_attr($value).'">';
                break;

            case 'button':
                $input_html .= '<button class="btn btn-success">Advance Role Base Redirections</button>';
                break;
                
            case 'html':
                $input_html .= '<p>'.$description.'</p>';
                break;

            case 'access_roles':
                
                $input_html .= wpgdpr_get_all_wp_roles($name, $id, $value);
                break;

            case 'dash_access_role':
                
                $get_roles = get_editable_roles();
                $input_html .= '<select name="'.esc_attr($name).'[]" id="'.esc_attr($id).'" class="gn_roles" multiple>';
                unset($get_roles['administrator']);
                foreach ($get_roles as $roles => $role_name) {

                    $selected = '';
                    if( !empty($value) ) {
                        $selected = in_array($roles, $value) ? 'selected="selected"' : '';
                    }
                    $input_html .= '<option value="'.esc_attr($roles).'" '.$selected.'>'.sprintf(__("%s","wp-registration"),$roles).'</option>';
                }
                $input_html .= '</select>';


                break;
                
          
        }
        
        return $input_html;
    }
    
    
    // Saving settings
    function save_settings() {
        
       if( !isset($_POST[$this->setting_key]) ) 
            wp_die('No Data Found');
             
       // Sanitizing posted data
        $sanitized_settings = array();
        foreach($_POST[$this->setting_key] as $key => $value) {
            
            switch ($key) {
                case 'wpgdpr_policy_text':
                    $sanitized_settings[$key] = wp_filter_post_kses( $value );
                    break;
                
                default:
                    $sanitized_settings[$key] = sanitize_text_field($value);
                    break;
            }
        }
        
        update_option($this->setting_key, $sanitized_settings);
        wp_die( __("Settings updated successfully", 'wp-gdpr') );
    }
    
    // Get all settings from option
    function get_saved_settings() {
        
        $settings = get_option($this->setting_key);
        return $settings;
    }
    
    // Get option value
    function get_option($id) {
        
        if( isset($this->saved_settings[$id]) ) {
            return $this->saved_settings[$id];
        }
        
        return '';
    }
}

function WPGDPR_Settings() {
    
    return WPGDPR_Settings::get_instance();
}