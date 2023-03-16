#!/usr/bin/env php
<?php 

//Define command line arguments
$options = getopt('', array('file:', 'rebuild_table::'));

//Check options
if (!isset($options['file'])){
    exit("Error: Missing required --file \n");
}

//Get file path
$filename = $options['file'];

//Check if file exists
if (!file_exists($filename)){
    exit("Error:File does not exist\n");
}

//Connect to database
$servername = $options['servername'];
$username = $options['username'];
$password = $options['password'];
$database = $options['database'];

try{
    $conn = new PDO("mysql:host=$servername; dbname=$database",  $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    echo "Connected to database";
}catch(PDOException $e){
    echo "Error connecting to database.";
    exit();
}

?>
