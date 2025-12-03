<?php
include "db.php";

// FIX: Only run login logic if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // FIXED: Read 'email' and 'password' to match login.html
    $email    = trim($_POST["email"]);    
    $password = $_POST["password"]; 

    // Validate inputs
    if (empty($email) || empty($password)) {
        die("Please fill in all required fields.");
    }

    // FIX: Query by email, not username and role
    $sql = "SELECT id, password_hash, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
         die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            
            // Login successful
            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"]; 

            // Redirect based on the role stored in the database
            if ($user["role"] === "admin") {
                // Assuming admin_dashboard.php is in the root
                header("Location: admin_dashboard.php");
            } else {
                // CORRECT PATH: Must include the directory name USERS/
                header("Location: USERS/customer_home.php"); 
            }
            exit;
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "User not found with that email address!";
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect if the file is accessed directly
    header("Location: login.html");
    exit;
}
?>