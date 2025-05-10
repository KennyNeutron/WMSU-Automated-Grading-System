<?php
session_start();

if (!isset($_SESSION['user_role']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] != 1)) {
    header('location: ./login.php');
    exit(); // Add an exit statement after redirection
}

if(isset($_GET["studentid"])) {
    // Include database connection
    include_once("./classes/database.php");

    // Create an instance of the Database class
    $database = new Database();
    // Establish the database connection
    $connection = $database->connect();

    // Get the student ID from the URL parameter
    $student_id = $_GET["studentid"];

    if ($connection) {
        try {
            // Prepare the delete statement
            $stmt = $connection->prepare("DELETE FROM student WHERE studentid = ?");
            if ($stmt) {
                // Bind the parameter and execute the statement
                $stmt->execute([$student_id]);

                // Check if any rows were affected
                if ($stmt->rowCount() > 0) {
                    // Redirect back to students.php after successful deletion
                    header("Location: ./students.php");
                    exit(); // Make sure to exit after redirection to prevent further execution
                } else {
                    // If no rows were affected, show an error message
                    echo "<p>No record found with the provided student ID.</p>";
                }
            } else {
                // Handle statement preparation error
                echo "<p>Error preparing statement: " . $connection->errorInfo()[2] . "</p>";
            }
        } catch (Exception $e) {
            // Handle exception
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        // Handle connection error
        echo "<p>Failed to connect to the database.</p>";
    }
} else {
    // If the student ID parameter is not provided in the URL, show an error message
    echo "<p>Student ID parameter is missing.</p>";
}
?>
