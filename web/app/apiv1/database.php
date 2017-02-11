<?php
//Database connection Settings

// Yes, there are passwords in the commit history
// No, they won't work anymore :)
// Note to self: sanitise before committing 

$database=array();
$database['name']='eversafe'; // Database Name
$database['username']='eversafe'; // Username - leave blank if MSSQL & windows auth
$database['password']='******'; // Password - leave blank if MSSQL & windows auth
$database['host']='localhost'; // database Host
$database['driver']='mysql'; // database Driver - mysql, sqlsrv etc
// PDO connection string - this is correct for mysql but might need tweaked for
// MSSQL etc
$database['connstring']=$database['driver'].":host=".$database['host'].";dbname=".$database['name'];
