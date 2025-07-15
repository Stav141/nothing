<?php

if (!isset($_GET["token"]) || $_GET["token"] == '' || strlen($_GET["token"]) != 32)
{
    header("Location: /discord-auth/unauthorized/");
    die();
}

$host = '192.168.1.87';
$port = 65432;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    // echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    http_response_code(500);
    require "failure/index.php";
    die();
}

$result = socket_connect($socket, $host, $port);
if ($result === false) {
    // echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
    http_response_code(500);
    require "failure/index.php";
    die();
}

$jsonData = [
    'action' => 'start',
    'ip' => $_SERVER['HTTP_CF_CONNECTING_IP'],
    'token' => $_GET["token"],
    'time' => time()
];

$message = json_encode($jsonData) . "\n";

socket_write($socket, $message, strlen($message));

$response = '';
while ($buf = socket_read($socket, 2048)) {
    $response .= $buf;
}

$message = "";

try {
    $jsonResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    // if the server says that this token doesn't exist or we shouldn't be here
    if ($jsonResponse["authorized"] == false && $jsonResponse["bad_token"] == true)
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
    $message = "You are verifying for the Discord account <b>" . $jsonResponse["discord_displayname"] . "</b> (aka <b>" . $jsonResponse["discord_username"] . "</b>) to enter <b>" . $jsonResponse["guild"] . "</b>.<br />Please complete the captcha below and click on the 'verify' button to continue.";
} catch (JsonException $e) {
    $message = "Error decoding JSON: " . $e->getMessage() . "\n";
}

socket_close($socket);

?><html>
    <head>
        <title>Discord Server Authorization</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script src="https://www.hCaptcha.com/1/api.js" async defer></script>
        <link rel="stylesheet" href="/style.css" type="text/css">
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector("form").addEventListener("submit", function(event) {
                var hcaptchaVal = document.querySelector('[name=h-captcha-response]').value;
                if (hcaptchaVal === "") {
                    event.preventDefault();
                    alert("Finish the captcha before clicking on the 'verify' button.");
                }
            });
        });

        </script>
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
            <?php if (!empty($message)): ?><p><?php echo $message; ?></p><?php endif; ?>
            <form action="/discord-auth/submit/" method="POST">
                <input type="hidden" name="token" value="<?php echo $_GET["token"]; ?>" />
                <div class="h-captcha" data-sitekey="909ac966-0d88-4900-838a-9d9e129eec7e"></div>
                <br />
                <input type="submit" name="submit" value="Verify" />
            </form>
        </div>
    </body>
</html>

