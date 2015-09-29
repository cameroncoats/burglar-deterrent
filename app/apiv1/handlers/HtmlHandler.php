<?php
// this class renders the output as HTML, useful when testing the api with a browser
class HtmlHandler extends BaseHandler
{
    public function render($content)
    {
        header('Content-Type: text/html; charset=utf8');
        $this->layoutStart();
        if (is_array($content)) {
            $this->printArray($content);
        } else {
            $this->printUrlOrString($content);
        }
        $this->layoutStop();

        return true;
    }
    /**
     * Recursively render an array to an HTML list.
     *
     * @param array $content data to be rendered
     */
    protected function printArray(array $content)
    {
        echo "<ul>\n";
        // field name
        foreach ($content as $field => $value) {
            echo '<li><strong>'.$field.':</strong> ';
            if (is_array($value)) {
                // recurse
                $this->printArray($value);
            } else {
                // value, with hyperlinked hyperlinks
                $this->printUrlOrString($value);
            }
            echo "</li>\n";
        }
        echo "</ul>\n";
    }
    /**
     * Renders the passed value, either raw or as a link (if prepended by http
     * or https).
     *
     * @param string $value
     */
    protected function printUrlOrString($value)
    {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
        if ((strpos($value, 'http://') === 0) || (strpos($value, 'https://') === 0)) {
            echo '<a href="'.$value.'">'.$value.'</a>';
        } else {
            echo $value;
        }
    }
    /**
     * Render start of HTML page.
     */
    protected function layoutStart()
    {
        echo <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>API v1</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.5/paper/bootstrap.min.css" rel="stylesheet" integrity="sha256-hMIwZV8FylgKjXnmRI2YY0HLnozYr7Cuo1JvRtzmPWs= sha512-k+wW4K+gHODPy/0gaAMUNmCItIunOZ+PeLW7iZwkDZH/wMaTrSJTt7zK6TGy6p+rnDBghAxdvu1LX2Ohg0ypDw==" crossorigin="anonymous">
</head>
<body>
<div class="col-xs-10 col-xs-offset-1">
<div class="jumbotron">
<div class="row">
<div class="col-xs-10 col-xs-offset-1">

<h1>GWA-RM API v1<br><small>
EOT
.$_SERVER['PATH_INFO'].
<<<EOT
</small></h1></div></div></div>
<div class="well">
<div class="row">
<div class="col-xs-8 col-xs-offset-4">

EOT;
    }
    /**
     * Render end of HTML page.
     */
    protected function layoutStop()
    {
        echo <<<EOT
        </div></div></div></div>
</body>
</html>
EOT;
    }
}
