<?php
// this is the base class inherited by all controllers
// there's no functionality yet, but it's included for completeness
// & for later additions.
class BaseController
{
  public function badRequest($message){
    die(header("HTTP/1.1 400 Bad Request").$message);
  }
}
