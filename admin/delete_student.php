<?php
require_once '../classes/students.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $student_data_id = $input['student_data_id'] ?? null;

    if ($student_data_id === null) {
        error_log("Student ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Student ID missing.']);
        exit;
    }
    if ($student_data_id) {
        $student = new Students();
        $result = $student->delete($student_data_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Student ID: $student_data_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>