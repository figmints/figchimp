<?php
/*
Plugin Name: Figchimp
Plugin URI: http://figmints.com/
Description: Figmints Mailchimp newsletter widget.
Author: Seth Krasnianski @ Figmints Delicious Design
Version: 1.0
Author Email: seth@figmints.com
*/

// Require ajax action
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once(dirname(__FILE__) . '/figchimp_subscribe.php');
// Add admin URL path
function admin_url_js() {
    echo '<script type="text/javascript">';
      echo 'var ADMIN_URL = "' . admin_url() . '";';
    echo '</script>';
}
add_action('wp_head', 'admin_url_js');
// Enqueue js for form submit
wp_enqueue_script( 'figchimp-submit', plugins_url('/lib/submit.js', __FILE__), array(), '1.0', true );
// Init ajax endpoint for form submit
add_action( 'wp_ajax_figchimp_subscribe', 'figchimp_subscribe' );
add_action( 'wp_ajax_nopriv_figchimp_subscribe', 'figchimp_subscribe' );
// Init Widget
add_action( 'widgets_init', create_function( '', 'register_widget( "Figchimp_Widget" );' ) );
// Add Figchimp styles
function figchimp_styles() { ?>
  <style>

    #figchimp .email.error {
      border: 1px solid red;
    }

    .figchimp-message{
      padding: 20px 0;
    }

  </style>
<?php }
//
add_filter('wp_head', 'figchimp_styles');

// Twitter Feed Widget
class Figchimp_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'figchimp_widget', // Base ID
      'Figchimp', // Name
      array( 'description' => __( 'Figchimp newsletter widget.', 'text_domain' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    extract( $args );
    $title       = apply_filters( 'widget_title', $instance['title'] );
    $APIkey      = isset( $instance['APIkey'] ) ? $instance['APIkey'] : "API Key";
    $listID      = isset( $instance['listID'] ) ? $instance['listID'] : "List ID";;

    echo $before_widget;
      include dirname(__FILE__) . '/form.php';
    echo $after_widget;
    // $form ='<div class="thanks">Thanks for subscribing! Check your e-mail for confirmation</div>
    //         <div class="error"></div>
    //         <form class="single mailChimp">
    //           <input type="submit" class="submit" value="Subscribe"/>
    //           <input type="text" class="email" value=""/>
    //           <div class="clearall"></div>
    //          </form>';

    // echo '<div id="newsletter" class="aside widget">';
    // if ( ! empty( $title ) && ! empty( $description ) )
    //   echo $before_title . $title . $after_title;
    //   echo __( '<p class="newsletter-desc">' . $description . '</p>' . $form, 'text_domain' );
    //   echo $after_widget;
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title']  = strip_tags( $new_instance['title'] );
    $instance['APIkey'] = strip_tags( $new_instance['APIkey'] );
    $instance['listID'] = strip_tags( $new_instance['listID'] );

    return $instance;
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $defaults = array(
      'title' => __('Title'),
      'APIkey' => __('API Key'),
      'listID' => __('List ID')
    );
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'APIkey' ); ?>"><?php _e( 'API Key:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'APIkey' ); ?>" name="<?php echo $this->get_field_name( 'APIkey' ); ?>" type="text" value="<?php echo esc_attr( $instance['APIkey'] ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'listID' ); ?>"><?php _e( 'List ID:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'listID' ); ?>" name="<?php echo $this->get_field_name( 'listID' ); ?>" type="text" value="<?php echo esc_attr( $instance['listID'] ); ?>" />
    </p>
    <?php
  }

} // end class

?>