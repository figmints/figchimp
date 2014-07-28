<?php
// Require Mailchimp API
require_once(dirname(__FILE__) . '/lib/mailchimp.php');
// AJAX subscribe action
function figchimp_subscribe() {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get widget options
    $widget  = get_option('widget_figchimp_widget');
    // Extract options
    $options = reset($widget);
    // Get apikey
    $apikey  = $options['APIkey'];
    // Get listId
    $listId  = $options['listID'];

    // Instantiate Mailchimp API
    $api = new MCAPI($apikey);
    // echo json_encode(array('test'));
    $email = $_POST['email'];

    // Subscribe Email
    $retval = $api->listSubscribe( $listId, $email);

    if ($api->errorCode){
      $response = array(
        'error'   => true,
        'code'    => "Code: " . $api->errorCode,
        'message' => "Unable to subscribe: " . $api->errorMessage
      );
      echo json_encode($response);
    } else {
      $response = array(
        'error'   => false,
        'message' => "Subscribed - look for the confirmation email!"
      );
      echo json_encode($response);
    }

    die();
  }
}