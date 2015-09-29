<?php
// this object holds all data relevant to the HTTP request
class Request {
  // define public vars
    public $url_elements;
    public $verb;
    public $parameters;
    // Constructor:
    //   - defines the HTTP verb & creates the url_elements array
    //   - gets the request parameters - get variables & request body
    //   - gets the request format - JSON, Form etc
    public function __construct() {
        // get the HTTP 'verb' - GET, POST, PUT or DELETE
        $this->verb = $_SERVER['REQUEST_METHOD'];
        // create an array of each of the path elements (relatively self explanatory)
        $this->url_elements = explode('/', $_SERVER['PATH_INFO']);
        // the complete path
        $this->url_complete = $_SERVER['PATH_INFO'];
        // this function is explained below
        $this->parseIncomingParams();
        // initialise json as default format
        // if the format is explicitly defined, use it instead.
        if(!isset($this->format)) {
                    $this->format = 'json';
        }
        return true;
    }
    // this function implements the last 2 bullets of the constructor - firstly
    // parsing the get params and then the request body. The request format is
    // detected first, so that the body can be correctly decoded.
    public function parseIncomingParams() {
        $parameters = array();

        // pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
        }

        // request body - this will override the GET vars if present
        // get input first
        // this is the raw request body with no decoding applied
        // as $_POST won't work for JSON
        $body = file_get_contents("php://input");
        // make sure content_type is defined
        $content_type = false;
        // check if content type header is set
        if(isset($_SERVER['CONTENT_TYPE'])) {
            // use this to decode the request
            $content_type = $_SERVER['CONTENT_TYPE'];
        }
        // if not (eg. in the case of a GET request), we will use accepts header
        // (which should always be set)
        else {
          $content_type = explode(",",$_SERVER['HTTP_ACCEPT'])[0];
        }
        switch($content_type) {
            case "application/json":
                // json decode into object
                $body_params = json_decode($body);
                // if the request isn't blank and is valid
                if($body_params) {
                    // recursively create array from object
                    // this is semi-redundant as json_decode can do this
                    // but this way prevents a few possible problems
                    foreach($body_params as $param_name => $param_value) {
                        $parameters[$param_name] = $param_value;
                    }
                }
                // define the request format
                $this->format = "json";
                break;
            case "application/x-www-form-urlencoded":
                // decode encoded form
                // this functionality is the same as using PHP's $_POST
                // but as we've already accessed php://input, $_POST won't work.
                parse_str($body, $postvars);
                // recurse into parameters array
                foreach($postvars as $field => $value) {
                    $parameters[$field] = $value;

                }
                // define the request format
                $this->format = "html";
                break;
                // if accept header is text/html
                case "text/html":
                $this->format = "html";
                break;
            default:
                // other formats could be parsed here - XML for example.
                break;
        }
        // define the object's parameters array
        $this->parameters = $parameters;
    }
}
