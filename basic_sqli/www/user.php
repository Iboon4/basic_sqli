<?php

error_reporting(E_ALL);
ini_set('display_errors', true);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("connection.php");

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect post data
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT id, username, email FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $input_username, $input_password);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Fetch data
        $row = $result->fetch_assoc();
        $response["message"] = "Hello " . $row['username'] . ". Successful Authentication";
    } else {
        $response["error"] = "User not found";
    }

    // Cerrar la sentencia preparada
    $stmt->close();
} else {
    $response["error"] = "Invalid request method";
}

// Cerrar la conexión
$conn->close();

echo json_encode($response);

?>
