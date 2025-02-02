<?php
require_once "connection.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Read raw POST data (for JSON)
    $data = json_decode(file_get_contents("php://input"), true);

    $user_email = $data['email'];
    $user_password = $data['password'];

    // Simple validation
    if (empty($user_email) || empty($user_password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        exit;
    }

    // SQL query to fetch user data based on email
    $query = "SELECT * FROM users WHERE email = '$user_email'";
    $result = $conn->query($query);

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($user_password, $user['password'])) {
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No user found with this email"]);
    }
}

$conn->close();
?>
