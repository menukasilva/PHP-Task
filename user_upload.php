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

//Create users table if not already exists
try{
    $conn->execute("CREATE TABLE IF NOT EXISTS 'users'(
        'id' INT NOT NULL PRIMARY KEY AUTOINCREMENT,
        'name' VARCHAR(255) NOT NULL,
        'surname' VARCHAR(255) NOT NULL,
        'email' VARCHAR(255) NOT NULL,)
        ENGINE = InnoDB;");
}catch(PDOException $e){
    echo "Error Could not create users table." .$e->getMessage() . "\n";
    exit(1);
}

//Prepare insert statement
$insert = $conn->prepare("INSERT INTO users,('name', 'password', 'email') VALUES (?, ?, ?)");

//Iterate through rows and insert into database table
foreach ($rows as $row) {
    //Check if email is valid
    if(!filter_var($row[2], FILTER_VALIDATE_EMAIL)){
        echo "Email is not valid. Skipping the row.\n";
        continue;
    }

    //Capitalize name and surname
    $name = ucfirst(strtolower($row[0]));
    $surname = ucfirst(strtolower($row[1]));
    $email = strtolower($row[2]);

    //Execute insert statement
    try{
        $insert->execute([$name,$surname,$email]);
    }catch(PDOException $e){
        echo "Error inserting row." .$e->getMessage() . "\n";
    }
}

//Close database connection
$conn = null;

echo "Done.\n";

?>
