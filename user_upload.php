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
$servername = $options['localhost'];
$username = $options['root'];
$password = $options['root'];
$database = $options['testing'];

//Open database connection with mysqli
$mysqli = new mysqli($servername, $username, $password, $database);

//Check for errors connecting to MySQL server
if ($mysqli->connect_error()) {
    exit("Error connecting to MySQL server" .$mysqli->connect_error() . "\n");
}

//Rebuild table if requested
if (isset($options['rebuild_table']) && $options['rebuild_table'] == 'true'){
    //Drop the users table
    $sql = "DROP TABLE IF EXISTS users";
    if (!$mysqli->query($sql)) {
        exit("Error dropping table: " . $mysqli->error . "\n");
}

//Create the users table
$sql = "CREATE TABLE users (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        surname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE
        )";
    if (!$mysqli->query($sql)) {
    exit("Error creating table: " . $mysqli->error . "\n");
    }
}

// Read CSV file
if (($handle = fopen($filename, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle);

    while (($data = fgetcsv($handle)) !== FALSE) {
        // Capitalize name and surname
        $name = ucwords(strtolower($data[0]));
        $surname = ucwords(strtolower($data[1]));

        // Lowercase email
        $email = strtolower($data[2]);

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Error: Invalid email address - $email\n";
            continue;
        }

        // Insert record into database
        $sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sss', $name, $surname, $email);

        if (!$stmt->execute()) {
            echo "Error inserting record - " . $mysqli->error . "\n";
        }
    }

    fclose($handle);
}

//Close database connection
$mysqli->close();

echo "Done.\n";

?>
