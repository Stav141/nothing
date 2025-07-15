<?php http_response_code(401); ?><html>
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
            <p>You do not have the proper credentials to be verified.</p>
        </div>
    </body>
</html>
