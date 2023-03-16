#!/usr/bin/env php
<?php 

//Define command line arguments
$options = getopt('', array('file:', 'rebuild_table::'));

// Show help message
if (isset($options['help'])) {
    echo "Usage: script.php --file <filename> [--rebuild_table] [--dry_run] [-u <username>] [-p <password>] [-h <host>]\n";
    echo "Options:\n";
    echo "  --file             Path to CSV file (required)\n";
    echo "  --rebuild_table    If set, the 'users' table will be dropped and re-created\n";
    echo "  --dry_run          If set, the script will not insert any data into the database\n";
    echo "  -u <username>      MySQL username\n";
    echo "  -p <password>      MySQL password\n";
    echo "  -h <host>          MySQL host\n";
    exit();
}

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

//Check if file is readable
if (!is_readable($filename)) {
    exit("Error: File '$filename' is not readable.\n");
}

// Get MySQL connection parameters
$servername = isset($options['h']) ? $options['h'] : 'localhost'; 
$username = isset($options['u']) ? $options['u'] : 'root';  
$password = isset($options['p']) ? $options['p'] : 'root';  
$database = 'testing';

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
