<?php
$validPaths = array(
  "/home/data/httpd/dev.eclipse.org/html/site_login/",
  "/localsite/"
);

$a = debug_backtrace();
$caller = $a[0]['file'];
$validCaller = false;
for ($i = 0; $i < count($validPaths); $i++) {
  if (strstr($caller, $validPaths[$i])) {
    $validCaller = true;
    // $auth_token will be deprecated once we start supporting bitcoins.
    // $auth_token = "";
    $payment_gateway_keys = array(
      'bitpay' => array(
        'staging' => '',
        'production' => ''
      ),
      'paypal' => array(
        'staging' => '',
        'production' => ''
      )
    );

    // Mailchimp authentications
    $mailchimp_keys = array(
      'staging' => array(
        'api_key' => '',
        'list_id' => ''
      ),
      'production' => array(
        'api_key' => '',
        'list_id' => ''
      )
    );

    break;
  }
}
if (!$validCaller) {
  echo "Execution from Invalid Path. This attempt has been logged. Please contact mailto:webmaster@eclipse.org";
  exit();
}