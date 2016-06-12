<?php
// Read config
require __DIR__ . '/config.default.php';
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
  include $configFile;
  $config = array_merge($defaultConfig, $config);
} else {
  $config = $defaultConfig;
}

// Compare slack token
if (!isset($_POST['token']) || $_POST['token'] != $config['slack_webhook_token']) {
  throw new Exception("Wrong slack webhook token", 1);
}

// Transform to mattermost payload
$data = [
  'text' => $_POST['text'],
  'username' => (isset($_POST['user_name']) && $_POST['user_name']) ? $_POST['user_name'] : 'slack',
  'icon_emoji' => ':monkey_face:',
];
$postData = [
  'payload' => json_encode($data),
];

// Post to matter most
//set POST variables
$url = $config['mattermost_webhook_url'];

//url-ify the data for the POST
$fieldsString = '';
foreach ($postData as $key => $value) {
  $fieldsString .= $key.'='.$value.'&';
}
rtrim($fieldsString, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($postData));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

echo 'Done';
