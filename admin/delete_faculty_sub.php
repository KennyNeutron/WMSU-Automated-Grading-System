<?php
require_once '../classes/faculty_subs.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);

    $faculty_sub_id = $input['faculty_sub_id'] ?? null;

    if ($faculty_sub_id === null) {
        error_log("Sched ID is null in backend.");
        echo json_encode(['success' => false, 'message' => 'Invalid request. Sched ID missing.']);
        exit;
    }
    if ($faculty_sub_id) {
        $faculty_sub = new Faculty_Subjects();
        $result = $faculty_sub->delete($faculty_sub_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => '']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete schedule.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid request. Sched ID: $faculty_sub_id"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid HTTP method.']);
}

?>