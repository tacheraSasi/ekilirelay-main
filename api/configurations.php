<?php 
#The default template function
function defaultTemplate(string $message):string{
    $template = '
        <!DOCTYPE html>
        <html lang="en"><head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email Template</title>
            <style>
                body {
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                }

                pre {
                    font-family: inherit;
                }

                small {
                    opacity: .7;
                }

                a {
                    color: rgb(4, 189, 96);
                }
            </style>
        </head>
        <body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
            <pre style="font-family: inherit;">
                '.$message.'
            </pre>
            <small style="opacity: .7;">Sent with <a href="http://relay.ekilie.com" target="_blank" style="color:rgb(4, 189, 96)">ekiliRelay</a></small>
        </body>
        </html>
    ';
    return $template;

}