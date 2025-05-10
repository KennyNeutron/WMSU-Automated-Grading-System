<?php
require_once './classes/database.php';

$database = new Database();
$pdo = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scores'])) {
    $scores = $_POST['scores'];
    $facultySubId = $_POST['faculty_sub_id'] ?? '';
    $activePeriod = $_GET['active_period'] ?? 'finalterm';

    foreach ($scores as $gradesId => $items) {
        foreach ($items as $itemId => $score) {
            // Check if the score already exists
            $checkQuery = "SELECT COUNT(*) FROM component_scores WHERE grades_id = :grades_id AND items_id = :items_id";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->execute([
                ':grades_id' => $gradesId,
                ':items_id' => $itemId
            ]);
            $exists = $checkStmt->fetchColumn() > 0;

            if ($exists) {
                // Update existing score
                $updateQuery = "UPDATE component_scores SET score = :score WHERE grades_id = :grades_id AND items_id = :items_id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([
                    ':score' => $score,
                    ':grades_id' => $gradesId,
                    ':items_id' => $itemId,
                ]);
            } else {
                // Insert new score
                $insertQuery = "INSERT INTO component_scores (grades_id, items_id, score) VALUES (:grades_id, :items_id, :score)";
                $insertStmt = $pdo->prepare($insertQuery);
                $insertStmt->execute([
                    ':grades_id' => $gradesId,
                    ':items_id' => $itemId,
                    ':score' => $score,
                ]);
            }
        }
    }

    // Redirect back after saving
    header("Location: subject_students.php?faculty_sub_id=$facultySubId&active_period=$activePeriod");
    exit();
}
?>
