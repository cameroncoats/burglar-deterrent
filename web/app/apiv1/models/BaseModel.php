<?php
// this is the base class that all models will inherit
// any functions common to all models should be defined here
class BaseModel
{
    // Class constructor -
    // Currently only defines the database connection for later use in
    // other models.
    public function __construct()
    {
      global $database;
      $db = new PDO($database['connstring'],$database['username'],$database['password']) or die("Database Problemo");
        $this->_db = $db;
        return true;
    }
}
