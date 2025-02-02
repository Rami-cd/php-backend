<?php
require_once "connection.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Read raw POST data (for JSON)
  $data = json_decode(file_get_contents("php://input"), true);

  // Now, access the data like this:
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
      echo json_encode(["status" => "success", "message" => "User registered successfully"]);
  } else {
      echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
  }
}

$conn->close();