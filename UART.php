<?php
// Database connection details
$db_host = "localhost";
$db_username = "root";
$db_password = "password";
$db_name = "drink_dispenser";

// Set the serial port path and baud rate
$serial_port = "/dev/ttyAMA10";  // for GPIO pins
$baud_rate = "2400";

// Config the serial port once
exec("stty -F $serial_port $baud_rate cs8 -cstopb -parenb");

// Function to fetch data from the database
function fetch_data($pin, $rfid) {
    global $db_host, $db_username, $db_password, $db_name;

    // Create database connection
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Prepare and execute SQL query
    $stmt = $mysqli->prepare("SELECT first_name FROM users WHERE pin = ? AND rfid = ?");
    $stmt->bind_param("ss", $pin, $rfid);
    $stmt->execute();
    $stmt->bind_result($first_name);
    $stmt->fetch();
    $stmt->close();
    $mysqli->close();

    return $first_name;
}

// Open the serial port
$serial = fopen($serial_port, "w+");
if (!$serial) {
    echo json_encode(['status' => 'error', 'message' => 'Unable to open serial port']);
    exit;
}

// Read input command from the serial port
$input_command = fread($serial, 128);  // Adjust the buffer size as needed
$input_command = strtoupper(trim($input_command));

// Define a list of commands and their corresponding actions
$commands = [
    'FETCH_DATA' => function($params) {
        $pin = $params[0];
        $rfid = $params[1];
        return fetch_data($pin, $rfid);
    }
];

// Parse the input command
$command_parts = explode(" ", $input_command);
$command = $command_parts[0];
$params = array_slice($command_parts, 1);

if (array_key_exists($command, $commands)) {
    // Execute the command
    $response = $commands[$command]($params);
    fwrite($serial, $response);
} else {
    fwrite($serial, "INVALID COMMAND");
}

fclose($serial);
?>
