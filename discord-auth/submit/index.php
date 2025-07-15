<?php

if (!isset($_POST['h-captcha-response']))
{
    header("Location: /discord-auth/unauthorized/");
    die();
}

if (empty($_POST['h-captcha-response']))
{
    header("Location: /discord-auth/unauthorized/");
    die();
}

$host = '192.168.1.87';
$port = 65432;

$jsonData = [
    'action' => "finish",
    'response' => $_POST["h-captcha-response"],
    'ip' => $_SERVER["HTTP_CF_CONNECTING_IP"],
    'token' => $_POST["token"],
    'time' => time()
];

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    // echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

$result = socket_connect($socket, $host, $port);
if ($result === false) {
    // echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}

$message = json_encode($jsonData) . "\n";

socket_write($socket, $message, strlen($message));

$response = '';
while ($buf = socket_read($socket, 2048)) {
    $response .= $buf;
}

$r_message = "";

try {
    $jsonResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    // if the server says that this token doesn't exist or we shouldn't be here
    if ($jsonResponse['authorized'] == false && $jsonResponse["bad_token"] == true)
    {
        // header("Location: /discord-auth/bad-token/");
        http_response_code(400);
        require "bad-token/index.php";
        die();
    }
    else if ($jsonResponse["authorized"] == false)
    {
        header("Location: /discord-auth/unauthorized/");
        die();
    }
    $invalid_response = $jsonResponse["try_again"] ? "please close the webpage and try again." : "please close the webpage and contact <b>" . $jsonResponse["guild"] . "</b> administrators.";
    $r_message = $jsonResponse["valid"] ? "You have been verified and can now close this webpage." : "You could not be verified at this time; " . $invalid_response;
} catch (JsonException $e) {
    // for( $i = 0 ; $i < strlen($response) ; $i++ ) {
    //     $r_message .= 'pos: ' . $i . ' | ord: ' . ord( $response[$i] ) . ' | char: ' . $response[$i] . '<br />';
    // }
    // $r_message .= "\nError decoding JSON: " . $e->getMessage() . "\n<br />\n" . $response;
    $r_message = "There was an error while loading the verification result. Please try generating a new captcha and notifying the administrator.";
}

socket_close($socket);

?>

<html>
    <head>
        <title>Discord Server Authorization</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script src="https://www.hCaptcha.com/1/api.js" async defer></script>
        <link rel="stylesheet" href="/style.css" type="text/css">
        <style>
        .content {
          position: absolute;
          left: 50%;
          top: 50%;
          -webkit-transform: translate(-50%, -50%);
          transform: translate(-50%, -50%);
        }
        </style>
    </head>
    <body>
        <div class="content">
            <?php if (!empty($r_message)): ?><p><?php echo $r_message; ?></p><?php endif; ?>
        </div>
    </body>
</html>
