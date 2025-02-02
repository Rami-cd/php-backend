<?php
require_once "connection.php";
require_once '../vendor/autoload.php';  // Include Composer autoload for JWT

use \Firebase\JWT\JWT;

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
            // Password is correct, generate JWT token
            $secret_key = "your_secret_key";  // Use the same secret key as in registration
            $issued_at = time();
            $expiration_time = $issued_at + 3600;  // 1 hour from now
            $payload = array(
                "email" => $user_email,
                "username" => $user['username'],
                "iat" => $issued_at,
                "exp" => $expiration_time
            );

            // Encode the JWT
            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "token" => $jwt  // Send the JWT token in the response
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No user found with this email"]);
    }
}

$conn->close();
?>
