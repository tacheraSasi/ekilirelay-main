<?php 
#The default template function
function defaultTemplate(string $message):string{
    $template = '
        <!DOCTYPE html>
        <html lang="en">
        <body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
            <pre style="font-family: inherit;">
                '.$message.'
            </pre>
            <small style="opacity: .7;">Sent with <a href="http://relay.ekilie.com" style="color:rgb(4, 189, 96)">ekiliRelay</a></small>
        </body>
        </html>
    ';
    return $template;

}