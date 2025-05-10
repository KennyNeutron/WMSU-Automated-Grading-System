<?php

require_once './classes/database.php';

// Create a new instance of the Database class to get the PDO connection
$database = new Database();
$pdo = $database->connect();  // Assuming `getConnection()` is a method that returns the PDO object

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scores'])) {
    $scores = $_POST['scores'];
    $facultySubId = $_POST['faculty_sub_id'] ?? '';  // Default to empty if not set
    $activePeriod = $_GET['active_period'] ?? 'midterm' ;


    foreach ($scores as $gradesId => $items) {
        foreach ($items as $itemId => $score) {
            // Update the score in the database for each student and item
            $query = "UPDATE component_scores SET score = :score WHERE grades_id = :grades_id AND items_id = :items_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':score' => $score,
                ':grades_id' => $gradesId,
                ':items_id' => $itemId,
            ]);
        }
    }

    // Redirect or show a success message
    header("Location: subject_students.php?faculty_sub_id=$facultySubId&active_period=$activePeriod");

    exit();
}
?>
