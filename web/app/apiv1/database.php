<?php
//Database connection Settings
$database=array();
$database['name']='eversafe'; // Database Name
$database['username']='eversafe'; // Username - leave blank if MSSQL & windows auth
$database['password']='safeever'; // Password - leave blank if MSSQL & windows auth
$database['host']='localhost'; // database Host
$database['driver']='mysql'; // database Driver - mysql, sqlsrv etc
// PDO connection string - this is correct for mysql but might need tweaked for
// MSSQL etc
$database['connstring']=$database['driver'].":host=".$database['host'].";dbname=".$database['name'];
