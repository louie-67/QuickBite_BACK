<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $plain_password = $_POST["password"];
    
    // Hardcode role to 'customer'
    $role = "customer"; 
    $username = $email; // Use email for the username field as well

    if (empty($email) || empty($plain_password)) {
        die("Please fill in all required fields.");
    }

    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // 1. Check if the email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Error: This email address is already registered. Please login.'); window.location='login.html';</script>";
        exit;
    }
    $check_stmt->close();


    // 2. Insert the new user (username, email, password_hash, role)
    $insert_sql = "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);

    if ($insert_stmt === false) {
         die('MySQL prepare error: ' . $conn->error);
    }

    $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

    if ($insert_stmt->execute()) {
        echo "<script>alert('Account created successfully! You are registered as a $role.'); window.location='login.html';</script>";
    } else {
        echo "Error: Could not create account. " . $insert_stmt->error;
    }

    $insert_stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit;
}
?>