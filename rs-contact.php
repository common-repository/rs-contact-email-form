<?php
/*
Plugin Name: RS Contact Email Form
Plugin URI: http://smallbusinesswebdesigns.net/
Description: You can customize your own css with this plugin as per you want for design(else by default load theme style) and use as a widget in sidebar.It's very easy in customization .Currently I have added some main email field's but in next release I'll add attachments and optional style.
Use this plugin on any page,post and widget area by short-code [rs_contact_form]
You can use in sidebar by widget and widget name is "RS Contact Form Email".
Version: 1.0
Author: smallbusinessau
Author URI: http://smallbusinesswebdesigns.net/
*/

add_action("wp_head", "rcfe_rs_form_code");
function rcfe_rs_form_code_init() { 
	load_plugin_textdomain( 'rcfe_rs_form_code', false, dirname( plugin_basename( __FILE__ ) ));
}
add_action('init', 'rcfe_rs_form_code_init');

function rcfe_rs_form_code()
{
    
    echo '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">';
 wp_nonce_field('not_sent'); 
    echo '<p>';
    
    echo 'Your Name (required) <br/>';
    
    echo '<input type="text" name="rs-name" pattern="[a-zA-Z0-9 ]+" value="' . (isset($_POST["rs-name"]) ? esc_attr($_POST["rs-name"]) : '') . '" size="35" />';
    
    echo '</p>';
    
    echo '<p>';
    
    echo 'Your Email (required) <br/>';
    
    echo '<input type="email" name="rs-email" value="' . (isset($_POST["rs-email"]) ? esc_attr($_POST["rs-email"]) : '') . '" size="35"/>';
    
    echo '</p>';
    
    echo '<p>';
    
    echo 'Subject (required) <br/>';
    
    echo '<input type="text" name="rs-subject" pattern="[a-zA-Z ]+" value="' . (isset($_POST["rs-subject"]) ? esc_attr($_POST["rs-subject"]) : '') . '" size="35" />';
    
    echo '</p>';
    
    echo '<p>';
    
    echo 'Your Message (required) <br/>';
    
    echo '<textarea rows="5" cols="35" name="rs-message">' . (isset($_POST["rs-message"]) ? esc_attr($_POST["rs-message"]) : '') . '</textarea>';
    
    echo '</p>';
    
    echo '<p><input type="submit" name="rs-submitted" value="Send"></p>';
    
    echo '</form>';
    
}



/* Hook Plugin */
	register_activation_hook(__FILE__,'rcfe_rs_form_code');


function rcfe_sent_email()
{
    // if the submit button is clicked, send the email 
      $retrieved_nonce = $_REQUEST['_wpnonce'];
      if (!wp_verify_nonce($retrieved_nonce, 'not_sent' ) ) die( 'Failed security check' );        
        // sanitize form values        
         $name    = sanitize_text_field($_POST["rs-name"]);
         $email   = sanitize_email($_POST["rs-email"]);
         $subject = sanitize_text_field($_POST["rs-subject"]);
         $message = esc_textarea($_POST["rs-message"]);       
        
        if ($name != '' && $email != '' && $subject != '') {
            
            // get the blog administrator's email address				
            
            $to = get_option('admin_email');
            
            $headers = "From: $name <$email>" . "\r\n";
            
            // If email has been process for sending, display a success message
            
            if (wp_mail($to, $subject, $message, $headers)) {
                
                echo '<div>';
                
                echo '<p>Thanks for contacting me, expect a response soon.</p>';
                
                echo '</div>';
            } else {
                echo 'An unexpected error occurred';
            }
        } else {
            echo 'Fill all required fields';
        }
}

if (function_exists("rcfe_sent_email")) {
   rcfe_sent_email();
}

function rs_shortcode()
{
    
    ob_start();
    
    rcfe_sent_email();
    
    rcfe_rs_form_code();
    
    return ob_get_clean();
    
}

add_shortcode('rs_contact_form', 'rs_shortcode');

class RsContactFormEmailWidget extends WP_Widget
{
    
    function RsContactFormEmailWidget()
    {
        
        $widget_ops = array(
            'classname' => 'RsContactFormEmailWidget',
            'description' => 'Displays RS Contact Form Email'
        );
        
        $this->WP_Widget('RsContactFormEmailWidget', 'RS Contact Form Email', $widget_ops);
        
    }
    
    
    
    function form($instance)
    {
        
        $instance = wp_parse_args((array) $instance, array(
            'title' => ''
        ));
        
        $title = $instance['title'];
        
?>

  <p><label for="<?php
        echo $this->get_field_id('title');
?>">Title: <input class="widefat" id="<?php
        echo $this->get_field_id('title');
?>" name="<?php
        echo $this->get_field_name('title');
?>" type="text" value="<?php
        echo attribute_escape($title);
?>" /></label></p>

<?php
        
    }   
    
    function update($new_instance, $old_instance)
    {        
        $instance = $old_instance;        
        $instance['title'] = $new_instance['title'];        
        return $instance;        
    }  
        
    function widget($args, $instance)
    {        
        extract($args, EXTR_SKIP); 
        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
        if (!empty($title))
            echo $before_title . $title . $after_title;
        echo do_shortcode('[rs_contact_form]');
        echo $after_widget;        
    }
}
add_action('widgets_init', create_function('', 'return register_widget("RsContactFormEmailWidget");'));
?>