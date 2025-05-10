<?php
require_once '../classes/grades.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $grades_id = $input['grades_id'] ?? null;

    if ($grades_id === null) {
        error_log("Grades ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Grades ID missing.']);
        exit;
    }
    if ($grades_id) {
        $student = new Grades();
        $result = $student->delete($grades_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete grades.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Grades ID: $grades_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>