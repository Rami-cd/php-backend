<?php
require_once "connection.php";
require_once '../vendor/autoload.php';  // Include Composer autoload for JWT

use \Firebase\JWT\JWT;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Read raw POST data (for JSON)
  $data = json_decode(file_get_contents("php://input"), true);

  $user_username = $data['username'];
  $user_email = $data['email'];
  $user_password = $data['password'];

  // Simple validation
  if (empty($user_username) || empty($user_email) || empty($user_password)) {
      echo json_encode(["status" => "error", "message" => "All fields are required"]);
      exit;
  }

  // Hash the password before saving
  $hashed_password = password_hash($user_password, PASSWORD_BCRYPT);

  // SQL query to insert user data
  $query = "INSERT INTO users (username, email, password) VALUES ('$user_username', '$user_email', '$hashed_password')";

  if ($conn->query($query) === TRUE) {
      // Generate JWT token
      $secret_key = "your_secret_key"; // Use a secret key for encoding
      $issued_at = time();
      $expiration_time = $issued_at + 3600;  // 1 hour from now
      $payload = array(
          "username" => $user_username,
          "email" => $user_email,
          "iat" => $issued_at,
          "exp" => $expiration_time
      );

      // Encode the JWT
      $jwt = JWT::encode($payload, $secret_key, 'HS256');

      // Get the last inserted user's username or email for updating the token
      $update_query = "UPDATE users SET token = '$jwt' WHERE email = '$user_email'";  // Use 'email' as the unique identifier

      if ($conn->query($update_query) === TRUE) {
          echo json_encode([
              "status" => "success",
              "message" => "User registered successfully",
              "token" => $jwt
          ]);
      } else {
          echo json_encode(["status" => "error", "message" => "Error updating token: " . $conn->error]);
      }
  } else {
      echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
  }
}

$conn->close();
?>
