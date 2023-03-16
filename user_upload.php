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





?>
