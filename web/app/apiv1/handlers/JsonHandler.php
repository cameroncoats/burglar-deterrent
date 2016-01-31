<?php
// Handler class for JSON output format
class JsonHandler extends BaseHandler {
    // all handlers should have render function as a minimum
    public function render($content) {
        // set the content type header
        header('Content-Type: application/json; charset=utf8');
        // encode the content array as JSON & echo
        echo json_encode($content);
        // done
        return true;
    }
}
