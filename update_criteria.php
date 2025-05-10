<?php
session_start();

// Check user authentication
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
    exit();
}

// Include necessary files
include_once("./classes/database.php");

// Create an instance of the Database class
$database = new Database();
// Establish the database connection
$connection = $database->connect();

// Check if the connection is successful
if ($connection) {
    try {
        // Prepare update statement
        $stmt = $connection->prepare("UPDATE subject_setting SET criteria_name = ?, criteria_weight = ? WHERE id = ?");
        $stmt->bindParam(1, $_POST['criteria_name']);
        $stmt->bindParam(2, $_POST['criteria_weight']);
        $stmt->bindParam(3, $_POST['criteria_id']);
        $stmt->execute();
        header('Location: subject_setting.php');
        exit();
    } catch (Exception $e) {
        // Handle exception
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
} else {
    // Handle connection error
    echo "<p>Failed to connect to the database.</p>";
}
?>
